<?php

namespace Tests\Feature;

use App\Filament\Resources\CustomRequestResource;
use App\Models\CustomRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CustomRequestWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_request_with_only_required_fields_does_not_fail_on_missing_optional_fields(): void
    {
        $this->post(route('custom-request.store'), [
            'customer_name' => 'Client Test',
            'customer_email' => 'client@example.com',
        ])->assertRedirect()->assertSessionHas('success');

        $this->assertDatabaseHas('custom_requests', [
            'customer_name' => 'Client Test',
            'customer_email' => 'client@example.com',
            'product_id' => null,
            'customer_phone' => null,
            'status' => 'new',
        ]);
    }

    public function test_reference_image_is_reencoded_and_stored_only_on_private_disk(): void
    {
        if (! function_exists('imagecreatetruecolor')) {
            $this->markTestSkipped('GD is required for secure image normalization.');
        }

        Storage::fake('local');
        Storage::fake('public');

        $image = UploadedFile::fake()->image('atelier.jpg', 1200, 800)->size(400);

        $this->post(route('custom-request.store'), [
            'customer_name' => 'Client Imagine',
            'customer_email' => 'imagine@example.com',
            'reference_image' => $image,
        ])->assertRedirect()->assertSessionHas('success');

        $request = CustomRequest::query()->where('customer_email', 'imagine@example.com')->firstOrFail();

        $this->assertNotNull($request->reference_image_path);
        $this->assertStringStartsWith('custom_requests/', $request->reference_image_path);
        Storage::disk('local')->assertExists($request->reference_image_path);
        Storage::disk('public')->assertMissing($request->reference_image_path);

        $stored = Storage::disk('local')->get($request->reference_image_path);
        $this->assertNotFalse(@imagecreatefromstring($stored));
        $this->assertNotSame($image->getClientOriginalName(), basename($request->reference_image_path));
    }

    public function test_reference_image_rejects_excessive_dimensions(): void
    {
        if (! function_exists('imagecreatetruecolor')) {
            $this->markTestSkipped('GD is required for image fixture generation.');
        }

        Storage::fake('local');

        $image = UploadedFile::fake()->image('oversized.jpg', 6001, 10)->size(200);

        $this->from('/contact')
            ->post(route('custom-request.store'), [
                'customer_name' => 'Client Mare',
                'customer_email' => 'mare@example.com',
                'reference_image' => $image,
            ])
            ->assertRedirect('/contact')
            ->assertSessionHasErrors('reference_image');

        $this->assertDatabaseMissing('custom_requests', [
            'customer_email' => 'mare@example.com',
        ]);
    }

    public function test_filament_resource_uses_the_custom_request_model(): void
    {
        $this->assertSame(CustomRequest::class, CustomRequestResource::getModel());
    }
}
