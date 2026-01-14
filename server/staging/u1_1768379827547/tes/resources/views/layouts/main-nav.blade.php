<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Interaktif | Notezque V6</title>
    <!-- Memuat Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Konfigurasi Tailwind untuk warna dan font (Sesuai Gaya Pastel Minimalis) -->
    <script>
        tailwind.config = {
            darkMode: 'class', // Sinkron dengan build config agar utilitas dark: tersedia
            theme: {
                extend: {
                    colors: {
                        'v4-background': '#FFFFFF',
                        'v4-surface': '#FFFFFF',
                        'v4-primary': '#3385ff',
                        'v4-secondary': '#A8A8E6',
                        'v4-pink': '#FFC0CB',
                        'v4-text': '#1F2937',
                        'v4-light': '#EDEFFF',
                        'v4-subtle': '#E5E7EB',
                        'v4-custom': '#33adff'
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    keyframes: {
                        'ping-slow': {
                            '75%, 100%': {
                                transform: 'scale(1.5)',
                                opacity: '0'
                            },
                        }
                    },
                    animation: {
                        'ping-slow': 'ping-slow 2s cubic-bezier(0, 0, 0.2, 1) infinite',
                    }
                }
            }
        }
    </script>
    <!-- Prevent FOUC: set kelas .dark lebih awal sebelum render body -->
    <script>
        (function() {
            try {
                const stored = localStorage.getItem('theme');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                const shouldDark = stored ? stored === 'dark' : prefersDark;
                if (shouldDark) {
                    document.documentElement.classList.add('dark');
                }
            } catch (e) {
                // Jika localStorage tidak tersedia, abaikan.
            }
        })();
    </script>
    <!-- Tambahkan Lucide Icons untuk kebutuhan Dashboard -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--tw-colors-v4-background);
            color: var(--tw-colors-v4-text);
            line-height: 1.6;
        }

        /* --- Sidebar Collapsible (Desktop Only) --- */
        .sidebar-expanded {
            width: 16rem;
            /* w-64 */
            padding-right: 1.5rem;
            /* p-6 */
            padding-left: 1.5rem;
            /* p-6 */
        }

        .sidebar-collapsed {
            width: 5rem;
            /* 80px */
            padding-right: 0.5rem;
            /* p-2 */
            padding-left: 0.5rem;
            /* p-2 */
        }

        .sidebar-collapsed .nav-text,
        .sidebar-collapsed .logo-text,
        .sidebar-collapsed .logo-img-expanded,
        .sidebar-collapsed .logo-header-expanded {
            display: none !important;
        }

        .sidebar-collapsed .sidebar-item {
            justify-content: center;
        }

        .sidebar-collapsed .logo-img-collapsed {
            display: block !important;
        }

        .sidebar-collapsed .logo-header-collapsed {
            display: flex !important;
            justify-content: center;
        }

        /* Menghapus border saat collapsed untuk tampilan minimalis */
        .sidebar-collapsed .border-b,
        .sidebar-collapsed .border-t {
            border-color: transparent !important;
        }


        /* --- Styling Sidebar Item --- */
        .sidebar-item {
            display: flex;
            align-items: center;
            padding: 0.75rem;
            border-radius: 0.75rem;
            /* rounded-xl */
            color: #6B7280;
            /* gray-500 */
            transition: all 0.2s ease-in-out;
            cursor: pointer;
            /* Ensure text wraps correctly in expanded mode */
            white-space: nowrap;
        }

        .sidebar-item:hover {
            background-color: var(--tw-colors-v4-light);
            color: var(--tw-colors-v4-primary);
            box-shadow: 0 4px 6px -1px rgba(140, 158, 255, 0.1), 0 2px 4px -2px rgba(140, 158, 255, 0.06);
            transform: translateY(-1px);
        }

        /* Styling Sidebar item aktif */
        .sidebar-active {
            background-color: rgb(239 246 255);
            color: var(--tw-colors-v4-primary) !important;
            font-weight: 600;
            box-shadow: 0 10px 15px -3px rgba(140, 158, 255, 0.3), 0 4px 6px -4px rgba(140, 158, 255, 0.1);
        }

        .sidebar-active svg {
            color: var(--tw-colors-v4-primary) !important;
        }

        /* Force color of icon when active, but match text color */
        .sidebar-active i[data-lucide] {
            color: var(--tw-colors-v4-primary) !important;
        }

        /* --- Styling Top Nav Icons --- */
        .top-nav-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 9999px;
            cursor: pointer;
            transition: background-color 0.2s, transform 0.1s;
        }

        .top-nav-icon:hover {
            background-color: var(--tw-colors-v4-subtle);
            transform: scale(1.05);
        }

        /* --- Scrollbar Minimalis (Aesthetic Scrollbar) --- */
        #main-content::-webkit-scrollbar {
            width: 6px;
            background: transparent;
        }

        #main-content::-webkit-scrollbar-thumb {
            background-color: #d1d5db;
            /* gray-300 */
            border-radius: 10px;
        }

        #main-content::-webkit-scrollbar-thumb:hover {
            background-color: #9ca3af;
            /* gray-400 */
        }

        /* --- Dark Mode Styles --- */
        .dark body {
            background-color: #1a1a1a;
            color: #e5e7eb;
        }

        .dark .bg-v4-surface {
            background-color: #2d2d2d !important;
        }

        .dark .bg-v4-background {
            background-color: #1a1a1a !important;
        }

        .dark .text-v4-text {
            color: #e5e7eb !important;
        }

        .dark .border-gray-100,
        .dark .border-gray-200 {
            border-color: #374151 !important;
        }

        .dark .text-gray-500 {
            color: #9ca3af !important;
        }

        .dark .text-gray-400 {
            color: #6b7280 !important;
        }

        .dark .hover\:bg-v4-subtle:hover {
            background-color: #374151 !important;
        }

        .dark .sidebar-item:hover {
            background-color: #374151 !important;
        }

        .dark .top-nav-icon:hover {
            background-color: #374151 !important;
        }

        .dark #profile-dropdown-menu,
        .dark #notif-dropdown {
            background-color: #2d2d2d !important;
            border-color: #374151 !important;
        }

        .dark .hover\:bg-v4-light:hover {
            background-color: #374151 !important;
        }

        /* --- Generic Utility Overrides (Gray & Slate families) --- */
        /* Backgrounds */
        .dark .bg-white {
            background-color: #2d2d2d !important;
        }

        .dark .bg-gray-50,
        .dark .bg-slate-50 {
            background-color: #262626 !important;
        }

        .dark .bg-gray-100,
        .dark .bg-slate-100 {
            background-color: #2f2f2f !important;
        }

        .dark .bg-gray-200,
        .dark .bg-slate-200 {
            background-color: #383838 !important;
        }

        .dark .bg-gray-300,
        .dark .bg-slate-300 {
            background-color: #404040 !important;
        }

        /* Text (elevated scale) */
        .dark .text-gray-900,
        .dark .text-slate-900 {
            color: #f8fafc !important;
        }

        .dark .text-gray-800,
        .dark .text-slate-800 {
            color: #f1f5f9 !important;
        }

        .dark .text-gray-700,
        .dark .text-slate-700 {
            color: #e2e8f0 !important;
        }

        .dark .text-gray-600,
        .dark .text-slate-600 {
            color: #cbd5e1 !important;
        }

        .dark .text-gray-500,
        .dark .text-slate-500 {
            color: #94a3b8 !important;
        }

        .dark .text-gray-400,
        .dark .text-slate-400 {
            color: #64748b !important;
        }

        /* Borders & Dividers */
        .dark .border-gray-100,
        .dark .border-slate-100 {
            border-color: #2f2f2f !important;
        }

        .dark .border-gray-200,
        .dark .border-slate-200 {
            border-color: #383838 !important;
        }

        .dark .border-gray-300,
        .dark .border-slate-300 {
            border-color: #404040 !important;
        }

        .dark .divide-gray-100>*+*,
        .dark .divide-slate-100>*+* {
            border-color: #2f2f2f !important;
        }

        /* Hover states unify for subtle surfaces */
        .dark .hover\:bg-gray-50:hover,
        .dark .hover\:bg-slate-50:hover {
            background-color: #313131 !important;
        }

        .dark .hover\:bg-gray-100:hover,
        .dark .hover\:bg-slate-100:hover {
            background-color: #3a3a3a !important;
        }

        .dark .hover\:bg-gray-200:hover,
        .dark .hover\:bg-slate-200:hover {
            background-color: #454545 !important;
        }

        /* Form elements (common) */
        .dark input[type="text"],
        .dark input[type="email"],
        .dark input[type="password"],
        .dark textarea,
        .dark select {
            background-color: #2d2d2d !important;
            color: #e5e7eb !important;
            border-color: #404040 !important;
        }

        .dark input::placeholder,
        .dark textarea::placeholder {
            color: #64748b !important;
        }

        .dark input:focus,
        .dark textarea:focus,
        .dark select:focus {
            outline: none;
            border-color: #3385ff !important;
            box-shadow: 0 0 0 1px #3385ff40;
        }

        /* Buttons (semantic adjustments) */
        .dark .btn-primary {
            background-color: #3385ff !important;
            color: #fff !important;
        }

        .dark .btn-secondary {
            background-color: #4b5563 !important;
            color: #e5e7eb !important;
        }

        .dark .btn-danger {
            background-color: #dc2626 !important;
            color: #fff !important;
        }

        .dark .btn-primary:hover {
            background-color: #1d72e8 !important;
        }

        .dark .btn-secondary:hover {
            background-color: #64748b !important;
        }

        .dark .btn-danger:hover {
            background-color: #b91c1c !important;
        }

        /* Table adjustments */
        .dark table {
            color: #e5e7eb;
        }

        .dark thead {
            background-color: #2d2d2d;
        }

        .dark tbody tr {
            border-color: #383838;
        }

        .dark tbody tr:hover {
            background-color: #313131;
        }

        /* Code / pre blocks */
        .dark pre,
        .dark code {
            background-color: #262626 !important;
            color: #e5e7eb !important;
        }

        .dark pre {
            border: 1px solid #383838 !important;
        }

        /* Cards generic */
        .dark .card,
        .dark .panel,
        .dark .widget {
            background-color: #2d2d2d !important;
            border-color: #383838 !important;
        }

        /* Utility for smooth transitions across newly added elements */
        .dark .bg-white,
        .dark .bg-gray-50,
        .dark .bg-gray-100,
        .dark .bg-slate-50,
        .dark .bg-slate-100,
        .dark .card,
        .dark .panel,
        .dark input,
        .dark textarea,
        .dark select {
            transition: background-color .3s ease, color .3s ease, border-color .3s ease;
        }

        /* Global Dark Mode Styles for Content Areas - Match Topbar */
        .dark main {
            background-color: #2d2d2d !important;
            /* Sama dengan topbar */
        }

        .dark .bg-white {
            background-color: #3d3d3d !important;
            /* Card lebih terang sedikit dari background */
        }

        .dark .bg-slate-50 {
            background-color: #404040 !important;
        }

        .dark .bg-slate-100 {
            background-color: #4a4a4a !important;
        }

        .dark .text-slate-800 {
            color: #f8fafc !important;
            /* White untuk heading */
        }

        .dark .text-slate-700 {
            color: #f1f5f9 !important;
        }

        .dark .text-slate-600 {
            color: #e2e8f0 !important;
        }

        .dark .text-slate-500 {
            color: #cbd5e1 !important;
        }

        .dark .text-slate-400 {
            color: #94a3b8 !important;
        }

        .dark .border-slate-200 {
            border-color: #4a4a4a !important;
        }

        .dark .border-slate-300 {
            border-color: #525252 !important;
        }

        .dark .divide-slate-100>*+* {
            border-color: #4a4a4a !important;
        }

        /* Hover states untuk dark mode */
        .dark .hover\:bg-slate-50:hover {
            background-color: #404040 !important;
        }

        .dark .hover\:bg-blue-50:hover {
            background-color: rgba(59, 130, 246, 0.1) !important;
        }

        .dark .hover\:bg-purple-50:hover {
            background-color: rgba(168, 85, 247, 0.1) !important;
        }

        .dark .hover\:bg-green-50:hover {
            background-color: rgba(34, 197, 94, 0.1) !important;
        }

        /* Card and Shadow Effects */
        .dark .shadow-sm,
        .dark .shadow-md,
        .dark .shadow-lg,
        .dark .shadow-xl {
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.5) !important;
        }

        /* Smooth transition untuk dark mode */
        body,
        aside,
        header,
        main,
        footer,
        .bg-v4-surface,
        .bg-v4-background,
        .sidebar-item,
        .top-nav-icon {
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }
    </style>
