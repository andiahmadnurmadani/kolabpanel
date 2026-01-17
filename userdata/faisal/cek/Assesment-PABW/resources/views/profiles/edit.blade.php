<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edit Profil - DonGiv</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <style>
        .profile-form-card {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            transition: all 0.3s ease;
        }
        .profile-form-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .text-gray-800 {
            color: #1f2937;
        }
        .text-gray-600 {
            color: #4b5563;
        }
        .text-gray-700 {
            color: #374151;
        }
        .bg-gradient-to-br.from-white.to-blue-50 {
            background: linear-gradient(to bottom right, #ffffff, #dbeafe);
        }
        .bg-gradient-to-r.from-blue-600.to-blue-700 {
            background: linear-gradient(to right, #2563eb, #1d4ed8);
        }
        .bg-gradient-to-r.from-blue-600.to-blue-700:hover {
            background: linear-gradient(to right, #1d4ed8, #1e40af);
        }
        .bg-gradient-to-r.from-green-50.to-emerald-50 {
            background: linear-gradient(to right, #f0fdf4, #ecfdf5);
        }
        .border-l-4.border-green-500 {
            border-left-color: #22c55e;
        }
        .text-green-700 {
            color: #15803d;
        }
        .bg-gradient-to-r.from-gray-500.to-gray-600 {
            background: linear-gradient(to right, #6b7280, #4b5563);
        }
        .bg-gradient-to-r.from-gray-600.to-gray-700 {
            background: linear-gradient(to right, #4b5563, #374151);
        }
        .shadow-xl {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .shadow-lg {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-cyan-50 text-gray-800">
    <!-- Header -->
    <header class="bg-white/80 backdrop-blur-sm shadow-sm fixed top-0 w-full z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-6 py-3 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <img src="{{ asset('images/dongiv-logo.png') }}" alt="DonGiv Logo" class="h-8">
            </div>

            <nav class="hidden md:flex items-center space-x-8 font-medium">
                <a href="{{ route('home') }}" class="text-gray-700 hover:text-primary transition">Beranda</a>
                <a href="{{ route('campaigns.all') }}" class="text-gray-700 hover:text-primary transition">Donasi</a>
                <a href="#" class="text-gray-700 hover:text-primary transition">Galang Dana</a>
                <a href="#" class="text-gray-700 hover:text-primary transition">Relawan</a>
                <a href="#" class="text-gray-700 hover:text-primary transition">Cara Kerja</a>
                <a href="#" class="text-gray-700 hover:text-primary transition">Tentang Kami</a>
            </nav>
            <div class="flex items-center space-x-3">
                <!-- Search bar -->
                <div class="hidden md:flex items-center">
                    <input type="text" placeholder="Cari sesuatu..." class="px-4 py-2 rounded-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                    <button class="ml-2 bg-primary text-white px-4 py-2 rounded-full hover:bg-blue-800">
                        <i class="fas fa-search"></i>
                    </button>
                </div>

                @auth
                <!-- Profile dropdown when user is logged in -->
                <div class="relative">
                    <button id="profileButton" class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-800 font-semibold hover:bg-blue-200 transition-colors" type="button">
                        <span class="font-bold">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    </button>
                    <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-50">
                        <a href="{{ route('profiles.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors">
                            <i class="fas fa-user mr-2"></i>Profil Saya
                        </a>
                        <form method="POST" action="{{ route('logout') }}" id="logout-form" style="display: none;">
                            @csrf
                        </form>
                        <a href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-red-600 transition-colors">
                            <i class="fas fa-sign-out-alt mr-2"></i>Keluar
                        </a>
                    </div>
                </div>
                @else
                <!-- Login and Register buttons when user is not logged in -->
                <a href="{{ route('login') }}" class="px-5 py-2 rounded-full font-semibold border border-primary text-primary hover:bg-primary hover:text-white transition-all duration-300">Masuk</a>
                <a href="{{ route('register') }}" class="px-5 py-2 rounded-full font-semibold bg-primary text-white hover:bg-blue-800 transition-all duration-300">Daftar</a>
                @endauth
            </div>
        </div>
    </header>

    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-cyan-50 pt-24 pb-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-800">Edit Profil</h1>
                <p class="text-gray-600">Perbarui informasi akun Anda</p>
            </div>

            <div class="profile-form-card bg-gradient-to-br from-white to-blue-50 rounded-2xl shadow-xl p-6 md:p-8 border border-gray-200/50 backdrop-blur-sm">
                @if(session('success'))
                    <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 text-green-700 px-6 py-4 rounded-xl shadow-md">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mr-4">
                                <i class="fas fa-check-circle text-2xl mt-1 text-green-600"></i>
                            </div>
                            <div>
                                <p class="font-bold text-lg">Sukses!</p>
                                <p class="">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('profiles.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Profile Picture Section -->
                    <div class="flex flex-col items-center mb-8">
                        <div class="relative">
                            @if($user->photo)
                                <img src="{{ asset('storage/' . $user->photo) }}"
                                     alt="Profile Photo"
                                     class="w-32 h-32 rounded-full object-cover border-4 border-blue-200 shadow-lg">
                            @else
                                <div class="w-32 h-32 rounded-full bg-gradient-to-r from-blue-400 to-blue-600 flex items-center justify-center text-white text-5xl font-bold border-4 border-blue-200 shadow-lg">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                            <label for="photo" class="absolute bottom-2 right-2 bg-white rounded-full p-2 shadow-lg cursor-pointer">
                                <i class="fas fa-camera text-blue-600"></i>
                                <input type="file" name="photo" id="photo" class="hidden" accept="image/*">
                            </label>
                        </div>
                        <p class="mt-3 text-sm text-gray-600">Klik kamera untuk mengganti foto</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                            <input type="text"
                                   name="name"
                                   id="name"
                                   value="{{ old('name', $user->name) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email"
                                   name="email"
                                   id="email"
                                   value="{{ old('email', $user->email) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror">
                            @error('email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon</label>
                            <input type="tel"
                                   name="phone"
                                   id="phone"
                                   value="{{ old('phone', $user->phone) }}"
                                   placeholder="Contoh: 081234567890"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('phone') border-red-500 @enderror">
                            @error('phone')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir</label>
                            <input type="date"
                                   name="birth_date"
                                   id="birth_date"
                                   value="{{ old('birth_date', $user->birth_date?->format('Y-m-d')) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('birth_date') border-red-500 @enderror">
                            @error('birth_date')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:space-x-4 space-y-4 sm:space-y-0">
                        <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 transform hover:scale-[1.02] shadow-lg">
                            <i class="fas fa-save mr-2"></i>Simpan Perubahan
                        </button>
                        <a href="{{ route('profiles.index') }}" class="flex-1 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 text-center">
                            <i class="fas fa-arrow-left mr-2"></i>Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-gray-300 py-12 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="text-center md:text-left">
                    <h4 class="text-xl font-bold text-white mb-4">DonGiv</h4>
                    <p class="text-sm">Creating positive change through transparent and effective charitable giving.</p>
                </div>
                <div class="text-center md:text-left">
                    <h5 class="font-semibold text-white mb-4">Explore</h5>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('home') }}" class="hover:text-white transition">Home</a></li>
                        <li><a href="{{ route('donation.details') }}" class="hover:text-white transition">Donations</a></li>
                        <li><a href="#" class="hover:text-white transition">Volunteer</a></li>
                        <li><a href="#" class="hover:text-white transition">About Us</a></li>
                    </ul>
                </div>
                <div class="text-center md:text-left">
                    <h5 class="font-semibold text-white mb-4">Legal</h5>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white transition">Privacy Policy</a></li>
                        <li><a href="#" class="hover:text-white transition">Terms of Service</a></li>
                        <li><a href="#" class="hover:text-white transition">Charity Registration</a></li>
                    </ul>
                </div>
                <div class="text-center md:text-left">
                    <h5 class="font-semibold text-white mb-4">Contact Us</h5>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white transition">Support Center</a></li>
                        <li><a href="#" class="hover:text-white transition">Partnership Inquiry</a></li>
                        <li><a href="#" class="hover:text-white transition">Media Contact</a></li>
                    </ul>
                </div>
            </div>
            <div class="mt-8 pt-8 border-t border-gray-700 text-center text-sm">
                <p>&copy; {{ date('Y') }} DonGiv — Making a Difference Together ❤️</p>
            </div>
        </div>
    </footer>

    <script>
        // Profile dropdown functionality
        document.addEventListener('DOMContentLoaded', function() {
            const profileButton = document.getElementById('profileButton');
            const profileDropdown = document.getElementById('profileDropdown');

            if (profileButton && profileDropdown) {
                profileButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    profileDropdown.classList.toggle('hidden');
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!profileButton.contains(e.target) && !profileDropdown.contains(e.target)) {
                        profileDropdown.classList.add('hidden');
                    }
                });
            }
        });
    </script>
</body>
</html>