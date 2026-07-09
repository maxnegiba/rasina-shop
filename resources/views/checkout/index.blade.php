@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-24">
    <h1 class="font-serif text-3xl md:text-4xl text-dark-brown mb-8 text-center">Finalizare Comandă</h1>

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50/50 text-red-800 text-sm border-l border-red-500/30 font-light max-w-3xl mx-auto">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex flex-col lg:flex-row gap-12 max-w-5xl mx-auto">
        <div class="w-full lg:w-2/3">
            <form action="{{ route('checkout.process') }}" method="POST" id="checkout-form" class="space-y-8">
                @csrf

                <div class="bg-ivory p-6 md:p-8 border border-black/5">
                    <h2 class="font-serif text-xl text-dark-brown mb-6">Date de Contact</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] uppercase tracking-[0.1em] text-dark-brown/60 mb-2">Nume complet *</label>
                            <input type="text" name="name" required value="{{ old('name') }}" class="w-full bg-white border border-black/10 px-4 py-3 text-sm text-dark-brown focus:border-vintage-gold focus:ring-0 transition-colors">
                            @error('name') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase tracking-[0.1em] text-dark-brown/60 mb-2">Telefon *</label>
                            <input type="text" name="phone" required value="{{ old('phone') }}" class="w-full bg-white border border-black/10 px-4 py-3 text-sm text-dark-brown focus:border-vintage-gold focus:ring-0 transition-colors">
                            @error('phone') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[10px] uppercase tracking-[0.1em] text-dark-brown/60 mb-2">Email *</label>
                            <input type="email" name="email" required value="{{ old('email') }}" class="w-full bg-white border border-black/10 px-4 py-3 text-sm text-dark-brown focus:border-vintage-gold focus:ring-0 transition-colors">
                            @error('email') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="bg-ivory p-6 md:p-8 border border-black/5">
                    <h2 class="font-serif text-xl text-dark-brown mb-6">Metodă de Livrare</h2>

                    <div class="space-y-4">
                        <label class="flex items-center p-4 border border-black/10 cursor-pointer hover:border-vintage-gold transition-colors">
                            <input type="radio" name="shipping_method" value="home" class="text-vintage-gold focus:ring-vintage-gold h-4 w-4 border-gray-300" {{ old('shipping_method', 'home') === 'home' ? 'checked' : '' }} onchange="toggleShippingFields()">
                            <span class="ml-3 block">
                                <span class="block text-sm font-medium text-dark-brown">Livrare la Domiciliu (Sameday)</span>
                                <span class="block text-xs text-dark-brown/60 mt-1">Cost: 20 RON</span>
                            </span>
                        </label>

                        <label class="flex items-center p-4 border border-black/10 cursor-pointer hover:border-vintage-gold transition-colors">
                            <input type="radio" name="shipping_method" value="locker" class="text-vintage-gold focus:ring-vintage-gold h-4 w-4 border-gray-300" {{ old('shipping_method') === 'locker' ? 'checked' : '' }} onchange="toggleShippingFields()">
                            <span class="ml-3 block">
                                <span class="block text-sm font-medium text-dark-brown">Livrare la Easybox (Sameday)</span>
                                <span class="block text-xs text-dark-brown/60 mt-1">Cost: 15 RON</span>
                            </span>
                        </label>
                        @error('shipping_method') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <!-- Home Address Fields -->
                    <div id="home-fields" class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6 {{ old('shipping_method', 'home') === 'home' ? 'block' : 'hidden' }}">
                        <div class="md:col-span-2">
                            <label class="block text-[10px] uppercase tracking-[0.1em] text-dark-brown/60 mb-2">Adresă completă *</label>
                            <input type="text" name="address" id="home_address" value="{{ old('address') }}" class="w-full bg-white border border-black/10 px-4 py-3 text-sm text-dark-brown focus:border-vintage-gold focus:ring-0 transition-colors">
                            @error('address') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase tracking-[0.1em] text-dark-brown/60 mb-2">Județ *</label>
                            <input type="text" name="county" id="home_county" value="{{ old('county') }}" class="w-full bg-white border border-black/10 px-4 py-3 text-sm text-dark-brown focus:border-vintage-gold focus:ring-0 transition-colors">
                            @error('county') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase tracking-[0.1em] text-dark-brown/60 mb-2">Oraș *</label>
                            <input type="text" name="city" id="home_city" value="{{ old('city') }}" class="w-full bg-white border border-black/10 px-4 py-3 text-sm text-dark-brown focus:border-vintage-gold focus:ring-0 transition-colors">
                            @error('city') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Easybox Fields -->
                    <div id="locker-fields" class="mt-6 {{ old('shipping_method') === 'locker' ? 'block' : 'hidden' }}">
                        <button type="button" id="open-locker-map" class="w-full bg-white border border-vintage-gold text-dark-brown px-6 py-4 uppercase tracking-[0.1em] text-xs font-medium hover:bg-vintage-gold hover:text-white transition-colors duration-300 mb-4">
                            Selectează Easybox-ul
                        </button>

                        <div id="selected-locker-details" class="hidden p-4 bg-white border border-green-500/30">
                            <p class="text-xs text-green-700 font-medium mb-1">Easybox selectat:</p>
                            <p id="display-easybox-name" class="text-sm font-medium text-dark-brown"></p>
                            <p id="display-easybox-address" class="text-xs text-dark-brown/60 mt-1"></p>
                        </div>

                        <input type="hidden" name="easybox_id" id="easybox_id" value="{{ old('easybox_id') }}">
                        <input type="hidden" name="easybox_name" id="easybox_name" value="{{ old('easybox_name') }}">
                        <input type="hidden" name="easybox_address" id="easybox_address" value="{{ old('easybox_address') }}">

                        @error('easybox_id') <span class="text-xs text-red-500 mt-2 block">Vă rugăm să selectați un Easybox.</span> @enderror
                    </div>
                </div>

                <button type="submit" class="w-full bg-dark-brown text-white px-8 py-5 uppercase tracking-[0.2em] text-[10px] font-medium hover:bg-vintage-gold transition-colors duration-500 shadow-sm flex justify-center items-center gap-2 group">
                    <span>Continuă către plată</span>
                    <span class="transform group-hover:translate-x-1 transition-transform duration-300">&rarr;</span>
                </button>
            </form>
        </div>

        <div class="w-full lg:w-1/3">
            <div class="bg-ivory p-6 md:p-8 border border-black/5 sticky top-8">
                <h2 class="font-serif text-xl text-dark-brown mb-6">Sumar Comandă</h2>

                <div class="space-y-4 mb-6">
                    @foreach($cart as $item)
                        <div class="flex justify-between items-start gap-4 pb-4 border-b border-black/5">
                            <div>
                                <h3 class="text-sm text-dark-brown line-clamp-1" title="{{ $item['name'] }}">{{ $item['name'] }}</h3>
                                <p class="text-[10px] uppercase tracking-[0.1em] text-dark-brown/50 mt-1">Cantitate: {{ $item['quantity'] }}</p>
                            </div>
                            <span class="text-sm font-medium text-dark-brown whitespace-nowrap">{{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }} RON</span>
                        </div>
                    @endforeach
                </div>

                <div class="flex justify-between items-center text-sm text-dark-brown mb-2">
                    <span class="font-light">Subtotal</span>
                    <span>{{ number_format($total, 0, ',', '.') }} RON</span>
                </div>

                <div class="flex justify-between items-center text-sm text-dark-brown mb-6 pb-6 border-b border-black/5">
                    <span class="font-light">Livrare</span>
                    <span id="shipping-cost-display">20 RON</span>
                </div>

                <div class="flex justify-between items-center text-lg font-medium text-dark-brown">
                    <span>Total</span>
                    <span id="total-cost-display">{{ number_format($total + 20, 0, ',', '.') }} RON</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.sameday.ro/locker-plugin/lockerpluginsdk.js"></script>
