<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - NotezQue</title> <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 font-sans antialiased">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-lg flex-shrink-0 hidden md:flex flex-col">
            <div class="p-6 border-b border-gray-200">
                <h1 class="text-2xl font-bold text-blue-600 flex items-center space-x-2">
                    <img src="{{ $kontenStatis['site_logo'] ?? 'logo.png' }}" alt="{{ $kontenStatis['site_name'] ?? 'Notezque' }} Logo" class="h-8 w-auto">
                    <span>{{ $kontenStatis['site_name'] ?? 'NotezQue' }}</span>
                </h1>
                <p class="text-xs text-gray-500 mt-1">{{ $kontenStatis['site_tagline'] ?? 'Productivity Organizer' }}</p>
            </div>
            
            <nav class="flex-1 p-4 space-y-2">
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center px-4 py-3 rounded-lg font-medium transition-colors {{ Request::is('admin/dashboard') || Request::is('admin') ? 'text-blue-600 bg-blue-50' : 'text-gray-700 hover:bg-gray-100' }}">
                    <i class="fas fa-chart-line w-5 mr-3"></i>
                    Dashboard
                </a>
                <a href="{{ route('admin.users.index') }}" 
                   class="flex items-center px-4 py-3 rounded-lg font-medium transition-colors {{ Request::is('admin/users*') ? 'text-blue-600 bg-blue-50' : 'text-gray-700 hover:bg-gray-100' }}">
                    <i class="fas fa-users w-5 mr-3"></i>
                    Pengguna
                </a>
                <a href="{{ route('admin.content.index') }}" 
                   class="flex items-center px-4 py-3 rounded-lg font-medium transition-colors {{ Request::is('admin/content*') ? 'text-blue-600 bg-blue-50' : 'text-gray-700 hover:bg-gray-100' }}">
                    <i class="fas fa-file-alt w-5 mr-3"></i>
                    Konten Statis
                </a>
                <a href="{{ route('admin.statistics') }}" 
                   class="flex items-center px-4 py-3 rounded-lg font-medium transition-colors {{ Request::is('admin/statistics*') ? 'text-blue-600 bg-blue-50' : 'text-gray-700 hover:bg-gray-100' }}">
                    <i class="fas fa-chart-bar w-5 mr-3"></i>
                    Statistik
                </a>
            </nav>

            <div class="p-4 border-t border-gray-200">
                <div class="bg-blue-50 rounded-lg p-4">
                    <h3 class="font-semibold text-sm text-gray-800 mb-1">Butuh Bantuan?</h3>
                    <p class="text-xs text-gray-600 mb-3">Hubungi tim support kami</p>
                    <button class="w-full bg-blue-600 text-white text-sm py-2 rounded-md hover:bg-blue-700 transition-colors">
                        Hubungi Support
                    </button>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Topbar -->
            <header class="bg-white shadow-sm border-b border-gray-200 z-10">
                <div class="flex items-center justify-between px-6 py-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">@yield('page-title', 'Dashboard')</h2>
                        <p class="text-sm text-gray-500">@yield('page-subtitle', 'Selamat datang kembali di NotezQue')</p>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <!-- Profil Admin -->
                        <div class="relative">
                            <button onclick="toggleProfileDropdown()" class="flex items-center space-x-3 pl-3 border-l border-gray-200 hover:bg-gray-100 p-2 rounded-lg transition-colors">
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-500">{{ Auth::user()->is_admin ? 'Administrator' : 'User' }}</p>
                                </div>
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white font-bold">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                </div>
                                <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50">
                                <div class="px-4 py-3 border-b border-gray-200">
                                    <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                                </div>

                                <div class="border-t border-gray-200 my-2"></div>
                                <form action="{{ route('logout') }}" method="POST" class="block">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                        <i class="fas fa-sign-out-alt mr-3 w-4"></i>
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto bg-gray-50 p-6">
                @yield('content')
            </main>
        </div>
    </div>
    
    <script>
        function toggleProfileDropdown() {
            const dropdown = document.getElementById('profileDropdown');
            dropdown.classList.toggle('hidden');
        }
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('profileDropdown');
            const button = event.target.closest('button[onclick="toggleProfileDropdown()"]');
            
            if (!button && dropdown && !dropdown.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });
    </script>
</body>
</html>