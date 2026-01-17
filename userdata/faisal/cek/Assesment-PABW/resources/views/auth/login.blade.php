@props(['title' => 'Login - DonasiKita'])

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>

    {{-- CDN Tailwind & FontAwesome --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    {{-- Google Fonts: Plus Jakarta Sans --}}
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        primary: '#2563EB', // Blue 600
                        secondary: '#3B82F6', // Blue 500
                        accent: '#F59E0B', // Amber 500
                        dark: '#0F172A', // Slate 900
                        softblue: '#EFF6FF', // Blue 50
                    },
                    boxShadow: {
                        'soft': '0 4px 20px -2px rgba(0, 0, 0, 0.05)',
                        'glow': '0 0 15px rgba(37, 99, 235, 0.3)',
                    },
                    animation: {
                        'slide-up': 'slideUp 0.4s ease-out forwards',
                        'shake': 'shake 0.5s cubic-bezier(.36,.07,.19,.97) both',
                    },
                    keyframes: {
                        slideUp: {
                            '0%': { opacity: 0, transform: 'translateY(20px)' },
                            '100%': { opacity: 1, transform: 'translateY(0)' },
                        },
                        shake: {
                            '10%, 90%': { transform: 'translate3d(-1px, 0, 0)' },
                            '20%, 80%': { transform: 'translate3d(2px, 0, 0)' },
                            '30%, 50%, 70%': { transform: 'translate3d(-4px, 0, 0)' },
                            '40%, 60%': { transform: 'translate3d(4px, 0, 0)' }
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* Staggered Animation Delays */
        .delay-100 { animation-delay: 100ms; }
        .delay-200 { animation-delay: 200ms; }
        .delay-300 { animation-delay: 300ms; }

        .error-message {
            color: #dc2626;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-600 font-sans antialiased flex flex-col min-h-screen selection:bg-blue-100 selection:text-blue-900">

    {{-- NAVBAR --}}
    <nav class="fixed top-0 w-full z-40 transition-all duration-300 bg-white/80 backdrop-blur-md border-b border-white/50" id="navbar">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            {{-- Logo --}}
            <a href="{{ url('/') }}" class="flex items-center gap-2 transition transform hover:scale-105 active:scale-95">
                <img src="{{ asset('images/dongiv-logo.png') }}" alt="DonasiKita" class="h-10">
            </a>

            {{-- Desktop Menu --}}
            <div class="hidden md:flex items-center space-x-1">
                <a href="{{ url('/') }}" class="px-4 py-2 text-sm font-semibold text-slate-600 hover:text-primary hover:bg-blue-50 rounded-full transition-all">Beranda</a>
                <a href="{{ url('/') }}#donasi" class="px-4 py-2 text-sm font-semibold text-slate-600 hover:text-primary hover:bg-blue-50 rounded-full transition-all">Donasi</a>
                <a href="{{ route('volunteer.landing') }}" class="px-4 py-2 text-sm font-semibold text-slate-600 hover:text-primary hover:bg-blue-50 rounded-full transition-all">Relawan</a>
                <a href="{{ url('/') }}#cara-kerja" class="px-4 py-2 text-sm font-semibold text-slate-600 hover:text-primary hover:bg-blue-50 rounded-full transition-all">Cara Kerja</a>

                <div class="h-6 w-px bg-slate-200 mx-2"></div>

                {{-- Guest Buttons --}}
                <div class="flex items-center gap-3 ml-4">
                    <a href="{{ route('register') }}" class="px-5 py-2.5 text-sm font-bold text-slate-600 hover:text-primary transition-colors">
                        Daftar
                    </a>
                    <a href="{{ route('login') }}" class="px-6 py-2.5 text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 rounded-full shadow-lg shadow-blue-500/30 transition-all transform hover:-translate-y-0.5 active:translate-y-0">
                        Masuk
                    </a>
                </div>
            </div>

            {{-- Mobile Button --}}
            <button id="mobileMenuBtn" class="md:hidden p-2 text-slate-600 hover:text-primary transition-colors">
                <i class="fas fa-bars text-2xl"></i>
            </button>
        </div>

        {{-- Mobile Menu --}}
        <div id="mobileMenu" class="hidden md:hidden bg-white border-t border-slate-100 absolute w-full shadow-lg">
            <div class="flex flex-col p-4 space-y-3">
                <a href="{{ url('/') }}" class="px-4 py-3 rounded-xl hover:bg-blue-50 font-medium text-slate-700">Beranda</a>
                <a href="{{ url('/') }}#donasi" class="px-4 py-3 rounded-xl hover:bg-blue-50 font-medium text-slate-700">Donasi</a>
                <a href="{{ route('volunteer.campaigns.index') }}" class="px-4 py-3 rounded-xl hover:bg-blue-50 font-medium text-slate-700">Relawan</a>

                <div class="grid grid-cols-2 gap-3 mt-2">
                    <a href="{{ route('register') }}" class="text-center py-3 rounded-xl border border-slate-200 font-bold text-slate-600">Daftar</a>
                    <a href="{{ route('login') }}" class="text-center py-3 rounded-xl bg-primary text-white font-bold">Masuk</a>
                </div>
            </div>
        </div>
    </nav>

    {{-- MAIN CONTENT --}}
    <main class="flex-grow pt-20">
        <div class="min-h-[calc(100vh-5rem)] flex items-center justify-center px-4">
            <div class="w-full max-w-md">
                <div class="bg-white rounded-3xl shadow-2xl shadow-blue-900/10 border border-white/50 p-8 animate-slide-up">
                    <div class="text-center mb-8">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-50 to-indigo-50 text-primary mb-4 shadow-inner ring-1 ring-blue-100">
                            <i class="fas fa-sign-in-alt text-2xl"></i>
                        </div>
                        <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">Selamat Datang Kembali</h2>
                        <p class="text-slate-500 mt-2">Masuk untuk melanjutkan kebaikan Anda.</p>
                    </div>

                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-2 ml-1">Email Address</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    required
                                    value="{{ old('email') }}"
                                    class="w-full pl-11 pr-4 py-3.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-primary focus:ring-4 focus:ring-blue-500/10 transition-all duration-300 placeholder:text-slate-400 font-medium"
                                    placeholder="nama@email.com"
                                >
                                @error('email')
                                    <div id="email-error" class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-2 ml-1">Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                    <i class="fas fa-lock"></i>
                                </div>
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    required
                                    class="w-full pl-11 pr-4 py-3.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-primary focus:ring-4 focus:ring-blue-500/10 transition-all duration-300 placeholder:text-slate-400 font-medium"
                                    placeholder="••••••••"
                                >
                                @error('password')
                                    <div id="password-error" class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <label class="flex items-center cursor-pointer group">
                                <input type="checkbox" name="remember" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary transition-colors">
                                <span class="ml-2 text-sm text-slate-600 font-medium group-hover:text-primary transition-colors">Ingat Saya</span>
                            </label>
                            <a href="#" class="text-sm text-slate-500 hover:text-primary font-bold transition-colors">Lupa Password?</a>
                        </div>

                        <button
                            type="submit"
                            class="w-full py-3.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-blue-500/30 transform hover:-translate-y-0.5 active:translate-y-0 transition-all duration-300 flex items-center justify-center gap-2"
                        >
                            <span>Masuk Sekarang</span>
                            <i class="fas fa-arrow-right text-sm opacity-70"></i>
                        </button>
                    </form>

                    <div class="mt-8 pt-6 border-t border-slate-100 text-center">
                        <p class="text-sm text-slate-600">
                            Belum memiliki akun?
                            <a href="{{ route('register') }}" class="text-primary font-bold hover:text-indigo-600 hover:underline focus:outline-none transition-colors">
                                Daftar disini
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    {{-- FOOTER --}}
    <footer class="bg-[#0B1120] text-gray-300 pt-20 pb-10 border-t border-slate-800/50">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
                <div class="space-y-6">
                    <a href="{{ url('/') }}" class="flex items-center gap-2">
                        <img src="{{ asset('images/dongiv-logo.png') }}" alt="DonasiKita" class="h-9 brightness-0 invert opacity-90">
                    </a>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        Platform crowdfunding terpercaya yang menghubungkan kebaikan hati para donatur dengan ribuan cerita yang membutuhkan bantuan nyata.
                    </p>
                    <div class="flex gap-4 pt-2">
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-primary hover:text-white hover:-translate-y-1 transition-all duration-300 ring-1 ring-slate-700 hover:ring-primary"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-primary hover:text-white hover:-translate-y-1 transition-all duration-300 ring-1 ring-slate-700 hover:ring-primary"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-primary hover:text-white hover:-translate-y-1 transition-all duration-300 ring-1 ring-slate-700 hover:ring-primary"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>

                <div>
                    <h3 class="font-bold text-white text-lg mb-6">Jelajahi</h3>
                    <ul class="space-y-4 text-sm text-slate-400">
                        <li><a href="{{ url('/') }}" class="hover:text-primary hover:pl-2 transition-all duration-300 flex items-center gap-2"><i class="fas fa-chevron-right text-xs opacity-50"></i> Beranda</a></li>
                        <li><a href="#donasi" class="hover:text-primary hover:pl-2 transition-all duration-300 flex items-center gap-2"><i class="fas fa-chevron-right text-xs opacity-50"></i> Donasi Sekarang</a></li>
                        <li><a href="{{ route('volunteer.campaigns.index') }}" class="hover:text-primary hover:pl-2 transition-all duration-300 flex items-center gap-2"><i class="fas fa-chevron-right text-xs opacity-50"></i> Jadi Relawan</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-bold text-white text-lg mb-6">Dukungan</h3>
                    <ul class="space-y-4 text-sm text-slate-400">
                        <li><a href="#" class="hover:text-primary hover:pl-2 transition-all duration-300 flex items-center gap-2"><i class="fas fa-chevron-right text-xs opacity-50"></i> Tentang Kami</a></li>
                        <li><a href="#" class="hover:text-primary hover:pl-2 transition-all duration-300 flex items-center gap-2"><i class="fas fa-chevron-right text-xs opacity-50"></i> Pusat Bantuan</a></li>
                        <li><a href="#" class="hover:text-primary hover:pl-2 transition-all duration-300 flex items-center gap-2"><i class="fas fa-chevron-right text-xs opacity-50"></i> Kebijakan Privasi</a></li>
                    </ul>
                </div>

                <div class="bg-slate-800/50 p-6 rounded-2xl border border-slate-700/50">
                    <h3 class="font-bold text-white mb-2">Kabar Kebaikan</h3>
                    <p class="text-xs text-slate-400 mb-4">Dapatkan update penyaluran donasi setiap minggunya.</p>
                    <form action="#" class="space-y-2">
                        <div class="relative">
                            <i class="fas fa-envelope absolute left-3 top-3 text-slate-500"></i>
                            <input type="email" placeholder="Alamat email..." class="w-full pl-9 pr-4 py-2.5 rounded-xl text-sm bg-slate-900 border border-slate-700 text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all placeholder:text-slate-600">
                        </div>
                        <button class="w-full py-2.5 bg-primary hover:bg-blue-600 text-white text-sm font-bold rounded-xl transition-colors shadow-lg shadow-blue-900/20">Berlangganan</button>
                    </form>
                </div>
            </div>

            <div class="border-t border-slate-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-sm text-slate-500">&copy; {{ date('Y') }} DonasiKita. Hak Cipta Dilindungi.</p>
                <div class="flex gap-6 text-sm text-slate-500 font-medium">
                    <a href="#" class="hover:text-white transition-colors">Syarat & Ketentuan</a>
                    <a href="#" class="hover:text-white transition-colors">Privasi</a>
                </div>
            </div>
        </div>
    </footer>

    {{-- MAIN SCRIPT --}}
    <script>
        // Mobile Menu Logic
        document.addEventListener('DOMContentLoaded', function() {
            const mobileBtn = document.getElementById('mobileMenuBtn');
            const mobileMenu = document.getElementById('mobileMenu');
            if (mobileBtn && mobileMenu) {
                mobileBtn.addEventListener('click', () => {
                    mobileMenu.classList.toggle('hidden');
                });
            }

            // Navbar Scroll Effect
            const navbar = document.getElementById('navbar');
            if (navbar) {
                window.addEventListener('scroll', () => {
                    if (window.scrollY > 10) {
                        navbar.classList.add('shadow-soft');
                    } else {
                        navbar.classList.remove('shadow-soft');
                    }
                });
            }
        });

    </script>
</body>
</html>