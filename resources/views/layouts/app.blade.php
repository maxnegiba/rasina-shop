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
                        // Nuanțe curate, cu contrast puternic (Black & White + Accent auriu vibrant)
                        'ivory': '#FAFAFA', // un alb mai curat, foarte modern
                        'cream-white': '#FFFFFF',
                        'warm-beige': '#F0EDE5',
                        'vintage-gold': '#D4AF37', // Auriu metalic, mai contrastant
                        'deep-oak': '#2C1D11',
                        'smoked-black': '#0A0A0A', // Aproape complet negru
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
<body class="bg-ivory text-smoked-black font-sans antialiased selection:bg-vintage-gold selection:text-white flex flex-col min-h-screen">

    <!-- Navbar cu aspect modern, "plutitor" sau foarte curat -->
    <nav class="bg-white/90 backdrop-blur-xl sticky top-0 z-50 border-b border-black/5 shadow-sm transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-24 items-center">
                <div class="flex-shrink-0">
                    <a href="{{ route('home') }}" class="font-serif text-2xl tracking-[0.2em] uppercase text-smoked-black hover:text-vintage-gold transition-colors duration-300 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-vintage-gold"></span>
                        Ivory Vintage
                    </a>
                </div>
                <div class="hidden md:flex flex-1 justify-center">
                    <div class="flex items-center space-x-12 font-medium tracking-[0.15em] uppercase text-[11px] text-smoked-black/60">
                        <a href="{{ route('shop.index') }}" class="hover:text-vintage-gold hover:-translate-y-0.5 transition-all duration-300">Galerie</a>
                        <a href="{{ route('blog.index') }}" class="hover:text-vintage-gold hover:-translate-y-0.5 transition-all duration-300">Jurnal</a>
                        <a href="{{ route('about') }}" class="hover:text-vintage-gold hover:-translate-y-0.5 transition-all duration-300">Poveste</a>
                        <a href="{{ route('contact') }}" class="hover:text-vintage-gold hover:-translate-y-0.5 transition-all duration-300">Contact</a>
                    </div>
                </div>
                <div class="flex items-center space-x-6">
                    <button class="text-smoked-black/80 hover:text-vintage-gold transition duration-300 relative group">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <span class="absolute -top-1 -right-2 bg-vintage-gold text-white text-[9px] w-4 h-4 flex items-center justify-center rounded-full opacity-0 group-hover:opacity-100 transition-opacity">0</span>
                    </button>
                    <!-- Mobile menu button -->
                    <button id="mobile-menu-btn" class="md:hidden text-smoked-black focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Sidebar Menu -->
    <div id="mobile-sidebar" class="fixed inset-0 z-50 bg-smoked-black/50 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300">
        <div id="mobile-sidebar-content" class="fixed top-0 right-0 bottom-0 w-64 bg-white shadow-xl transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col">
            <div class="flex justify-between items-center p-6 border-b border-black/5">
                <span class="font-serif text-lg tracking-[0.1em] uppercase text-smoked-black flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-vintage-gold"></span>
                    Meniu
                </span>
                <button id="mobile-close-btn" class="text-smoked-black focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="flex-grow py-8 px-6 overflow-y-auto flex flex-col space-y-6">
                <a href="{{ route('shop.index') }}" class="mobile-link text-sm font-medium tracking-[0.15em] uppercase text-smoked-black/80 hover:text-vintage-gold transition-colors block">Galerie</a>
                <a href="{{ route('blog.index') }}" class="mobile-link text-sm font-medium tracking-[0.15em] uppercase text-smoked-black/80 hover:text-vintage-gold transition-colors block">Jurnal</a>
                <a href="{{ route('about') }}" class="mobile-link text-sm font-medium tracking-[0.15em] uppercase text-smoked-black/80 hover:text-vintage-gold transition-colors block">Poveste</a>
                <a href="{{ route('contact') }}" class="mobile-link text-sm font-medium tracking-[0.15em] uppercase text-smoked-black/80 hover:text-vintage-gold transition-colors block">Contact</a>
            </div>
        </div>
    </div>

    <main class="flex-grow">
        @yield('content')
    </main>

    <footer class="bg-smoked-black text-white pt-24 pb-12 mt-auto">
        <div class="max-w-7xl mx-auto px-4 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-16 mb-16">
                <div class="text-center md:text-left">
                    <h2 class="font-serif text-3xl mb-6 tracking-wider text-white">Ivory Vintage</h2>
                    <p class="font-light text-white/50 text-sm leading-relaxed max-w-sm mx-auto md:mx-0">
                        O fuziune atemporală între esența naturală a lemnului și eleganța translucidă a rășinii. Piese de artă unicat, lucrate manual cu pasiune și măiestrie.
                    </p>
                </div>
                <div class="text-center md:text-left flex flex-col md:items-center">
                    <div>
                        <h3 class="font-sans text-xs uppercase tracking-[0.2em] text-vintage-gold mb-6">Explorați</h3>
                        <ul class="space-y-4 font-light text-white/60 text-sm">
                            <li><a href="{{ route('shop.index') }}" class="hover:text-white transition">Galerie</a></li>
                            <li><a href="{{ route('about') }}" class="hover:text-white transition">Povestea Noastră</a></li>
                            <li><a href="{{ route('contact') }}" class="hover:text-white transition">Comenzi Personalizate</a></li>
                        </ul>
                    </div>
                </div>
                <div class="text-center md:text-right">
                    <h3 class="font-sans text-xs uppercase tracking-[0.2em] text-vintage-gold mb-6">Social</h3>
                    <ul class="space-y-4 font-light text-white/60 text-sm">
                        <li><a href="#" class="hover:text-white transition">Instagram</a></li>
                        <li><a href="#" class="hover:text-white transition">Facebook</a></li>
                        <li><a href="#" class="hover:text-white transition">Pinterest</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-white/10 pt-8 flex flex-col md:flex-row justify-between items-center text-[10px] uppercase tracking-[0.2em] text-white/40">
                <div class="mb-4 md:mb-0">
                    &copy; {{ date('Y') }} Ivory Vintage Gallery.
                </div>
                <div class="space-x-4">
                    <a href="#" class="hover:text-white transition">Termeni & Condiții</a>
                    <span>|</span>
                    <a href="#" class="hover:text-white transition">Confidențialitate</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Mobile Sidebar Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuBtn = document.getElementById('mobile-menu-btn');
            const closeBtn = document.getElementById('mobile-close-btn');
            const sidebar = document.getElementById('mobile-sidebar');
            const sidebarContent = document.getElementById('mobile-sidebar-content');
            const mobileLinks = document.querySelectorAll('.mobile-link');

            function openSidebar() {
                // Show the container
                sidebar.classList.remove('hidden');

                // Allow a tiny delay for display:block to apply before animating opacity/transform
                setTimeout(() => {
                    sidebar.classList.remove('opacity-0');
                    sidebarContent.classList.remove('translate-x-full');
                }, 10);

                // Prevent background scrolling
                document.body.style.overflow = 'hidden';
            }

            function closeSidebar() {
                // Animate out
                sidebar.classList.add('opacity-0');
                sidebarContent.classList.add('translate-x-full');

                // Hide after animation finishes
                setTimeout(() => {
                    sidebar.classList.add('hidden');
                }, 300); // 300ms matches the transition duration

                // Restore background scrolling
                document.body.style.overflow = '';
            }

            if (menuBtn && closeBtn && sidebar && sidebarContent) {
                // Open sidebar on hamburger click
                menuBtn.addEventListener('click', openSidebar);

                // Close sidebar on close button click
                closeBtn.addEventListener('click', closeSidebar);

                // Close sidebar on clicking outside the content area (on the backdrop)
                sidebar.addEventListener('click', function(e) {
                    if (e.target === sidebar) {
                        closeSidebar();
                    }
                });

                // Close sidebar when a link is clicked
                mobileLinks.forEach(link => {
                    link.addEventListener('click', closeSidebar);
                });
            }
        });
    </script>
</body>
</html>