</head>

<body class="antialiased flex flex-col h-screen">

    <div class="flex flex-1 overflow-hidden">
        <!-- 1. Sidebar (Navigasi Kiri) -->
        <aside id="sidebar"
            class="bg-white dark:bg-[#2d2d2d] p-4 shadow-2xl z-20 transition-[width,transform,padding] duration-300 ease-in-out transform -translate-x-full fixed md:relative md:translate-x-0 h-full border-r border-slate-200/60 dark:border-slate-700/50 flex flex-col sidebar-collapsed backdrop-blur-sm">
            <!-- CATATAN: Status awal diubah ke 'sidebar-collapsed' -->

            <!-- Logo Header -->
            <div id="logo-toggle-area"
                class="mb-8 flex flex-col cursor-pointer group md:hover:bg-gradient-to-r md:hover:from-blue-50 md:hover:to-purple-50 md:p-2 md:-mx-1 md:rounded-2xl transition-all duration-300">

                <!-- Expanded Header -->
                <div id="logo-header-expanded"
                    class="flex items-center space-x-3 p-4 border-b border-slate-200/60 logo-header-expanded">
                    <!-- Gunakan placeholder image karena asset('logo.png') tidak tersedia di sini -->
                    <div class="relative">
                        <div class="absolute inset-0 bg-blue-500 rounded-xl blur-md opacity-20 group-hover:opacity-30 transition-opacity"></div>
                        <img src="{{ $kontenStatis['site_logo'] ?? 'logo.png' }}" alt="{{ $kontenStatis['site_name'] ?? 'Notezque' }} Logo"
                            class="relative h-11 w-auto transition-transform duration-500 group-hover:scale-105 logo-img-expanded drop-shadow-lg" />
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent logo-text">{{ $kontenStatis['site_name'] ?? 'NotezQue' }}</span>
                        <span class="text-[10px] text-slate-500 font-medium logo-text">{{ $kontenStatis['site_tagline'] ?? 'Productivity Hub' }}</span>
                    </div>
                </div>

                <!-- Collapsed Header (Hidden by default, hanya menampilkan ikon) -->
                <div id="logo-header-collapsed" class="hidden justify-center logo-header-collapsed">
                    <div class="relative">
                        <div class="absolute inset-0 bg-blue-500 rounded-xl blur-md opacity-20 group-hover:opacity-40 transition-opacity"></div>
                        <img src="{{ $kontenStatis['site_logo'] ?? 'logo.png' }}" alt="{{ $kontenStatis['site_name'] ?? 'Notezque' }} Logo Collapsed"
                            class="relative h-11 w-11 rounded-xl logo-img-collapsed transition-transform duration-500 group-hover:scale-110 drop-shadow-lg" />
                    </div>
                </div>

            </div>

            <!-- Menu Navigasi -->
            <nav class="flex-grow space-y-1.5">
                <div class="nav-text px-3 mb-3">
                    <p class="text-xs font-semibold text-slate-400 dark:text-slate-300 uppercase tracking-wider">Menu Utama</p>
                </div>

                <!-- Dashboard (Aktif) -->
                <a href="{{url('/dashboard')}}" id="nav-dashboard" class="sidebar-item sidebar-active group relative">
                    <i data-lucide="layout-dashboard" class="w-5 h-5 transition-colors shrink-0"></i>
                    <span class="nav-text ml-3 h-5 font-medium">Dashboard</span>
                    <div class="nav-text absolute right-3 w-1.5 h-1.5 bg-blue-500 rounded-full opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </a>

                <!-- Jadwal Acara -->
                <a href="{{ url('/kalender') }}" id="nav-courses" class="sidebar-item group relative">
                    <i data-lucide="calendar" class="w-5 h-5 transition-colors shrink-0"></i>
                    <span class="nav-text ml-3 h-5 font-medium">Kalender Acara</span>
                    <div class="nav-text absolute right-3 w-1.5 h-1.5 bg-blue-500 rounded-full opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </a>

                <!-- Kolaborasi -->
                <a href="{{ url('/tugas') }}" id="nav-chats" class="sidebar-item group relative">
                    <i data-lucide="file-check-2" class="w-5 h-5 transition-colors shrink-0"></i>
                    <span class="nav-text ml-3 h-5 font-medium">Kelola Tugas</span>
                    <div class="nav-text absolute right-3 w-1.5 h-1.5 bg-blue-500 rounded-full opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </a>

                <!-- Materi -->
                <a href="{{ url('/materi') }}" id="nav-grades" class="sidebar-item group relative">
                    <i data-lucide="brain-circuit" class="w-5 h-5 transition-colors shrink-0"></i>
                    <span class="nav-text ml-3 h-5 font-medium">File Materi</span>
                    <div class="nav-text absolute right-3 w-1.5 h-1.5 bg-blue-500 rounded-full opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </a>

                <!-- Jadwal -->
                <a href="{{ url('/catatan') }}" id="nav-schedule" class="sidebar-item group relative">
                    <i data-lucide="notebook-pen" class="w-5 h-5 transition-colors shrink-0"></i>
                    <span class="nav-text ml-3 h-5 font-medium">Catatan</span>
                    <div class="nav-text absolute right-3 w-1.5 h-1.5 bg-blue-500 rounded-full opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </a>
            </nav>

            <!-- Menu Pengaturan Bawah -->
            <div class="mt-auto pt-6 border-t border-slate-200/60">
                <div class="nav-text px-3 mb-3">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Pengaturan</p>
                </div>

                <!-- Dark Mode Toggle -->
                <button id="dark-mode-toggle" class="sidebar-item w-full group relative" aria-label="Toggle dark mode">
                    <i data-lucide="moon" id="dark-icon" class="w-5 h-5 transition-colors shrink-0"></i>
                    <i data-lucide="sun" id="light-icon" class="w-5 h-5 transition-colors shrink-0 hidden"></i>
                    <span class="nav-text ml-3 h-5 font-medium">Mode Gelap</span>
                    <div class="nav-text absolute right-3 w-1.5 h-1.5 bg-blue-500 rounded-full opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </button>

                <form action="{{ route('logout') }}" method="POST" class="mt-1.5">
                    @csrf
                    <button type="submit" class="sidebar-item w-full group relative hover:bg-red-50 hover:text-red-600">
                        <i data-lucide="log-out" class="w-5 h-5 transition-colors shrink-0"></i>
                        <span class="nav-text ml-3 h-5 font-medium">Logout</span>
                        <div class="nav-text absolute right-3 w-1.5 h-1.5 bg-red-500 rounded-full opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Konten Utama (Top Nav + Main Content) -->
        <div id="main-container" class="flex-1 flex flex-col overflow-hidden transition-[margin-left] duration-300">

            <!-- 2. Top Navigation Bar (Nav Atas) -->
            <header class="bg-white shadow-sm sticky top-0 z-10 px-6 py-4 border-b border-slate-200/60 backdrop-blur-md">
                <div class="flex items-center justify-between">

                    <!-- Tombol Toggle Sidebar (Hanya di Mobile) -->
                    <button id="sidebar-toggle-mobile"
                        class="md:hidden text-v4-text mr-4 p-2.5 rounded-xl hover:bg-slate-100 transition-all duration-200 hover:shadow-md">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>

                    <!-- Nama Halaman -->
                    <div class="mr-auto">
                        @if (! View::hasSection('disableGlobalVariabel')) 
                        <h1 class="text-2xl font-bold text-slate-800">@yield('title', 'Ganti ini')</h1>
                        <p class="text-xs text-slate-500 font-medium mt-0.5">@yield('subtitle', 'Kelola produktivitas Anda')</p>
                        @endif
                    </div>

                    <!-- Ikon Notifikasi, Profil, dan Edit (Kanan) -->
                    <div class="flex items-center space-x-3">

                        @php
                            $user = auth()->user();
                            $initial = $user && $user->name ? strtoupper(substr($user->name, 0,strlen($user->name))) : 'U';
                            $profileUrl = ($user && $user->profile_photo)
                                ? asset('storage/' . $user->profile_photo)
                                : "https://placehold.co/120x120/6366F1/FFFFFF?text={$initial}";
                        @endphp
                        <!-- Profile Dropdown Trigger -->
                        <div id="profile-dropdown-trigger" class="relative">
                            <div class="flex items-center space-x-2 ml-4 cursor-pointer p-1 rounded-full hover:bg-v4-subtle transition duration-200"
                                aria-expanded="false" aria-controls="profile-dropdown-menu">
                                <img
                                    src="{{ $profileUrl }}"
                                    alt="Profile Picture"
                                    class="w-10 h-10 rounded-full shadow-xl object-cover"
                                    loading="lazy">
                                <span class="text-sm font-medium text-v4-text hidden lg:inline">Halo, {{ $initial }}</span>
                                <i data-lucide="chevron-down"
                                    class="w-4 h-4 text-gray-500 hidden lg:inline transition-transform duration-200"
                                    id="profile-arrow"></i>
                            </div>

                            <!-- Profile Dropdown Menu -->
                            <div id="profile-dropdown-menu"
                                class="absolute right-0 mt-3 w-48 bg-v4-surface rounded-xl shadow-xl border border-gray-100 py-1 opacity-0 pointer-events-none transition-opacity duration-200 transform origin-top-right scale-95"
                                role="menu" aria-orientation="vertical">
                                <a href="{{ url('/profile') }}"
                                    class="px-4 py-2 text-sm text-gray-700 hover:bg-v4-light hover:text-v4-primary flex items-center transition duration-150">
                                    <i data-lucide="user" class="w-4 h-4 mr-2"></i> Profil Saya
                                </a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full px-4 py-2 text-sm text-red-500 hover:bg-red-50 flex items-center transition duration-150">
                                        <i data-lucide="log-out" class="w-4 h-4 mr-2"></i> Keluar
                                    </button>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </header>

            <!-- Main Content Area (Overflow-y for content) -->
            <main id="main-content" class="flex-1 overflow-x-hidden overflow-y-auto  bg-v4-background">
                <!-- Tambahkan Konten Dashboard di sini -->
                @yield('content')

            </main>

            <!-- 3. Footer-->
            <footer class="bg-white py-4 border-t border-slate-200/60 w-full backdrop-blur-lg">
                <div class="max-w-7xl mx-auto px-6">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                        <!-- Copyright -->
                        <div class="text-center sm:text-left">
                            <p class="text-xs text-slate-600 font-medium">
                                &copy; 2025 <span class="font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">{{ $kontenStatis['site_name'] ?? 'NotezQue' }}</span>. {{ $kontenStatis['footer_copyright'] ?? 'All Rights Reserved.' }}
                            </p>
                            <p class="text-[10px] text-slate-400 mt-0.5">{{ $kontenStatis['footer_subtitle'] ?? 'Platform Manajemen Tugas dan Produktivitas Akademik' }}</p>
                        </div>

                        <!-- Quick Links -->
                        <div class="flex items-center gap-4 text-xs">
                            <a href="#" class="text-slate-500 hover:text-blue-600 font-medium transition-colors">Bantuan</a>
                            <span class="text-slate-300">•</span>
                            <a href="#" class="text-slate-500 hover:text-blue-600 font-medium transition-colors">Kebijakan Privasi</a>
                            <span class="text-slate-300">•</span>
                            <a href="#" class="text-slate-500 hover:text-blue-600 font-medium transition-colors">Tentang</a>
                        </div>
                    </div>
                </div>
            </footer>

        </div>
    </div>


    <!-- Overlay untuk mobile, menutup sidebar saat diklik -->
    <div id="sidebar-overlay"
        class="fixed inset-0 bg-black opacity-0 md:hidden z-10 transition-opacity duration-300 pointer-events-none">
    </div>


    <script>
        // Inisialisasi Lucide Icons
        lucide.createIcons();

        // Elemen Utama
        const sidebar = document.getElementById('sidebar');
        const mainContainer = document.getElementById('main-container');
        const toggleButtonMobile = document.getElementById('sidebar-toggle-mobile');
        const overlay = document.getElementById('sidebar-overlay');
        const logoToggleArea = document.getElementById('logo-toggle-area');

        // State Global Baru
        let isSidebarExpanded = false; // Status visual saat ini (collapsed/expanded)
        let isSidebarLocked = false; // Status permanen (true jika diklik/link aktif)


        // --- FUNGSI UTAMA UNTUK MENGATUR TAMPILAN DESKTOP ---
        function setDesktopLayout(expanded) {
            if (expanded) {
                // Expanded: Tampilkan teks, pakai kelas expanded
                sidebar.classList.replace('sidebar-collapsed', 'sidebar-expanded');
            } else {
                // Collapsed: Sembunyikan teks, pakai kelas collapsed
                sidebar.classList.replace('sidebar-expanded', 'sidebar-collapsed');
            }
            isSidebarExpanded = expanded;
        }

        // --- 1. Logika Toggle Sidebar untuk Mobile (Overlay Mode) ---
        function toggleMobileSidebar(open) {
            if (open) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.add('opacity-50', 'pointer-events-auto');
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.remove('opacity-50', 'pointer-events-auto');
            }
        }

        toggleButtonMobile.addEventListener('click', () => toggleMobileSidebar(sidebar.classList.contains('-translate-x-full')));
        overlay.addEventListener('click', () => toggleMobileSidebar(false));

        // Menutup sidebar saat link di mobile diklik
        document.querySelectorAll('#sidebar a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 768) {
                    toggleMobileSidebar(false);
                }
            });
        });


        // --- 2. Logika Toggle Sidebar untuk Desktop (Klik Logo -> Permanent Toggle) ---
        function toggleDesktopSidebar() {
            // Hanya aktifkan toggle di mode desktop
            if (window.innerWidth >= 768) {
                isSidebarLocked = !isSidebarLocked;
                // Terapkan layout berdasarkan status Lock yang baru
                setDesktopLayout(isSidebarLocked);
            }
        }

        // Event Listener: Klik pada area logo untuk Toggle Permanen
        logoToggleArea.addEventListener('click', toggleDesktopSidebar);


        // --- 3. Logika Hover Sidebar (Temporary Expand) ---
        sidebar.addEventListener('mouseover', () => {
            // Hanya aktifkan hover jika tidak sedang dikunci (locked) dan di mode desktop
            if (!isSidebarLocked && window.innerWidth >= 768) {
                setDesktopLayout(true); // Temporary expand
            }
        });

        sidebar.addEventListener('mouseleave', () => {
            // Hanya aktifkan mouseleave jika tidak sedang dikunci (locked) dan di mode desktop
            if (!isSidebarLocked && window.innerWidth >= 768) {
                setDesktopLayout(false); // Collapse back
            }
        });


        // --- 4. Logika Dropdown Profil (Tidak berubah) ---
        const profileTrigger = document.querySelector('#profile-dropdown-trigger > div');
        const profileMenu = document.getElementById('profile-dropdown-menu');
        const profileArrow = document.getElementById('profile-arrow');
        let isDropdownOpen = false;

        function toggleDropdown(open) {
            isDropdownOpen = open;
            if (open) {
                profileMenu.classList.remove('opacity-0', 'pointer-events-none', 'scale-95');
                profileMenu.classList.add('opacity-100', 'scale-100');
                profileArrow.classList.add('rotate-180');
                profileTrigger.setAttribute('aria-expanded', 'true');
            } else {
                profileMenu.classList.remove('opacity-100', 'scale-100');
                profileMenu.classList.add('opacity-0', 'pointer-events-none', 'scale-95');
                profileArrow.classList.remove('rotate-180');
                profileTrigger.setAttribute('aria-expanded', 'false');
            }
        }

        profileTrigger.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleDropdown(!isDropdownOpen);
        });

        document.addEventListener('click', (e) => {
            if (isDropdownOpen && !profileMenu.contains(e.target) && !profileTrigger.contains(e.target)) {
                toggleDropdown(false);
            }
        });

        // --- 5. Logika Navigasi & Active State (Berdasarkan URL) ---
        function setActiveLink() {
            const currentPath = window.location.pathname;
            document.querySelectorAll('.sidebar-item').forEach(item => {
                const itemPath = new URL(item.href).pathname;

                // Hapus state aktif dari semua item terlebih dahulu
                item.classList.remove('sidebar-active');

                // Tambahkan state aktif jika path cocok
                // Logika khusus untuk dashboard ('/')
                if (item.id === 'nav-dashboard' && (currentPath === '/dashboard' || currentPath === '/')) {
                    item.classList.add('sidebar-active');
                }
                // Untuk link lainnya
                else if (item.id !== 'nav-dashboard' && currentPath.startsWith(itemPath)) {
                    item.classList.add('sidebar-active');
                }
            });

            // Jika ada item yang aktif, pastikan sidebar expanded (jika di desktop)
            const hasActiveItem = document.querySelector('.sidebar-item.sidebar-active');
            if (hasActiveItem && window.innerWidth >= 768) {
                isSidebarLocked = true;
                setDesktopLayout(true);
            }
        }

        // Hapus event listener lama yang menggunakan preventDefault
        document.querySelectorAll('.sidebar-item').forEach(item => {
            item.addEventListener('click', function() {
                // Saat item diklik, kita hanya perlu memastikan sidebar terkunci jika di desktop
                if (window.innerWidth >= 768) {
                    isSidebarLocked = true;
                    setDesktopLayout(true);
                }
                // Navigasi akan berjalan normal karena tidak ada preventDefault
            });
        });


        // --- 6. Inisialisasi awal (Desktop Default Collapsed, kecuali ada item aktif) ---
        function initSidebar() {
            if (window.innerWidth >= 768) {
                // Desktop mode
                sidebar.classList.remove('fixed', '-translate-x-full');
                sidebar.classList.add('relative');

                // Atur layout sesuai status lock awal (biasanya false)
                setDesktopLayout(isSidebarLocked);

            } else {
                // Mobile mode
                sidebar.classList.add('fixed', '-translate-x-full');
                sidebar.classList.remove('relative', 'sidebar-expanded', 'sidebar-collapsed');
            }
            // Panggil fungsi untuk set link aktif setelah inisialisasi
            setActiveLink();
        }

        window.addEventListener('load', initSidebar);
        window.addEventListener('resize', initSidebar);
        // 🔔 Logika Dropdown Notifikasi
        const notifTrigger = document.getElementById('notif-trigger');
        const notifDropdown = document.getElementById('notif-dropdown');
        let isNotifOpen = false;

        notifTrigger.addEventListener('click', (e) => {
            e.stopPropagation();
            isNotifOpen = !isNotifOpen;
            if (isNotifOpen) {
                notifDropdown.classList.remove('opacity-0', 'pointer-events-none', 'scale-95');
                notifDropdown.classList.add('opacity-100', 'scale-100');
            } else {
                notifDropdown.classList.add('opacity-0', 'pointer-events-none', 'scale-95');
                notifDropdown.classList.remove('opacity-100', 'scale-100');
            }
        });

        // Tutup dropdown notifikasi kalau klik di luar
        document.addEventListener('click', (e) => {
            if (isNotifOpen && !notifTrigger.contains(e.target)) {
                notifDropdown.classList.add('opacity-0', 'pointer-events-none', 'scale-95');
                notifDropdown.classList.remove('opacity-100', 'scale-100');
                isNotifOpen = false;
            }
        });

        // --- Dark Mode Toggle Logic ---
        const darkModeToggle = document.getElementById('dark-mode-toggle');
        const darkIcon = document.getElementById('dark-icon');
        const lightIcon = document.getElementById('light-icon');
        const html = document.documentElement;

        // Check for saved theme preference or default to 'light'
        const currentTheme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');

        // Apply saved/system theme on page load (FOUC sudah diminimalkan lewat script di <head>)
        if (currentTheme === 'dark') {
            html.classList.add('dark');
        } else {
            html.classList.remove('dark');
        }
        // Sinkronisasi ikon & teks awal
        (function syncToggleUI() {
            const isDark = html.classList.contains('dark');
            darkIcon.classList.toggle('hidden', isDark);
            lightIcon.classList.toggle('hidden', !isDark);
            const navText = darkModeToggle.querySelector('.nav-text');
            if (navText) navText.textContent = isDark ? 'Mode Terang' : 'Mode Gelap';
        })();

        // Toggle dark mode on button click
        darkModeToggle.setAttribute('aria-pressed', html.classList.contains('dark'));
        darkModeToggle.addEventListener('click', function() {
            const nowDark = !html.classList.contains('dark');
            html.classList.toggle('dark', nowDark);
            localStorage.setItem('theme', nowDark ? 'dark' : 'light');
            darkModeToggle.setAttribute('aria-pressed', nowDark);
            // Update UI konsisten
            const navText = this.querySelector('.nav-text');
            if (navText) navText.textContent = nowDark ? 'Mode Terang' : 'Mode Gelap';
            darkIcon.classList.toggle('hidden', nowDark);
            lightIcon.classList.toggle('hidden', !nowDark);
            // Re-render ikon lucide
            lucide.createIcons();
        });
    </script>
</body>

</html>