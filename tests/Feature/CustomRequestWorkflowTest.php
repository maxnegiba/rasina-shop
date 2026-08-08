<?php

namespace Tests\Feature;

use App\Filament\Resources\CustomRequestResource;
use App\Models\CustomRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    public function test_filament_resource_uses_the_custom_request_model(): void
    {
        $this->assertSame(CustomRequest::class, CustomRequestResource::getModel());
    }
}
