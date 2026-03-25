<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ivory Vintage | Artă din Rășină Epoxidică</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;1,400;1,600&family=Montserrat:wght@300;400&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        // Paleta Cromatică (Ivory Vintage)
                        'ivory': '#FFFFF0', // Fildeș
                        'cream-white': '#FFFDD0', // Alb Crem
                        'warm-beige': '#F5F5DC', // Bej Cald
                        'vintage-gold': '#CFB53B', // Auriu antichizat
                        'deep-oak': '#3B2818', // Lemn de nuc/stejar profund
                        'dark-brown': '#2C1E16', // Maro Brun Închis
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
<body class="bg-ivory text-dark-brown font-sans antialiased selection:bg-vintage-gold selection:text-white flex flex-col min-h-screen">

    <nav class="bg-ivory/90 backdrop-blur-xl sticky top-0 z-50 border-b border-black/5 shadow-sm transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-24 items-center relative">
                <div class="flex-shrink-0 flex items-center h-full">
                    <a href="{{ route('home') ?? '/' }}" class="font-serif text-lg md:text-2xl tracking-[0.2em] uppercase text-dark-brown hover:text-vintage-gold transition-colors duration-300 flex items-center gap-2 md:gap-3">
                        <div class="relative h-24 w-24 md:w-32 flex items-center justify-center">
                            <img src="/img/logo.png" alt="Ivory Vintage Logo" class="absolute top-1 md:top-2 h-24 md:h-32 w-auto object-contain transition-all duration-300">
                        </div>
                        <span>MTD</span>
                    </a>
                </div>
                <div class="hidden md:flex flex-1 justify-center">
                    <div class="flex items-center space-x-12 font-medium tracking-[0.15em] uppercase text-[11px] text-dark-brown/60">
                        <a href="{{ route('shop.index') }}" class="hover:text-vintage-gold hover:-translate-y-0.5 transition-all duration-300">Galerie</a>
                        <a href="#" class="hover:text-vintage-gold hover:-translate-y-0.5 transition-all duration-300">Jurnal</a>
                        <a href="#" class="hover:text-vintage-gold hover:-translate-y-0.5 transition-all duration-300">Poveste</a>
                        <a href="{{ route('contact') ?? '#' }}" class="hover:text-vintage-gold hover:-translate-y-0.5 transition-all duration-300">Contact</a>
                    </div>
                </div>
                <div class="flex items-center space-x-6">
                    <button id="cart-menu-btn" class="text-dark-brown/80 hover:text-vintage-gold transition duration-300 relative group focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <span id="cart-count-badge" class="absolute -top-1 -right-2 bg-vintage-gold text-white text-[9px] w-4 h-4 flex items-center justify-center rounded-full opacity-100 transition-opacity {{ session('cart') && count(session('cart')) > 0 ? '' : 'hidden' }}">
                            {{ session('cart') ? count(session('cart')) : 0 }}
                        </span>
                    </button>
                    <button id="mobile-menu-btn" class="md:hidden text-dark-brown focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <div id="mobile-sidebar" class="fixed inset-0 z-50 bg-dark-brown/50 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300">
        <div id="mobile-sidebar-content" class="fixed top-0 right-0 bottom-0 w-64 bg-ivory shadow-xl transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col">
            <div class="flex justify-between items-center p-6 border-b border-black/5">
                <span class="font-serif text-lg tracking-[0.1em] uppercase text-dark-brown flex items-center gap-2">
                    <img src="/img/logo.png" alt="Logo" class="h-8 w-auto object-contain mr-2">
                    Meniu
                </span>
                <button id="mobile-close-btn" class="text-dark-brown focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="flex-grow py-8 px-6 overflow-y-auto flex flex-col space-y-6">
                <a href="{{ route('shop.index') }}" class="mobile-link text-sm font-medium tracking-[0.15em] uppercase text-dark-brown/80 hover:text-vintage-gold transition-colors block">Galerie</a>
                <a href="#" class="mobile-link text-sm font-medium tracking-[0.15em] uppercase text-dark-brown/80 hover:text-vintage-gold transition-colors block">Jurnal</a>
                <a href="#" class="mobile-link text-sm font-medium tracking-[0.15em] uppercase text-dark-brown/80 hover:text-vintage-gold transition-colors block">Poveste</a>
                <a href="{{ route('contact') ?? '#' }}" class="mobile-link text-sm font-medium tracking-[0.15em] uppercase text-dark-brown/80 hover:text-vintage-gold transition-colors block">Contact</a>
            </div>
        </div>
    </div>

    <div id="cart-sidebar" class="fixed inset-0 z-[60] bg-dark-brown/50 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300">
        <div id="cart-sidebar-content" class="fixed top-0 right-0 bottom-0 w-full sm:w-96 bg-ivory shadow-xl transform translate-x-full transition-transform duration-300 ease-in-out">
            <div id="cart-sidebar-inner" class="h-full">
                @if(View::exists('cart._sidebar_content'))
                    @include('cart._sidebar_content')
                @endif
            </div>
        </div>
    </div>

    <button id="floating-cart-btn" class="fixed bottom-6 right-6 z-40 bg-dark-brown text-white p-4 rounded-full shadow-lg hover:bg-vintage-gold hover:-translate-y-1 transition-all duration-300 focus:outline-none {{ session('cart') && count(session('cart')) > 0 ? '' : 'hidden' }}">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
        </svg>
        <span id="floating-cart-count" class="absolute -top-1 -right-1 bg-vintage-gold text-white text-[10px] w-5 h-5 flex items-center justify-center rounded-full border-2 border-ivory font-bold">
            {{ session('cart') ? count(session('cart')) : 0 }}
        </span>
    </button>

    <main class="flex-grow">
        @yield('content')
    </main>

    <footer class="bg-dark-brown text-white pt-20 pb-10 mt-auto border-t border-vintage-gold/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-12 lg:gap-8 mb-16">
                
                <div class="md:col-span-4 text-center md:text-left">
                    <h2 class="font-serif text-3xl mb-4 tracking-wider text-white">Ivory Vintage</h2>
                    <p class="font-light text-white/50 text-sm leading-relaxed mb-6 max-w-sm mx-auto md:mx-0">
                        O fuziune atemporală între esența naturală a lemnului și eleganța translucidă a rășinii. Piese de artă unicat, lucrate manual cu pasiune și măiestrie în România.
                    </p>
                    <a href="mailto:contact@mtdart.ro" class="text-vintage-gold hover:text-white transition-colors duration-300 text-sm font-medium tracking-wide">
                        contact@mtdart.ro
                    </a>
                </div>

                <div class="md:col-span-2 md:col-start-7 text-center md:text-left">
                    <h3 class="font-sans text-[10px] uppercase tracking-[0.2em] text-vintage-gold mb-6 font-semibold">Explorați</h3>
                    <ul class="space-y-4 font-light text-white/60 text-sm">
                        <li><a href="{{ route('shop.index') }}" class="hover:text-white hover:translate-x-1 transition-all duration-300 inline-block">Galerie Produse</a></li>
                        <li><a href="#" class="hover:text-white hover:translate-x-1 transition-all duration-300 inline-block">Povestea Noastră</a></li>
                        <li><a href="{{ route('contact') ?? '#' }}" class="hover:text-white hover:translate-x-1 transition-all duration-300 inline-block">Comenzi Personalizate</a></li>
                    </ul>
                </div>

                <div class="md:col-span-2 text-center md:text-left">
                    <h3 class="font-sans text-[10px] uppercase tracking-[0.2em] text-vintage-gold mb-6 font-semibold">Informații Utile</h3>
                    <ul class="space-y-4 font-light text-white/60 text-sm">
                        <li><a href="{{ route('legal.terms') }}" class="hover:text-white hover:translate-x-1 transition-all duration-300 inline-block">Termeni și Condiții</a></li>
                        <li><a href="{{ route('legal.privacy') }}" class="hover:text-white hover:translate-x-1 transition-all duration-300 inline-block">Politica de Confidențialitate</a></li>
                        <li><a href="{{ route('legal.returns') }}" class="hover:text-white hover:translate-x-1 transition-all duration-300 inline-block">Livrare și Retur</a></li>
                    </ul>
                </div>

                <div class="md:col-span-2 text-center md:text-left">
                    <h3 class="font-sans text-[10px] uppercase tracking-[0.2em] text-vintage-gold mb-6 font-semibold">Social</h3>
                    <ul class="space-y-4 font-light text-white/60 text-sm">
                        <li><a href="#" target="_blank" class="hover:text-white transition-colors duration-300 flex items-center justify-center md:justify-start gap-2">Instagram</a></li>
                        <li><a href="#" target="_blank" class="hover:text-white transition-colors duration-300 flex items-center justify-center md:justify-start gap-2">Facebook</a></li>
                        <li><a href="#" target="_blank" class="hover:text-white transition-colors duration-300 flex items-center justify-center md:justify-start gap-2">Pinterest</a></li>
                    </ul>
                </div>

            </div>

            <div class="border-t border-white/10 pt-8 flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="text-[10px] uppercase tracking-[0.2em] text-white/40 text-center md:text-left">
                    &copy; {{ date('Y') }} Ivory Vintage Gallery.<br class="md:hidden"> Toate drepturile rezervate.
                </div>
                
                <div class="flex items-center gap-4 opacity-50 grayscale hover:grayscale-0 transition-all duration-500">
                    <div class="flex items-center gap-1 text-[10px] text-white/60 uppercase tracking-[0.1em] mr-2">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        Plăți Securizate
                    </div>
                    <span class="text-xs font-bold text-white/80">VISA</span>
                    <span class="text-xs font-bold text-white/80">Mastercard</span>
                    <span class="text-xs font-bold text-white/80">Stripe</span>
                </div>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile Sidebar
            const menuBtn = document.getElementById('mobile-menu-btn');
            const closeBtn = document.getElementById('mobile-close-btn');
            const sidebar = document.getElementById('mobile-sidebar');
            const sidebarContent = document.getElementById('mobile-sidebar-content');
            const mobileLinks = document.querySelectorAll('.mobile-link');

            function openSidebar() {
                if(!sidebar) return;
                sidebar.classList.remove('hidden');
                setTimeout(() => {
                    sidebar.classList.remove('opacity-0');
                    sidebarContent.classList.remove('translate-x-full');
                }, 10);
                document.body.style.overflow = 'hidden';
            }

            function closeSidebar() {
                if(!sidebar) return;
                sidebar.classList.add('opacity-0');
                sidebarContent.classList.add('translate-x-full');
                setTimeout(() => {
                    sidebar.classList.add('hidden');
                }, 300);
                document.body.style.overflow = '';
            }

            if (menuBtn && closeBtn && sidebar && sidebarContent) {
                menuBtn.addEventListener('click', openSidebar);
                closeBtn.addEventListener('click', closeSidebar);
                sidebar.addEventListener('click', function(e) {
                    if (e.target === sidebar) closeSidebar();
                });
                mobileLinks.forEach(link => link.addEventListener('click', closeSidebar));
            }

            // Cart Sidebar
            const cartSidebar = document.getElementById('cart-sidebar');
            const cartSidebarContent = document.getElementById('cart-sidebar-content');
            const cartMenuBtn = document.getElementById('cart-menu-btn');
            const floatingCartBtn = document.getElementById('floating-cart-btn');
            const cartSidebarInner = document.getElementById('cart-sidebar-inner');

            function openCartSidebar() {
                if(!cartSidebar) return;
                cartSidebar.classList.remove('hidden');
                setTimeout(() => {
                    cartSidebar.classList.remove('opacity-0');
                    cartSidebarContent.classList.remove('translate-x-full');
                }, 10);
                document.body.style.overflow = 'hidden';
            }

            function closeCartSidebar() {
                if(!cartSidebar) return;
                cartSidebar.classList.add('opacity-0');
                cartSidebarContent.classList.add('translate-x-full');
                setTimeout(() => {
                    cartSidebar.classList.add('hidden');
                }, 300);
                document.body.style.overflow = '';
            }

            if (cartMenuBtn) cartMenuBtn.addEventListener('click', openCartSidebar);
            if (floatingCartBtn) floatingCartBtn.addEventListener('click', openCartSidebar);

            document.body.addEventListener('click', function(e) {
                const closeBtn = e.target.closest('#cart-sidebar-close');
                if (closeBtn || e.target === cartSidebar) {
                    closeCartSidebar();
                }
            });

            function updateCartUI(cartCount, htmlContent) {
                const navbarBadge = document.getElementById('cart-count-badge');
                const floatingBtn = document.getElementById('floating-cart-btn');
                const floatingCount = document.getElementById('floating-cart-count');

                if (navbarBadge) {
                    navbarBadge.textContent = cartCount;
                    cartCount > 0 ? navbarBadge.classList.remove('hidden') : navbarBadge.classList.add('hidden');
                }

                if (floatingBtn && floatingCount) {
                    floatingCount.textContent = cartCount;
                    cartCount > 0 ? floatingBtn.classList.remove('hidden') : floatingBtn.classList.add('hidden');
                }

                if (cartSidebarInner && htmlContent) {
                    cartSidebarInner.innerHTML = htmlContent;
                }
            }

            // AJAX Adaugare in Cos
            const addForms = document.querySelectorAll('.add-to-cart-ajax-form');
            addForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const clickedButton = e.submitter;
                    if (clickedButton && clickedButton.value === "1") return;

                    e.preventDefault();
                    const formData = new FormData(form);
                    const url = form.getAttribute('action');

                    fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Eroare la adăugarea în coș.');
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            updateCartUI(data.cart_count, data.html);
                            openCartSidebar();
                        }
                    })
                    .catch(error => console.error(error));
                });
            });

            // AJAX Stergere din Cos
            document.body.addEventListener('click', function(e) {
                const removeBtn = e.target.closest('.remove-from-cart-btn');
                if (removeBtn) {
                    e.preventDefault();
                    const productId = removeBtn.getAttribute('data-id');
                    if (!productId) return;

                    // Extragem meta tag-ul pt token csrf (l-am adăugat în <head>)
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    const formData = new FormData();
                    formData.append('id', productId);
                    formData.append('_token', csrfToken);

                    // Deoarece adăugăm rutele legal pe viitor, ne asigurăm că punem aici ruta corectă pentru remove (presupusă /cos/sterge)
                    // Dacă ai definit route('cart.remove'), folosește direct stringul '/cos/sterge' sau {{ route('cart.remove') }}
                    fetch("{{ route('cart.remove') ?? '/cos/sterge' }}", {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            updateCartUI(data.cart_count, data.html);
                        }
                    })
                    .catch(error => console.error('Error removing item:', error));
                }
            });
        });
    </script>
</body>
</html>