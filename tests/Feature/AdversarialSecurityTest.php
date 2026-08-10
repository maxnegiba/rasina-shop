<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureAdminMfa;
use App\Models\Order;
use App\Models\User;
use App\Services\CheckoutPaymentIntentService;
use App\Services\SafeHtmlSanitizer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Stripe\PaymentIntent;
use Tests\TestCase;

class AdversarialSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_xss_payloads_are_neutralized_by_html_sanitizer(): void
    {
        $html = <<<'HTML'
<p onclick="alert(1)">safe</p>
<script>alert(document.domain)</script>
<a href="java\nscript:alert(1)" target="_blank">bad</a>
<img src="data:text/html;base64,PHNjcmlwdD5hbGVydCgxKTwvc2NyaXB0Pg==" onerror="alert(1)">
<svg><script>alert(1)</script></svg>
HTML;

        $clean = app(SafeHtmlSanitizer::class)->sanitize($html);

        $this->assertStringContainsString('<p>safe</p>', $clean);
        $this->assertStringNotContainsString('<script', strtolower($clean));
        $this->assertStringNotContainsString('onclick=', strtolower($clean));
        $this->assertStringNotContainsString('onerror=', strtolower($clean));
        $this->assertStringNotContainsString('javascript:', strtolower($clean));
        $this->assertStringNotContainsString('data:text/html', strtolower($clean));
        $this->assertStringNotContainsString('<svg', strtolower($clean));
    }

    public function test_stolen_or_expired_admin_mfa_session_cannot_cross_admin_boundary(): void
    {
        Route::middleware(EnsureAdminMfa::class)
            ->get('/_adversarial-admin-boundary', fn () => response('admin-ok'));

        $admin = User::factory()->create(['is_admin' => true]);
        $otherAdmin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->withSession([
                'admin_mfa_verified_at' => time(),
                'admin_mfa_user_id' => $otherAdmin->id,
            ])
            ->get('/_adversarial-admin-boundary')
            ->assertRedirect(route('admin.mfa.challenge'));

        $this->actingAs($admin)
            ->withSession([
                'admin_mfa_verified_at' => time() - ((int) config('security.admin_mfa.session_seconds', 14400) + 1),
                'admin_mfa_user_id' => $admin->id,
            ])
            ->get('/_adversarial-admin-boundary')
            ->assertRedirect(route('admin.mfa.challenge'));
    }

    public function test_non_admin_user_cannot_reach_mfa_protected_admin_boundary(): void
    {
        Route::middleware(EnsureAdminMfa::class)
            ->get('/_adversarial-admin-role', fn () => response('admin-ok'));

        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->withSession([
                'admin_mfa_verified_at' => time(),
                'admin_mfa_user_id' => $user->id,
            ])
            ->get('/_adversarial-admin-role')
            ->assertForbidden();
    }

    public function test_forged_stripe_webhook_is_rejected_without_recording_event(): void
    {
        config(['services.stripe.webhook_secret' => 'whsec_adversarial_test']);

        $payload = json_encode([
            'id' => 'evt_forged',
            'object' => 'event',
            'type' => 'payment_intent.succeeded',
            'data' => ['object' => ['id' => 'pi_forged']],
        ], JSON_THROW_ON_ERROR);

        $this->call('POST', route('webhook.stripe'), [], [], [], [
            'HTTP_STRIPE_SIGNATURE' => 't='.time().',v1=definitely-invalid',
            'CONTENT_TYPE' => 'application/json',
        ], $payload)->assertStatus(400);

        $this->assertSame(0, DB::table('stripe_webhook_events')->count());
    }

    public function test_replayed_valid_stripe_event_is_idempotent(): void
    {
        $secret = 'whsec_replay_test';
        config(['services.stripe.webhook_secret' => $secret]);

        $payload = json_encode([
            'id' => 'evt_replay_001',
            'object' => 'event',
            'type' => 'mtd.security.noop',
            'data' => ['object' => ['id' => 'noop_001']],
        ], JSON_THROW_ON_ERROR);

        $timestamp = time();
        $signature = hash_hmac('sha256', $timestamp.'.'.$payload, $secret);
        $header = 't='.$timestamp.',v1='.$signature;

        $this->call('POST', route('webhook.stripe'), [], [], [], [
            'HTTP_STRIPE_SIGNATURE' => $header,
            'CONTENT_TYPE' => 'application/json',
        ], $payload)->assertOk()->assertJson(['status' => 'success']);

        $this->call('POST', route('webhook.stripe'), [], [], [], [
            'HTTP_STRIPE_SIGNATURE' => $header,
            'CONTENT_TYPE' => 'application/json',
        ], $payload)->assertOk()->assertJson(['status' => 'already_processed']);

        $this->assertSame(1, DB::table('stripe_webhook_events')->where('id', 'evt_replay_001')->count());
    }

    public function test_checkout_session_token_tampering_cannot_create_payment_intent(): void
    {
        $sessionOrder = $this->pendingOrder();
        $attackerOrder = $this->pendingOrder();

        $service = Mockery::mock(CheckoutPaymentIntentService::class);
        $service->shouldNotReceive('prepare');
        $this->app->instance(CheckoutPaymentIntentService::class, $service);

        $this->withSession(['checkout_order_token' => $sessionOrder->public_token])
            ->postJson(route('checkout.accept-terms'), [
                'order_token' => $attackerOrder->public_token,
                'accept_terms' => true,
                'acknowledge_privacy' => true,
                'amount' => 1,
                'total_amount' => 1,
            ])
            ->assertForbidden();

        $this->assertNull($attackerOrder->fresh()->terms_accepted_at);
        $this->assertNull($attackerOrder->fresh()->privacy_acknowledged_at);
    }

    public function test_client_supplied_amount_cannot_change_server_side_order_total(): void
    {
        $order = $this->pendingOrder();
        $intent = PaymentIntent::constructFrom([
            'id' => 'pi_amount_tamper_test',
            'client_secret' => 'pi_amount_tamper_secret',
        ]);

        $service = Mockery::mock(CheckoutPaymentIntentService::class);
        $service->shouldReceive('prepare')
            ->once()
            ->with(Mockery::on(function (Order $candidate) use ($order): bool {
                $candidate->refresh();

                return $candidate->is($order)
                    && (float) $candidate->total_amount === 100.0;
            }))
            ->andReturn($intent);
        $this->app->instance(CheckoutPaymentIntentService::class, $service);

        $this->withSession(['checkout_order_token' => $order->public_token])
            ->postJson(route('checkout.accept-terms'), [
                'order_token' => $order->public_token,
                'accept_terms' => true,
                'acknowledge_privacy' => true,
                'amount' => 1,
                'total_amount' => 1,
                'price' => 0.01,
            ])
            ->assertOk()
            ->assertJson(['client_secret' => 'pi_amount_tamper_secret']);

        $this->assertSame(100.0, (float) $order->fresh()->total_amount);
    }

    public function test_csrf_exception_is_scoped_only_to_stripe_webhook(): void
    {
        $bootstrap = file_get_contents(base_path('bootstrap/app.php'));

        $this->assertIsString($bootstrap);
        $this->assertStringContainsString("'webhook/stripe'", $bootstrap);
        $this->assertStringNotContainsString("'checkout/", $bootstrap);
        $this->assertStringNotContainsString("'cos/", $bootstrap);
        $this->assertStringNotContainsString("'admin", $bootstrap);
        $this->assertStringNotContainsString("'*'", $bootstrap);
    }

    public function test_unsigned_proforma_idor_attempt_is_rejected(): void
    {
        $order = $this->pendingOrder();

        $this->get('/proforma/'.$order->public_token)
            ->assertForbidden();
    }

    public function test_non_image_payload_disguised_as_jpeg_is_rejected(): void
    {
        Storage::fake('local');
        Storage::fake('public');

        $fake = UploadedFile::fake()->createWithContent(
            'reference.jpg',
            "<?php echo 'not-an-image'; ?>\n<script>alert(1)</script>",
        );

        $this->from('/contact')
            ->post(route('custom-request.store'), [
                'customer_name' => 'Attacker',
                'customer_email' => 'attack@example.com',
                'reference_image' => $fake,
            ])
            ->assertRedirect('/contact')
            ->assertSessionHasErrors('reference_image');

        $this->assertDatabaseMissing('custom_requests', [
            'customer_email' => 'attack@example.com',
        ]);
    }

    private function pendingOrder(): Order
    {
        return Order::create([
            'order_number' => 'MTD-ADV-'.strtoupper(bin2hex(random_bytes(3))),
            'total_amount' => 100,
            'payment_status' => 'pending',
            'shipping_status' => 'processing',
            'customer_details' => [],
            'stock_reserved_at' => now(),
        ]);
    }
}
