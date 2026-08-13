<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verificare securitate — MTD ART</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-ivory text-dark-brown flex items-center justify-center p-6">
    <main class="w-full max-w-md bg-white border border-black/10 shadow-xl p-8 sm:p-10">
        <div class="text-center mb-8">
            <p class="text-[10px] tracking-[0.22em] uppercase text-vintage-gold font-semibold mb-3">Securitate administrare</p>
            <h1 class="font-serif text-3xl mb-3">Verificare în doi pași</h1>
            <p class="text-sm text-dark-brown/60 leading-relaxed">Am trimis un cod de 6 cifre la <strong>{{ $email }}</strong>. Codul expiră în 10 minute.</p>
        </div>

        @if(session('status'))
            <div class="mb-5 border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.mfa.verify') }}" class="space-y-5">
            @csrf
            <div>
                <label for="code" class="block text-xs uppercase tracking-[0.14em] mb-2">Cod securitate</label>
                <input id="code" name="code" type="text" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" maxlength="6" required autofocus class="w-full border border-black/20 px-4 py-4 text-center text-2xl tracking-[0.35em] focus:border-vintage-gold focus:ring-vintage-gold">
                @error('code')<p class="mt-2 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="w-full bg-dark-brown text-white px-5 py-4 uppercase tracking-[0.15em] text-[10px] font-semibold hover:bg-vintage-gold transition-colors">Verifică și continuă</button>
        </form>

        <div class="mt-5 flex flex-col items-center gap-3">
            <form method="POST" action="{{ route('admin.mfa.resend') }}" class="text-center">
                @csrf
                <button type="submit" class="text-xs underline text-dark-brown/60 hover:text-vintage-gold">Trimite un cod nou</button>
            </form>

            <form method="POST" action="{{ route('admin.mfa.logout') }}" class="text-center">
                @csrf
                <button type="submit" class="text-xs underline text-dark-brown/60 hover:text-vintage-gold">Conectează-te cu alt cont</button>
            </form>
        </div>
    </main>
</body>
</html>
