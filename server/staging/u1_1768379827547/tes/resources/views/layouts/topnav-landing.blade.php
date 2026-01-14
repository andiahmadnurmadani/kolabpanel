<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Notezque V4 - Pastel Minimalist Productivity')</title>
    <!-- Memuat Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Konfigurasi Tailwind untuk warna dan font (Sesuai Gaya Pastel Minimalis) -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        // Warna dasar pastel
                        'v4-background': '#F9F9F9', // Hampir Putih
                        'v4-surface': '#FFFFFF',    // Putih Murni
                        'v4-primary': '#0E7CF4',    // Blue Pastel (Aksen utama)
                        'v4-secondary': '#6ca9fa',  // Lavender Pastel (Aksen sekunder)
                        'v4-pink': '#FFC0CB',       // Pink Pastel
                        'v4-text': '#1F2937',       // Dark Gray (Teks utama)
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <style>
        /* Smooth scroll behavior untuk seluruh halaman */
        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #F9F9F9;
            color: #1F2937;
            overflow-x: hidden;
            line-height: 1.6;
        }

        /* Fade-in animation untuk elemen yang masuk viewport */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Class untuk animasi */
        .animate-on-scroll {
            opacity: 0;
            transition: all 0.6s ease-out;
        }

        .animate-on-scroll.animated {
            opacity: 1;
        }

        .fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
        }

        .fade-in-left {
            animation: fadeInLeft 0.8s ease-out forwards;
        }

        .fade-in-right {
            animation: fadeInRight 0.8s ease-out forwards;
        }

        /* Stagger animation untuk cards */
        .card-stagger-1 { animation-delay: 0.1s; }
        .card-stagger-2 { animation-delay: 0.2s; }
        .card-stagger-3 { animation-delay: 0.3s; }

        /* Styling untuk Button Primary Pastel dengan smooth transition */
        .primary-button-pastel {
            background-color: #0aa0f6;
            color: white;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .primary-button-pastel::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .primary-button-pastel:hover::before {
            width: 300px;
            height: 300px;
        }

        .primary-button-pastel:hover {
            background-color: #7A8CEB;
            box-shadow: 0 8px 25px rgba(140, 158, 255, 0.5);
            transform: translateY(-2px);
        }

        .primary-button-pastel:active {
            transform: translateY(0);
            box-shadow: 0 4px 15px rgba(140, 158, 255, 0.4);
        }

        /* Efek blur pastel di latar belakang (seperti di image_fbfe23.png) */
        .abstract-blur-pastel {
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            opacity: 0.2;
            filter: blur(150px);
            z-index: -1;
            mix-blend-mode: multiply;
        }
        .blur-pink-pastel { background-color: #FFC0CB; top: -100px; right: -100px; }
        .blur-blue-pastel { background-color: #0aa0f6; bottom: -100px; left: -100px; }
    </style>
</head>
<body class="antialiased">
    <!-- Abstract Blur Background Elements -->
    <div class="abstract-blur-pastel blur-pink-pastel"></div>
    <div class="abstract-blur-pastel blur-blue-pastel"></div>

    <!-- Navigasi Bar -->
    <header class="bg-white/90 backdrop-blur-lg sticky top-0 z-50 border-b border-slate-200/50 shadow-sm">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex justify-between items-center">
                <!-- Logo Notezque -->
                <a href="/" class="flex items-center space-x-3 group">
                    <div class="relative">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-cyan-600 rounded-xl blur-md opacity-20 group-hover:opacity-40 transition-opacity"></div>
                        @php
                            $logo = \App\Models\KontenStatis::where('key', 'site_logo')->first();
                            $siteName = \App\Models\KontenStatis::where('key', 'site_name')->first();
                            $tagline = \App\Models\KontenStatis::where('key', 'site_tagline')->first();
                        @endphp
                        <img src="{{ $logo ? url($logo->value) : url('logo.png') }}" alt="{{ $siteName ? $siteName->value : 'Notezque' }} Logo" class="relative h-11 w-auto transition-transform duration-300 group-hover:scale-105"/>
                    </div>
                    <div class="hidden sm:block">
                        <span class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent">{{ $siteName ? $siteName->value : 'Notezque' }}</span>
                        <p class="text-[10px] text-slate-500 font-medium -mt-1">{{ $tagline ? $tagline->value : 'Productivity Hub' }}</p>
                    </div>
                </a>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center gap-8">
                    <a href="#fitur" class="text-sm font-medium text-slate-700 hover:text-blue-600 transition-colors relative group">
                        Fitur Utama
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-blue-600 to-cyan-600 group-hover:w-full transition-all duration-300"></span>
                    </a>
                    <a href="#alur" class="text-sm font-medium text-slate-700 hover:text-blue-700 transition-colors relative group">
                        Alur Kerja
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-blue-700 to-sky-600 group-hover:w-full transition-all duration-300"></span>
                    </a>
                    <a href="#pengembangan" class="text-sm font-medium text-slate-700 hover:text-cyan-600 transition-colors relative group">
                        Roadmap
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-cyan-600 to-blue-500 group-hover:w-full transition-all duration-300"></span>
                    </a>
                    <a href="{{ route('akses.sistem') }}" 
                       class="px-6 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-700 rounded-full shadow-md hover:shadow-lg hover:scale-105 transition-all duration-300">
                        Akses Sistem
                        <svg class="inline-block w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <button class="md:hidden p-2.5 rounded-xl text-slate-700 hover:bg-slate-100 transition-colors" id="mobile-menu-button-v4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                    </svg>
                </button>
            </div>
        </nav>

        <!-- Mobile Menu -->
        <div class="md:hidden hidden bg-white/95 backdrop-blur-lg border-t border-slate-200/50" id="mobile-menu-v4">
            <div class="px-4 pt-2 pb-4 space-y-2">
                <a href="#fitur" class="block px-4 py-3 rounded-xl text-base font-medium text-slate-700 hover:bg-blue-50 hover:text-blue-600 transition-colors">
                    Fitur Utama
                </a>
                <a href="#alur" class="block px-4 py-3 rounded-xl text-base font-medium text-slate-700 hover:bg-cyan-50 hover:text-cyan-600 transition-colors">
                    Alur Kerja
                </a>
                <a href="#pengembangan" class="block px-4 py-3 rounded-xl text-base font-medium text-slate-700 hover:bg-sky-50 hover:text-sky-600 transition-colors">
                    Roadmap
                </a>
                <a href="{{ route('akses.sistem') }}" 
                   class="block px-4 py-3 text-base font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl text-center mt-3 shadow-md hover:shadow-lg transition-all">
                    Akses Sistem →
                </a>
            </div>
        </div>
    </header>

    <!-- Konten Halaman -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gradient-to-r from-slate-50 via-white to-slate-50 py-4 border-t border-slate-200/60 w-full backdrop-blur-lg">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                <!-- Copyright -->
                <div class="text-center sm:text-left">
                    @php
                        $copyright = \App\Models\KontenStatis::where('key', 'footer_copyright')->first();
                        $footerSubtitle = \App\Models\KontenStatis::where('key', 'footer_subtitle')->first();
                    @endphp
                    <p class="text-xs text-slate-600 font-medium">
                        {{ $copyright ? $copyright->value : '© 2025 Notezque. All Rights Reserved.' }}
                    </p>
                    <p class="text-[10px] text-slate-400 mt-0.5">{{ $footerSubtitle ? $footerSubtitle->value : 'Platform Manajemen Tugas dan Produktivitas Akademik' }}</p>
                </div>
                
                <!-- Quick Links -->
                <div class="flex items-center gap-4 text-xs">
                    <a href="#" class="text-slate-500 hover:text-blue-600 font-medium transition-colors">Bantuan</a>
                    <span class="text-slate-300">•</span>
                    <a href="#" class="text-slate-500 hover:text-cyan-600 font-medium transition-colors">Kebijakan Privasi</a>
                    <span class="text-slate-300">•</span>
                    <a href="#" class="text-slate-500 hover:text-blue-600 font-medium transition-colors">Tentang</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // ===== MOBILE MENU TOGGLE =====
        document.getElementById('mobile-menu-button-v4').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobile-menu-v4');
            mobileMenu.classList.toggle('hidden');
        });

        // Menutup menu mobile ketika salah satu link diklik
        document.querySelectorAll('#mobile-menu-v4 a').forEach(link => {
            link.addEventListener('click', () => {
                document.getElementById('mobile-menu-v4').classList.add('hidden');
            });
        });

        // ===== SMOOTH SCROLL FOR ANCHOR LINKS =====
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const href = this.getAttribute('href');
                
                // Skip jika link hanya "#" atau tidak ada target
                if (href === '#' || href === '#!') return;
                
                const target = document.querySelector(href);
                if (!target) return;
                
                e.preventDefault();
                
                // Smooth scroll dengan offset untuk fixed header
                const headerOffset = 80;
                const elementPosition = target.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });

                // Close mobile menu if open
                document.getElementById('mobile-menu-v4').classList.add('hidden');
            });
        });

        // ===== SCROLL ANIMATIONS (Intersection Observer) =====
        const observerOptions = {
            root: null,
            rootMargin: '0px',
            threshold: 0.1
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animated');
                    // Optional: unobserve after animation
                    // observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        // Observe all elements with animate-on-scroll class
        document.addEventListener('DOMContentLoaded', () => {
            const animateElements = document.querySelectorAll('.animate-on-scroll');
            animateElements.forEach(el => observer.observe(el));
        });

        // ===== HEADER SCROLL EFFECT =====
        let lastScroll = 0;
        const header = document.querySelector('header');

        window.addEventListener('scroll', () => {
            const currentScroll = window.pageYOffset;
            
            // Add shadow when scrolled
            if (currentScroll > 10) {
                header.classList.add('shadow-md');
            } else {
                header.classList.remove('shadow-md');
            }

            lastScroll = currentScroll;
        });

        // ===== PARALLAX EFFECT FOR BLUR BACKGROUNDS =====
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const blurElements = document.querySelectorAll('.abstract-blur-pastel');
            
            blurElements.forEach((element, index) => {
                const speed = index % 2 === 0 ? 0.5 : -0.3;
                element.style.transform = `translateY(${scrolled * speed}px)`;
            });
        });
    </script>
</body>
</html>