<script>
    const baseTotal = {{ $total }};

    function toggleShippingFields() {
        const method = document.querySelector('input[name="shipping_method"]:checked').value;
        const homeFields = document.getElementById('home-fields');
        const lockerFields = document.getElementById('locker-fields');
        const shippingCostDisplay = document.getElementById('shipping-cost-display');
        const totalCostDisplay = document.getElementById('total-cost-display');

        const homeInputs = homeFields.querySelectorAll('input');

        if (method === 'home') {
            homeFields.classList.remove('hidden');
            lockerFields.classList.add('hidden');
            homeInputs.forEach(input => input.setAttribute('required', 'required'));

            shippingCostDisplay.textContent = '20 RON';
            totalCostDisplay.textContent = (baseTotal + 20).toLocaleString('ro-RO') + ' RON';
        } else {
            homeFields.classList.add('hidden');
            lockerFields.classList.remove('hidden');
            homeInputs.forEach(input => input.removeAttribute('required'));

            shippingCostDisplay.textContent = '15 RON';
            totalCostDisplay.textContent = (baseTotal + 15).toLocaleString('ro-RO') + ' RON';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        toggleShippingFields(); // Set initial state

        // Sameday Locker Integration
        const lockerPluginUrl = '{{ config("services.sameday.env") === "prod" ? "https://lockerplugin.sameday.ro" : "https://lockerplugin.sameday.ro" }}'; // Verificăm dacă există mediu separat pentru hartă demo. Oficial SDK-ul detectează intern pe baza clientId-ului dacă e cazul.

        const lockerOptions = {
            clientId: '{{ config("services.sameday.username") }}',
            countryCode: 'RO',
            langCode: 'ro',
            apiUsername: '{{ config("services.sameday.username") }}',
        };

        let lockerPlugin;
        if(window.LockerPlugin) {
            lockerPlugin = window.LockerPlugin.getInstance();
            lockerPlugin.init(lockerOptions);
        }

        document.getElementById('open-locker-map').addEventListener('click', function() {
            if(lockerPlugin) {
                lockerPlugin.open();
            } else {
                alert("Harta Easybox nu s-a putut încărca.");
            }
        });

        if(lockerPlugin) {
             lockerPlugin.subscribe((locker) => {
                document.getElementById('easybox_id').value = locker.lockerId;
                document.getElementById('easybox_name').value = locker.name;
                document.getElementById('easybox_address').value = locker.address;

                document.getElementById('display-easybox-name').textContent = locker.name;
                document.getElementById('display-easybox-address').textContent = locker.address;
                document.getElementById('selected-locker-details').classList.remove('hidden');

                lockerPlugin.close();
            });
        }

        // Restor pre-selected data if validation fails
        const oldEasyboxName = document.getElementById('easybox_name').value;
        if(oldEasyboxName) {
            document.getElementById('display-easybox-name').textContent = oldEasyboxName;
            document.getElementById('display-easybox-address').textContent = document.getElementById('easybox_address').value;
            document.getElementById('selected-locker-details').classList.remove('hidden');
        }
    });
</script>
@endsection
