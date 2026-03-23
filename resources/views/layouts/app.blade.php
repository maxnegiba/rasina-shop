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

    <<nav class="bg-white/90 backdrop-blur-xl sticky top-0 z-50 border-b border-black/5 shadow-sm transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-24 items-center">
                <div class="flex-shrink-0">
                    <a href="{{ route('home') }}" class="font-serif text-lg md:text-2xl tracking-[0.2em] uppercase text-smoked-black hover:text-vintage-gold transition-colors duration-300 flex items-center gap-2 md:gap-3">
                        <img src="/img/logo.png" alt="Ivory Vintage Logo" class="h-14 md:h-20 w-auto object-contain transition-all duration-300">
                        
                        <span>MTD</span>
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
                    <button id="cart-menu-btn" class="text-smoked-black/80 hover:text-vintage-gold transition duration-300 relative group focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <span id="cart-count-badge" class="absolute -top-1 -right-2 bg-vintage-gold text-white text-[9px] w-4 h-4 flex items-center justify-center rounded-full opacity-100 transition-opacity {{ session('cart') && count(session('cart')) > 0 ? '' : 'hidden' }}">
                            {{ session('cart') ? count(session('cart')) : 0 }}
                        </span>
                    </button>
                    <button id="mobile-menu-btn" class="md:hidden text-smoked-black focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>

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

    <div id="cart-sidebar" class="fixed inset-0 z-[60] bg-smoked-black/50 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300">
        <div id="cart-sidebar-content" class="fixed top-0 right-0 bottom-0 w-full sm:w-96 bg-white shadow-xl transform translate-x-full transition-transform duration-300 ease-in-out">
            <div id="cart-sidebar-inner" class="h-full">
                @include('cart._sidebar_content')
            </div>
        </div>
    </div>

    <button id="floating-cart-btn" class="fixed bottom-6 right-6 z-40 bg-smoked-black text-white p-4 rounded-full shadow-lg hover:bg-vintage-gold hover:-translate-y-1 transition-all duration-300 focus:outline-none {{ session('cart') && count(session('cart')) > 0 ? '' : 'hidden' }}">
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile Sidebar
            const menuBtn = document.getElementById('mobile-menu-btn');
            const closeBtn = document.getElementById('mobile-close-btn');
            const sidebar = document.getElementById('mobile-sidebar');
            const sidebarContent = document.getElementById('mobile-sidebar-content');
            const mobileLinks = document.querySelectorAll('.mobile-link');

            function openSidebar() {
                sidebar.classList.remove('hidden');
                setTimeout(() => {
                    sidebar.classList.remove('opacity-0');
                    sidebarContent.classList.remove('translate-x-full');
                }, 10);
                document.body.style.overflow = 'hidden';
            }

            function closeSidebar() {
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
                cartSidebar.classList.remove('hidden');
                setTimeout(() => {
                    cartSidebar.classList.remove('opacity-0');
                    cartSidebarContent.classList.remove('translate-x-full');
                }, 10);
                document.body.style.overflow = 'hidden';
            }

            function closeCartSidebar() {
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

                    const tokenMatch = document.head.innerHTML.match(/<meta\s+name="csrf-token"\s+content="([^"]+)"/);
                    let csrfToken = tokenMatch ? tokenMatch[1] : (document.querySelector('input[name="_token"]')?.value || '');

                    const formData = new FormData();
                    formData.append('id', productId);
                    formData.append('_token', csrfToken);

                    fetch("{{ route('cart.remove') }}", {
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