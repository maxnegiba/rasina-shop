<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\CustomRequest;
use App\Models\Product;

class CustomOrderModal extends Component
{
    public $isOpen = false;
    public $product_id = null;
    public $product_name = null;
    public $name = '';
    public $email = '';
    public $phone = '';
    public $message = '';
    public $successMessage = '';

    protected $listeners = ['openCustomOrderModal' => 'openModal'];

    public function openModal($productId = null)
    {
        $this->isOpen = true;
        $this->successMessage = '';
        $this->product_id = $productId;

        if ($productId) {
            $product = Product::find($productId);
            $this->product_name = $product ? $product->name : null;
        } else {
            $this->product_name = null;
        }
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->reset(['name', 'email', 'phone', 'message', 'successMessage', 'product_id', 'product_name']);
    }

    public function submit()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string',
        ]);

        CustomRequest::create([
            'customer_name' => $this->name,
            'customer_email' => $this->email,
            'customer_phone' => $this->phone,
            'special_message' => $this->message,
            'product_id' => $this->product_id,
            'status' => 'new',
        ]);

        $this->successMessage = 'Cererea a fost trimisă cu succes! Te vom contacta în curând.';
        $this->reset(['name', 'email', 'phone', 'message']);
    }

    public function render()
    {
        return view('livewire.custom-order-modal');
    }
}
