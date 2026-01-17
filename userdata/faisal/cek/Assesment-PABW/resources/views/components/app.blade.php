@props(['title' => 'DonasiKita - Platform Kebaikan'])

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
                <a href="{{ url('/') }}#donasi" class="px-4 py-2 text-sm font-semibold text-slate-600 hover:text-primary hover:bg-blue-50 rounded-full transition-all">Donasi</a>
                <a href="{{ route('volunteer.landing') }}" class="px-4 py-2 text-sm font-semibold text-slate-600 hover:text-primary hover:bg-blue-50 rounded-full transition-all">Relawan</a>
                <a href="{{ url('/') }}#cara-kerja" class="px-4 py-2 text-sm font-semibold text-slate-600 hover:text-primary hover:bg-blue-50 rounded-full transition-all">Cara Kerja</a>

                <div class="h-6 w-px bg-slate-200 mx-2"></div>

                {{-- NOTIFICATION BELL SYSTEM (HANYA JIKA LOGIN) --}}
                @auth
                    <div class="relative mr-5">
                        <button id="notifButton" class="relative p-2 text-slate-600 hover:text-primary transition-colors focus:outline-none">
                            <i class="fas fa-bell text-xl"></i>
                            
                            {{-- Titik Merah (Hanya muncul jika ada notif belum dibaca) --}}
                            @if(auth()->user()->unreadNotifications->count() > 0)
                                <span id="notifBadge" class="absolute top-1 right-1 h-2.5 w-2.5 bg-red-500 rounded-full border-2 border-white animate-pulse"></span>
                            @endif
                        </button>

                        {{-- Dropdown Notifikasi --}}
                        <div id="notifDropdown" class="hidden absolute right-0 mt-3 w-80 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-50 overflow-hidden transform origin-top-right transition-all">
                            <div class="px-4 py-3 border-b border-slate-50 flex justify-between items-center">
                                <p class="text-sm font-bold text-slate-800">Notifikasi</p>
                                @if(auth()->user()->unreadNotifications->count() > 0)
                                    <span class="text-xs bg-blue-100 text-blue-600 px-2 py-0.5 rounded-full font-bold">
                                        {{ auth()->user()->unreadNotifications->count() }} Baru
                                    </span>
                                @endif
                            </div>

                            <div class="max-h-[300px] overflow-y-auto no-scrollbar">
                                @forelse(auth()->user()->notifications as $notification)
                                    <a href="{{ $notification->data['url'] ?? '#' }}" class="block px-4 py-3 hover:bg-slate-50 transition-colors border-b border-slate-50 last:border-0 {{ $notification->read_at ? 'opacity-60' : 'bg-blue-50/40' }}">
                                        <div class="flex gap-3">
                                            <div class="mt-1 flex-shrink-0">
                                                {{-- Icon Dinamis dari Database --}}
                                                <i class="{{ $notification->data['icon'] ?? 'fas fa-info-circle' }} {{ $notification->data['color'] ?? 'text-blue-500' }}"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-slate-700 leading-tight">{{ $notification->data['title'] }}</p>
                                                <p class="text-xs text-slate-500 mt-1 leading-relaxed line-clamp-2">{{ $notification->data['message'] }}</p>
                                                <p class="text-[10px] text-slate-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                    </a>
                                @empty
                                    <div class="px-4 py-8 text-center text-slate-400">
                                        <i class="fas fa-bell-slash text-2xl mb-2 opacity-50"></i>
                                        <p class="text-xs">Belum ada notifikasi</p>
                                    </div>
                                @endforelse
                            </div>
                            
                            @if(auth()->user()->notifications->count() > 0)
                                <div class="border-t border-slate-50 p-2 text-center bg-slate-50/50">
                                    <form action="{{ route('notifications.markAllRead') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-xs font-bold text-primary hover:text-blue-700 transition-colors">
                                            Tandai semua dibaca
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                @endauth

                    {{-- This section works for both server-side auth and API auth --}}
                    {{-- This section will be controlled by JavaScript based on auth state --}}
                    {{-- Server-side auth elements (will be shown if Laravel auth is active) --}}
                    @auth
                        <div id="serverAuthElements" class="relative ml-2">
                            <button id="profileButton" class="flex items-center gap-2 px-1 py-1 pr-3 rounded-full border border-slate-200 hover:border-blue-300 hover:bg-blue-50 transition-all focus:outline-none focus:ring-2 focus:ring-blue-100">
                                @if(auth()->check() && auth()->user()->photo)
                                    <img src="{{ asset('storage/' . auth()->user()->photo) }}"
                                         class="w-8 h-8 rounded-full object-cover shadow-md ring-2 ring-white"
                                         onerror="this.onerror=null; this.src='https://placehold.co/32x32?text={{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}';">
                                @elseif(auth()->check())
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 text-white flex items-center justify-center font-bold text-sm shadow-md ring-2 ring-white">
                                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                                    </div>
                                @else
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 text-white flex items-center justify-center font-bold text-sm shadow-md ring-2 ring-white">
                                        U
                                    </div>
                                @endif
                                <span class="text-sm font-semibold text-slate-700 max-w-[100px] truncate">{{ auth()->user()->name ?? 'User' }}</span>
                                <i class="fas fa-chevron-down text-xs text-slate-400"></i>
                            </button>
                            <div id="profileDropdown" class="hidden absolute right-0 mt-3 w-60 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-50 transform origin-top-right transition-all">
                                <div class="px-5 py-3 border-b border-slate-50 mb-2">
                                    <p class="text-xs text-slate-400 uppercase tracking-wider font-bold">Akun Masuk</p>
                                    <div class="flex items-center gap-3 mt-2">
                                        @if(auth()->check() && auth()->user()->photo)
                                            <img src="{{ asset('storage/' . auth()->user()->photo) }}"
                                                 class="w-10 h-10 rounded-full object-cover"
                                                 onerror="this.onerror=null; this.src='https://placehold.co/40x40?text={{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}';">
                                        @elseif(auth()->check())
                                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 text-white flex items-center justify-center font-bold">
                                                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                                            </div>
                                        @else
                                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 text-white flex items-center justify-center font-bold">
                                                U
                                            </div>
                                        @endif
                                        <div>
                                            <p class="text-sm font-bold text-slate-800 truncate">{{ auth()->user()->name ?? 'User' }}</p>
                                            <p class="text-xs text-slate-500 truncate">{{ auth()->user()->email ?? 'email@example.com' }}</p>
                                        </div>
                                    </div>
                                </div>
                               {{-- PERBAIKAN DI SINI: href="#" diganti route('profiles.index') --}}
                                <a href="{{ route('profiles.index') }}" class="group flex items-center px-4 py-2.5 text-sm text-slate-600 hover:bg-blue-50 hover:text-primary transition-colors mx-2 rounded-xl">
                                    <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 group-hover:bg-blue-100 group-hover:text-primary flex items-center justify-center mr-3 transition-colors">
                                        <i class="fas fa-user-circle"></i>
                                    </div>
                                    Profil & Riwayat
                                </a>
                                <div class="border-t border-slate-50 mt-2 pt-2 mx-2">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="group flex w-full items-center px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 rounded-xl transition-colors">
                                            <div class="w-8 h-8 rounded-lg bg-red-50 text-red-500 group-hover:bg-red-100 flex items-center justify-center mr-3 transition-colors">
                                                <i class="fas fa-sign-out-alt"></i>
                                            </div>
                                            Keluar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                    @else
                        {{-- Guest elements (login/register buttons) --}}
                        <div id="guestElements" class="flex items-center gap-3 ml-4">
                            <a href="{{ route('login') }}" class="px-5 py-2.5 text-sm font-bold text-slate-600 hover:text-primary transition-colors" id="loginLink">
                                Masuk
                            </a>
                            <a href="{{ route('register') }}" class="px-6 py-2.5 text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 rounded-full shadow-lg shadow-blue-500/30 transition-all transform hover:-translate-y-0.5 active:translate-y-0" id="registerLink">
                                Daftar Sekarang
                            </a>
                        </div>
                    @endauth

                    {{-- Elements for API authentication (will be managed by JavaScript) --}}
                    <div id="apiAuthElements" class="hidden">
                        <div id="profileDropdownContainer" class="relative ml-2">
                            <button id="apiProfileButton" class="flex items-center gap-2 px-1 py-1 pr-3 rounded-full border border-slate-200 hover:border-blue-300 hover:bg-blue-50 transition-all focus:outline-none focus:ring-2 focus:ring-blue-100">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 text-white flex items-center justify-center font-bold text-sm shadow-md ring-2 ring-white" id="profileInitial">
                                    U
                                </div>
                                <span class="text-sm font-semibold text-slate-700 max-w-[100px] truncate" id="profileName">User</span>
                                <i class="fas fa-chevron-down text-xs text-slate-400"></i>
                            </button>
                            <div id="apiProfileDropdown" class="hidden absolute right-0 mt-3 w-60 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-50 transform origin-top-right transition-all">
                                <div class="px-5 py-3 border-b border-slate-50 mb-2">
                                    <p class="text-xs text-slate-400 uppercase tracking-wider font-bold">Akun Masuk</p>
                                    <p class="text-sm font-bold text-slate-800 truncate" id="dropdownName">User</p>
                                    <p class="text-xs text-slate-500 truncate" id="dropdownEmail">user@example.com</p>
                                </div>
                                <a href="{{ route('profiles.index') }}" class="group flex items-center px-4 py-2.5 text-sm text-slate-600 hover:bg-blue-50 hover:text-primary transition-colors mx-2 rounded-xl">
                                    <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 group-hover:bg-blue-100 group-hover:text-primary flex items-center justify-center mr-3 transition-colors">
                                        <i class="fas fa-user-circle"></i>
                                    </div>
                                    Profil & Riwayat
                                </a>
                                <div class="border-t border-slate-50 mt-2 pt-2 mx-2">
                                    <button id="apiLogoutBtn" class="group flex w-full items-center px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 rounded-xl transition-colors">
                                        <div class="w-8 h-8 rounded-lg bg-red-50 text-red-500 group-hover:bg-red-100 flex items-center justify-center mr-3 transition-colors">
                                            <i class="fas fa-sign-out-alt"></i>
                                        </div>
                                        Keluar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="apiNotificationContainer" class="relative mr-5 hidden">
                        <button id="apiNotifButton" class="relative p-2 text-slate-600 hover:text-primary transition-colors focus:outline-none">
                            <i class="fas fa-bell text-xl"></i>
                            <span id="apiNotifBadge" class="absolute top-1 right-1 h-2.5 w-2.5 bg-red-500 rounded-full border-2 border-white animate-pulse hidden"></span>
                        </button>
                        <div id="apiNotifDropdown" class="hidden absolute right-0 mt-3 w-80 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-50 overflow-hidden transform origin-top-right transition-all">
                            <div class="px-4 py-3 border-b border-slate-50 flex justify-between items-center">
                                <p class="text-sm font-bold text-slate-800">Notifikasi</p>
                                <span id="apiNotifCount" class="text-xs bg-blue-100 text-blue-600 px-2 py-0.5 rounded-full font-bold"></span>
                            </div>
                            <div id="apiNotifList" class="max-h-[300px] overflow-y-auto no-scrollbar">
                                <!-- Notifications will be loaded here -->
                            </div>
                            <div class="border-t border-slate-50 p-2 text-center bg-slate-50/50">
                                <button id="markAllReadBtn" class="text-xs font-bold text-primary hover:text-blue-700 transition-colors">
                                    Tandai semua dibaca
                                </button>
                            </div>
                        </div>
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
                <a href="{{ url('/') }}#donasi" class="px-4 py-3 rounded-xl hover:bg-blue-50 font-medium text-slate-700">Donasi</a>
                <a href="{{ route('volunteer.campaigns.index') }}" class="px-4 py-3 rounded-xl hover:bg-blue-50 font-medium text-slate-700">Relawan</a>
                @auth
                        <a href="{{ route('profiles.index') }}" class="group flex items-center px-4 py-2.5 text-sm text-slate-600 hover:bg-blue-50 hover:text-primary transition-colors mx-2 rounded-xl">
                            <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 group-hover:bg-blue-100 group-hover:text-primary flex items-center justify-center mr-3 transition-colors">
                                <i class="fas fa-user-circle"></i>
                            </div>
                            Profil & Riwayat
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="w-full text-left px-4 py-3 rounded-xl text-red-500 font-bold hover:bg-red-50">Keluar</button>
                    </form>
                @else
                    <div id="mobileAuthButtons" class="grid grid-cols-2 gap-3 mt-2">
                        <a href="{{ route('login') }}" class="text-center py-3 rounded-xl border border-slate-200 font-bold text-slate-600" id="mobileLoginLink">Masuk</a>
                        <a href="{{ route('register') }}" class="text-center py-3 rounded-xl bg-primary text-white font-bold" id="mobileRegisterLink">Daftar</a>
                    </div>

                    {{-- Mobile Profile Menu for API Auth --}}
                    <div id="mobileProfileContainer" class="hidden">
                        <div class="px-4 py-3 border-b border-slate-50">
                            @if(auth()->check() && auth()->user()->photo)
                                <img src="{{ asset('storage/' . auth()->user()->photo) }}"
                                     class="w-12 h-12 rounded-full object-cover mx-auto mb-2"
                                     onerror="this.onerror=null; this.src='https://placehold.co/48x48?text={{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}';">
                            @elseif(auth()->check())
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 text-white flex items-center justify-center font-bold text-lg mx-auto mb-2">
                                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                                </div>
                            @endif
                            <p class="text-sm font-bold text-slate-800 text-center">{{ auth()->user()->name ?? 'User' }}</p>
                            <p class="text-xs text-slate-500 text-center">{{ auth()->user()->email ?? 'email@example.com' }}</p>
                        </div>
                        <a id="mobileProfileLink" href="{{ route('profiles.index') }}" class="group flex items-center px-4 py-2.5 text-sm text-slate-600 hover:bg-blue-50 hover:text-primary transition-colors mx-2 rounded-xl">
                            <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 group-hover:bg-blue-100 group-hover:text-primary flex items-center justify-center mr-3 transition-colors">
                                <i class="fas fa-user-circle"></i>
                            </div>
                            <span id="mobileProfileName">Profil & Riwayat</span>
                        </a>
                        <button id="mobileApiLogoutBtn" class="w-full text-left px-4 py-3 rounded-xl text-red-500 font-bold hover:bg-red-50 mt-2">Keluar</button>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    {{-- KONTEN UTAMA --}}
    <main class="flex-grow pt-20">
        {{ $slot }}
    </main>

    {{-- FOOTER PREMIUM --}}
    <footer class="bg-[#0B1120] text-gray-300 pt-20 pb-10 border-t border-slate-800/50">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
                {{-- Brand Column --}}
                <div class="space-y-6">
                    <a href="{{ url('/') }}" class="flex items-center gap-2">
                        {{-- Filter brightness-0 invert membuat logo hitam menjadi putih --}}
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

                {{-- Links 1 --}}
                <div>
                    <h3 class="font-bold text-white text-lg mb-6">Jelajahi</h3>
                    <ul class="space-y-4 text-sm text-slate-400">
                        <li><a href="#donasi" class="hover:text-primary hover:pl-2 transition-all duration-300 flex items-center gap-2"><i class="fas fa-chevron-right text-xs opacity-50"></i> Donasi Sekarang</a></li>
                        <li><a href="{{ route('volunteer.campaigns.index') }}" class="hover:text-primary hover:pl-2 transition-all duration-300 flex items-center gap-2"><i class="fas fa-chevron-right text-xs opacity-50"></i> Jadi Relawan</a></li>
                        <li><a href="#" class="hover:text-primary hover:pl-2 transition-all duration-300 flex items-center gap-2"><i class="fas fa-chevron-right text-xs opacity-50"></i> Kisah Sukses</a></li>
                    </ul>
                </div>

                {{-- Links 2 --}}
                <div>
                    <h3 class="font-bold text-white text-lg mb-6">Dukungan</h3>
                    <ul class="space-y-4 text-sm text-slate-400">
                        <li><a href="#" class="hover:text-primary hover:pl-2 transition-all duration-300 flex items-center gap-2"><i class="fas fa-chevron-right text-xs opacity-50"></i> Tentang Kami</a></li>
                        <li><a href="#" class="hover:text-primary hover:pl-2 transition-all duration-300 flex items-center gap-2"><i class="fas fa-chevron-right text-xs opacity-50"></i> Pusat Bantuan</a></li>
                        <li><a href="#" class="hover:text-primary hover:pl-2 transition-all duration-300 flex items-center gap-2"><i class="fas fa-chevron-right text-xs opacity-50"></i> Kebijakan Privasi</a></li>
                    </ul>
                </div>

                {{-- Newsletter --}}
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
                <p class="text-sm text-slate-500">&copy; {{ date('Y') }} DonGiv. Hak Cipta Dilindungi.</p>
                <div class="flex gap-6 text-sm text-slate-500 font-medium">
                    <a href="#" class="hover:text-white transition-colors">Syarat & Ketentuan</a>
                    <a href="#" class="hover:text-white transition-colors">Privasi</a>
                </div>
            </div>
        </div>
    </footer>
    
    {{-- 1. CDN SweetAlert2 (WAJIB ADA UNTUK POPUP) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- 2. MAIN SCRIPT --}}
    <script>
        // Function to check authentication state (both server-side and API)
        function updateAuthUI() {
            // Check for API token in localStorage
            const token = localStorage.getItem('auth_token');
            const userData = localStorage.getItem('user');

            // Get all UI containers
            const serverAuthElements = document.getElementById('serverAuthElements');
            const serverNotifContainer = document.getElementById('serverNotifContainer');
            const guestElements = document.getElementById('guestElements');
            const apiAuthElements = document.getElementById('apiAuthElements');
            const apiNotificationContainer = document.getElementById('apiNotificationContainer');
            const mobileAuthButtons = document.getElementById('mobileAuthButtons');
            const mobileProfileContainer = document.getElementById('mobileProfileContainer');

            if (token && userData) {
                try {
                    const user = JSON.parse(userData);

                    // Hide server-side auth elements (if they exist) since we're using API auth
                    if (serverAuthElements) serverAuthElements.classList.add('hidden');
                    if (serverNotifContainer) serverNotifContainer.classList.add('hidden');
                    if (guestElements) guestElements.classList.add('hidden');

                    // Show API authenticated UI
                    if (apiAuthElements) {
                        apiAuthElements.classList.remove('hidden');

                        // Update profile info in API elements
                        const profileInitial = document.getElementById('profileInitial');
                        const profileName = document.getElementById('profileName');
                        const dropdownName = document.getElementById('dropdownName');
                        const dropdownEmail = document.getElementById('dropdownEmail');

                        if (profileInitial) profileInitial.textContent = user.name ? user.name.charAt(0).toUpperCase() : 'U';
                        if (profileName) profileName.textContent = user.name || 'User';
                        if (dropdownName) dropdownName.textContent = user.name || 'User';
                        if (dropdownEmail) dropdownEmail.textContent = user.email || 'user@example.com';
                    }

                    if (apiNotificationContainer) apiNotificationContainer.classList.remove('hidden');

                    // Update mobile menu
                    if (mobileAuthButtons) mobileAuthButtons.classList.add('hidden');
                    if (mobileProfileContainer) {
                        mobileProfileContainer.classList.remove('hidden');

                        // Update mobile profile name
                        const mobileProfileName = document.getElementById('mobileProfileName');
                        if (mobileProfileName) mobileProfileName.textContent = (user.name || 'User') + ' - Profil & Riwayat';
                    }

                    return user;
                } catch (e) {
                    console.error('Error parsing user data:', e);
                    // If there's an error, clear the stored data
                    localStorage.removeItem('auth_token');
                    localStorage.removeItem('user');
                    return null;
                }
            } else {
                // Default behavior based on Laravel's server-side auth
                // If serverAuthElements exists (user is logged in via Laravel), show it
                // Otherwise, show guest elements
                if (serverAuthElements && !serverAuthElements.classList.contains('hidden')) {
                    // Server-side auth is active, hide API elements
                    if (apiAuthElements) apiAuthElements.classList.add('hidden');
                    if (apiNotificationContainer) apiNotificationContainer.classList.add('hidden');
                    if (mobileAuthButtons) mobileAuthButtons.classList.add('hidden');
                    if (mobileProfileContainer) mobileProfileContainer.classList.add('hidden');
                } else {
                    // No server-side auth, and no API token, show guest UI
                    if (serverAuthElements) serverAuthElements.classList.add('hidden');
                    if (serverNotifContainer) serverNotifContainer.classList.add('hidden');
                    if (guestElements) {
                        guestElements.classList.remove('hidden');
                    }
                    if (apiAuthElements) apiAuthElements.classList.add('hidden');
                    if (apiNotificationContainer) apiNotificationContainer.classList.add('hidden');
                    if (mobileAuthButtons) {
                        mobileAuthButtons.classList.remove('hidden');
                    }
                    if (mobileProfileContainer) mobileProfileContainer.classList.add('hidden');
                }
            }
        }

        // API Logout function
        async function apiLogout() {
            const token = localStorage.getItem('auth_token');

            if (token) {
                try {
                    await fetch('/api/logout', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': 'Bearer ' + token,
                            'X-Requested-With': 'XMLHttpRequest',
                        }
                    });
                } catch (e) {
                    console.error('Logout error:', e);
                    // Continue with local cleanup even if API call fails
                }
            }

            // Clear local storage
            localStorage.removeItem('auth_token');
            localStorage.removeItem('user');

            // Refresh page to update UI
            window.location.reload();
        }

        // Run check immediately and also after DOM loads
        document.addEventListener('DOMContentLoaded', function() {
            // Update authentication UI on page load
            updateAuthUI();

            // Set up API logout buttons
            const apiLogoutBtn = document.getElementById('apiLogoutBtn');
            const mobileApiLogoutBtn = document.getElementById('mobileApiLogoutBtn');

            if (apiLogoutBtn) {
                apiLogoutBtn.addEventListener('click', apiLogout);
            }

            if (mobileApiLogoutBtn) {
                mobileApiLogoutBtn.addEventListener('click', apiLogout);
            }

            // 1. Notification Dropdown Logic (API version)
            const apiNotifBtn = document.getElementById('apiNotifButton');
            const apiNotifMenu = document.getElementById('apiNotifDropdown');

            if (apiNotifBtn && apiNotifMenu) {
                apiNotifBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    apiNotifMenu.classList.toggle('hidden');
                });
            }

            // 2. Profile Dropdown Logic (API version) - for API profile button
            const apiProfileBtn = document.getElementById('apiProfileButton');
            const apiProfileMenu = document.getElementById('apiProfileDropdown');

            if (apiProfileBtn && apiProfileMenu) {
                apiProfileBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    apiProfileMenu.classList.toggle('hidden');
                });
            }

            // 2.5. Profile Dropdown Logic (Server-side version) - for server-side profile button
            const profileBtn = document.getElementById('profileButton');
            const profileMenu = document.getElementById('profileDropdown');

            if (profileBtn && profileMenu) {
                profileBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    profileMenu.classList.toggle('hidden');
                });
            }

            // 2.6. Notification Dropdown Logic (Server-side version) - for server-side notification button
            const notifBtn = document.getElementById('notifButton');
            const notifMenu = document.getElementById('notifDropdown');

            if (notifBtn && notifMenu) {
                notifBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    notifMenu.classList.toggle('hidden');
                });
            }

            // 3. Global Click Handler (Tutup dropdown saat klik di luar area)
            document.addEventListener('click', (e) => {
                if (apiNotifBtn && apiNotifMenu && !apiNotifBtn.contains(e.target) && !apiNotifMenu.contains(e.target)) {
                    apiNotifMenu.classList.add('hidden');
                }
                if (apiProfileBtn && apiProfileMenu && !apiProfileBtn.contains(e.target) && !apiProfileMenu.contains(e.target)) {
                    apiProfileMenu.classList.add('hidden');
                }
                // Tutup juga dropdown server-side jika ada
                if (profileBtn && profileMenu && !profileBtn.contains(e.target) && !profileMenu.contains(e.target)) {
                    profileMenu.classList.add('hidden');
                }
                // Tutup juga dropdown notifikasi server-side jika ada
                const notifBtn = document.getElementById('notifButton');
                const notifMenu = document.getElementById('notifDropdown');
                if (notifBtn && notifMenu && !notifBtn.contains(e.target) && !notifMenu.contains(e.target)) {
                    notifMenu.classList.add('hidden');
                }
            });

            // 4. Mobile Menu Logic
            const mobileBtn = document.getElementById('mobileMenuBtn');
            const mobileMenu = document.getElementById('mobileMenu');
            if (mobileBtn && mobileMenu) {
                mobileBtn.addEventListener('click', () => {
                    mobileMenu.classList.toggle('hidden');
                });
            }

            // 5. Navbar Scroll Effect
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

            // --- C. SWEETALERT POPUP LOGIC ---

            // Popup Sukses
            @if(session('success'))
                Swal.fire({
                    title: 'Berhasil!',
                    text: "{!! session('success') !!}",
                    imageUrl: 'https://cdn-icons-png.flaticon.com/512/148/148767.png', // Icon Checklist
                    imageWidth: 100,
                    imageHeight: 100,
                    imageAlt: 'Success Icon',
                    showConfirmButton: true,
                    confirmButtonText: 'Oke, Mengerti',
                    confirmButtonColor: '#4f46e5',
                    allowOutsideClick: false,
                    backdrop: `rgba(0,0,123,0.4)`,
                    customClass: {
                        popup: 'rounded-3xl shadow-2xl',
                        title: 'text-2xl font-bold text-slate-800',
                        confirmButton: 'rounded-xl px-6 py-2.5 font-bold'
                    }
                });
            @endif

            // Popup Error
            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Mohon Maaf',
                    text: "{!! session('error') !!}",
                    confirmButtonText: 'Tutup',
                    confirmButtonColor: '#ef4444',
                    customClass: {
                        popup: 'rounded-3xl shadow-xl',
                        confirmButton: 'rounded-xl px-6 py-2.5 font-bold'
                    }
                });
            @endif

            // Popup Info
            @if(session('info'))
                Swal.fire({
                    icon: 'info',
                    title: 'Informasi',
                    text: "{!! session('info') !!}",
                    confirmButtonColor: '#3b82f6',
                    customClass: { popup: 'rounded-3xl' }
                });
            @endif
        });

        // Also check on window load in case DOM is ready before script executes
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                updateAuthUI();
            });
        } else {
            // DOM is already loaded
            updateAuthUI();
        }

        // Function to calculate and update remaining days in real-time
        function updateRemainingDays() {
            const dayElements = document.querySelectorAll('.days-remaining');
            const now = new Date();

            dayElements.forEach(element => {
                const endDateStr = element.getAttribute('data-end-date');
                if (!endDateStr) return;

                // Parse the end date from the data attribute
                const endDate = new Date(endDateStr + ' 23:59:59'); // Set to end of the day
                const diffTime = endDate - now;
                const diffDays = Math.max(0, Math.ceil(diffTime / (1000 * 60 * 60 * 24)));

                // Update the display
                element.textContent = diffDays;
            });
        }

        // Initial update
        updateRemainingDays();

        // Update every minute
        setInterval(updateRemainingDays, 60000);
    </script>
</body>
</html>