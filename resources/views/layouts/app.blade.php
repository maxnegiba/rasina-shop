<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ivory Vintage | Artă din Rășină Epoxidică</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;1,400;1,600&family=Montserrat:wght@300;400&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        ivory: '#FFFFF0',
                        'cream-white': '#FFFDD0',
                        'warm-beige': '#F5F5DC',
                        'vintage-gold': '#B8860B',
                        'deep-oak': '#3F2C23',
                        'smoked-black': '#100C08',
                    },
                    fontFamily: {
                        serif: ['Playfair Display', 'serif'],
                        sans: ['Montserrat', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-ivory text-smoked-black font-sans antialiased">

    <nav class="border-b border-vintage-gold/20 bg-ivory/80 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex-shrink-0">
                    <a href="{{ route('home') }}" class="font-serif text-3xl tracking-widest uppercase text-vintage-gold">
                        Ivory Vintage
                    </a>
                </div>
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-8 font-light tracking-wide uppercase text-sm">
                        <a href="{{ route('shop.index') }}" class="hover:text-vintage-gold transition">Galerie</a>
                        <a href="{{ route('blog.index') }}" class="hover:text-vintage-gold transition">Jurnal</a>
                        <a href="{{ route('about') }}" class="hover:text-vintage-gold transition">Poveste</a>
                        <a href="{{ route('contact') }}" class="hover:text-vintage-gold transition">Contact</a>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <button class="text-smoked-black hover:text-vintage-gold">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer class="bg-smoked-black text-ivory py-16 mt-20">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h2 class="font-serif text-3xl mb-6">Ivory Vintage Art Gallery</h2>
            <p class="max-w-md mx-auto font-light opacity-70 mb-8">
                Fiecare piesă este o poveste unică scrisă în lemn și rășină epoxidică.
            </p>
            <div class="border-t border-ivory/10 pt-8 text-xs uppercase tracking-widest opacity-50">
                &copy; {{ date('Y') }} Ivory Vintage. Creat cu pasiune.
            </div>
        </div>
    </footer>

</body>
</html>
