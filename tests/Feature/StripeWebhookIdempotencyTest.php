<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StripeWebhookIdempotencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_same_stripe_event_is_recorded_only_once(): void
    {
        config()->set('services.stripe.webhook_secret', 'whsec_test');
        $payload = json_encode([
            'id' => 'evt_test_duplicate',
            'object' => 'event',
            'type' => 'customer.created',
            'data' => ['object' => ['id' => 'cus_test']],
        ], JSON_THROW_ON_ERROR);
        $timestamp = time();
        $signature = hash_hmac('sha256', $timestamp.'.'.$payload, 'whsec_test');
        $headers = [
            'HTTP_STRIPE_SIGNATURE' => 't='.$timestamp.',v1='.$signature,
            'CONTENT_TYPE' => 'application/json',
        ];

        $this->call('POST', route('webhook.stripe'), [], [], [], $headers, $payload)
            ->assertOk()
            ->assertJson(['status' => 'success']);

        $this->call('POST', route('webhook.stripe'), [], [], [], $headers, $payload)
            ->assertOk()
            ->assertJson(['status' => 'already_processed']);

        $this->assertDatabaseHas('stripe_webhook_events', [
            'id' => 'evt_test_duplicate',
            'status' => 'completed',
            'attempts' => 1,
        ]);
    }
}
