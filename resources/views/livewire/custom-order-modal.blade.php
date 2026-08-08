<div>
    <div x-data="{ isOpen: @entangle('isOpen') }"
         x-show="isOpen"
         x-cloak
         @open-custom-modal.window="Livewire.dispatch('openCustomOrderModal', { productId: $event.detail.productId })"
         @keydown.escape.window="$wire.closeModal()"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
         style="display: none;">

        <div @click.away="$wire.closeModal()"
             role="dialog"
             aria-modal="true"
             aria-labelledby="custom-order-title"
             class="bg-ivory w-full max-w-lg shadow-2xl relative border border-vintage-gold/20 flex flex-col max-h-[90vh]">

            <!-- Header -->
            <div class="p-6 md:p-8 border-b border-dark-brown/10 flex justify-between items-start shrink-0">
                <div>
                    <h2 id="custom-order-title" class="font-serif text-2xl text-dark-brown mb-1">Cerere Personalizată</h2>
                    @if($product_name)
                        <p class="text-xs font-sans uppercase tracking-widest text-vintage-gold">Pentru: {{ $product_name }}</p>
                    @endif
                </div>
                <button type="button" wire:click="closeModal" aria-label="Închide formularul" class="text-dark-brown/50 hover:text-dark-brown transition-colors focus-visible:ring-2 focus-visible:ring-vintage-gold">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Body -->
            <div class="p-6 md:p-8 overflow-y-auto">
                @if($successMessage)
                    <div class="p-4 bg-green-50 text-green-800 text-sm border-l-2 border-green-500 font-light mb-6">
                        {{ $successMessage }}
                    </div>
                @else
                    <form wire:submit.prevent="submit" class="space-y-6">

                        <div>
                            <label for="custom-order-name" class="block text-[10px] uppercase tracking-widest text-dark-brown/60 mb-2 font-medium">Nume Complet *</label>
                            <input id="custom-order-name" wire:model="name" type="text" autocomplete="name" class="w-full bg-transparent border-0 border-b border-dark-brown/20 focus:ring-0 focus:border-vintage-gold py-2 text-dark-brown placeholder-dark-brown/30 font-light" placeholder="Numele tău">
                            @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="custom-order-email" class="block text-[10px] uppercase tracking-widest text-dark-brown/60 mb-2 font-medium">Email *</label>
                                <input id="custom-order-email" wire:model="email" type="email" autocomplete="email" class="w-full bg-transparent border-0 border-b border-dark-brown/20 focus:ring-0 focus:border-vintage-gold py-2 text-dark-brown placeholder-dark-brown/30 font-light" placeholder="adresa@email.com">
                                @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="custom-order-phone" class="block text-[10px] uppercase tracking-widest text-dark-brown/60 mb-2 font-medium">Telefon</label>
                                <input id="custom-order-phone" wire:model="phone" type="tel" autocomplete="tel" class="w-full bg-transparent border-0 border-b border-dark-brown/20 focus:ring-0 focus:border-vintage-gold py-2 text-dark-brown placeholder-dark-brown/30 font-light" placeholder="Opțional">
                                @error('phone') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label for="custom-order-message" class="block text-[10px] uppercase tracking-widest text-dark-brown/60 mb-2 font-medium">Mesaj / Detalii *</label>
                            <textarea id="custom-order-message" wire:model="message" rows="4" class="w-full bg-transparent border border-dark-brown/20 focus:ring-0 focus:border-vintage-gold p-3 text-dark-brown placeholder-dark-brown/30 font-light" placeholder="Descrie cum dorești să fie lucrarea (dimensiuni, culori, inspirație)..."></textarea>
                            @error('message') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <button type="submit" wire:loading.attr="disabled" wire:target="submit" class="w-full bg-dark-brown text-white py-4 uppercase tracking-[0.2em] text-[10px] font-medium hover:bg-vintage-gold transition-colors duration-500 disabled:opacity-60 disabled:cursor-wait">
                            <span wire:loading.remove wire:target="submit">Trimite Cererea</span>
                            <span wire:loading wire:target="submit">Se trimite...</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
