<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan - DonGiv Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1d4ed8', // biru utama
                        secondary: '#3b82f6', // biru lebih muda
                        accent: '#f59e0b', // warna aksen
                        softblue: '#f0f5ff', // background lebih soft
                        softblue2: '#e0f2fe',
                        softblue3: '#bae6fd',
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 50%, #dbeafe 100%);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
        }
        .card {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
            transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.08), 0 10px 10px -5px rgba(0, 0, 0, 0.05);
        }
        .sidebar {
            background: linear-gradient(180deg, #1d4ed8 0%, #1e40af 100%);
            box-shadow: 5px 0 15px rgba(0, 0, 0, 0.1);
        }
        .nav-item {
            transition: all 0.3s ease;
            border-radius: 0.75rem;
        }
        .nav-item:hover {
            background-color: rgba(255, 255, 255, 0.15);
        }
        .nav-item.active {
            background-color: rgba(255, 255, 255, 0.25);
        }
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .input-field {
            transition: all 0.3s ease;
        }
        .input-field:focus {
            box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.2);
            border-color: #3b82f6;
        }
        .btn-primary {
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #1d4ed8 0%, #3b82f6 100%);
            position: relative;
            overflow: hidden;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.3);
        }
        .tab-button {
            transition: all 0.3s ease;
            border-bottom: 3px solid transparent;
        }
        .tab-button.active {
            border-bottom: 3px solid #3b82f6;
            color: #1d4ed8;
            font-weight: 600;
        }
    </style>
</head>

<body class="min-h-screen flex">
    <!-- Sidebar -->
    <div class="sidebar text-white w-64 min-h-screen p-6 sticky top-0">
        <div class="mb-10">
            <div class="flex items-center space-x-3 mb-8">
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-heart text-white text-xl"></i>
                </div>
                <h1 class="text-xl font-bold">DonGiv Admin</h1>
            </div>

            <nav class="space-y-1">
                <a href="{{ route('admin.dashboard') }}" class="nav-item flex items-center space-x-3 py-3 px-4 mb-2">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.campaigns.index') }}" class="nav-item flex items-center space-x-3 py-3 px-4 mb-2">
                    <i class="fas fa-donate"></i>
                    <span>Kampanye Donasi</span>
                </a>
                <a href="{{ route('admin.notifications.index') }}" class="nav-item flex items-center space-x-3 py-3 px-4 mb-2">
                    <i class="fas fa-bell"></i>
                    <span>Notifikasi</span>
                </a>
                <a href="{{ route('admin.settings') }}" class="nav-item active flex items-center space-x-3 py-3 px-4">
                    <i class="fas fa-cog"></i>
                    <span>Pengaturan</span>
                </a>
            </nav>
        </div>

        <div class="absolute bottom-6 left-6 right-6">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full bg-red-500/80 hover:bg-red-600 text-white py-3 px-4 rounded-lg flex items-center justify-center space-x-2 transition-colors">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-8 overflow-auto">
        <header class="mb-8 fade-in">
            <h2 class="text-3xl font-bold text-gray-800">Pengaturan Sistem</h2>
            <p class="text-gray-600">Atur preferensi dan konfigurasi platform</p>
        </header>

        <div class="card p-6 rounded-2xl fade-in">
            <!-- Tabs -->
            <div class="border-b border-gray-200 mb-6">
                <nav class="flex space-x-8">
                    <button class="tab-button active py-4 px-1 border-b-2 border-blue-500 font-medium text-blue-600" data-tab="general">Umum</button>
                    <button class="tab-button py-4 px-1 font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="profile">Profil</button>
                    <button class="tab-button py-4 px-1 font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="security">Keamanan</button>
                    <button class="tab-button py-4 px-1 font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="notification">Notifikasi</button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="tab-content active" id="general-tab">
                <form class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">
                                <i class="fas fa-globe mr-2 text-blue-500"></i>Nama Platform
                            </label>
                            <div class="relative">
                                <input type="text" class="input-field w-full border border-gray-200 p-4 rounded-xl focus:outline-none focus:border-blue-300 pl-12 bg-gray-50/50" 
                                       value="DonGiv - Platform Donasi Terpercaya" placeholder="Nama platform">
                                <i class="fas fa-globe absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-400"></i>
                            </div>
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">
                                <i class="fas fa-envelope mr-2 text-blue-500"></i>Email Kontak
                            </label>
                            <div class="relative">
                                <input type="email" class="input-field w-full border border-gray-200 p-4 rounded-xl focus:outline-none focus:border-blue-300 pl-12 bg-gray-50/50" 
                                       value="info@dongiv.org" placeholder="Email kontak">
                                <i class="fas fa-envelope absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-400"></i>
                            </div>
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">
                                <i class="fas fa-phone mr-2 text-blue-500"></i>Nomor Telepon
                            </label>
                            <div class="relative">
                                <input type="tel" class="input-field w-full border border-gray-200 p-4 rounded-xl focus:outline-none focus:border-blue-300 pl-12 bg-gray-50/50" 
                                       value="+62 21 1234 5678" placeholder="Nomor telepon">
                                <i class="fas fa-phone absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-400"></i>
                            </div>
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">
                                <i class="fas fa-map-marker-alt mr-2 text-blue-500"></i>Alamat Kantor
                            </label>
                            <div class="relative">
                                <input type="text" class="input-field w-full border border-gray-200 p-4 rounded-xl focus:outline-none focus:border-blue-300 pl-12 bg-gray-50/50" 
                                       value="Jl. Jend. Sudirman No.1, Jakarta" placeholder="Alamat kantor">
                                <i class="fas fa-map-marker-alt absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-400"></i>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">
                            <i class="fas fa-align-left mr-2 text-blue-500"></i>Deskripsi Platform
                        </label>
                        <div class="relative">
                            <textarea class="input-field w-full border border-gray-200 p-4 rounded-xl focus:outline-none focus:border-blue-300 pl-12 bg-gray-50/50" 
                                      rows="4" placeholder="Deskripsi singkat tentang platform donasi">DonGiv adalah platform donasi terpercaya yang menghubungkan para dermawan dengan pihak yang membutuhkan bantuan. Kami berkomitmen untuk menyalurkan bantuan secara transparan dan efektif.</textarea>
                            <i class="fas fa-align-left absolute left-4 top-4 text-blue-400"></i>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="button" class="btn-primary py-3 px-8 rounded-xl text-white font-semibold relative overflow-hidden">
                            <span class="relative z-10"><i class="fas fa-save mr-2"></i>Simpan Pengaturan</span>
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-blue-400 opacity-0 hover:opacity-100 transition-opacity duration-300"></div>
                        </button>
                    </div>
                </form>
            </div>

            <div class="tab-content hidden" id="profile-tab">
                <form class="space-y-6">
                    <div class="flex items-center mb-6">
                        <div class="w-24 h-24 rounded-full bg-blue-100 flex items-center justify-center mr-6">
                            <i class="fas fa-user text-blue-500 text-3xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-800">Foto Profil</h3>
                            <p class="text-gray-600">Format: JPG, PNG. Maks. ukuran 2MB</p>
                            <button type="button" class="mt-2 bg-blue-100 hover:bg-blue-200 text-blue-700 font-medium py-2 px-4 rounded-lg transition-colors">
                                <i class="fas fa-upload mr-2"></i>Unggah Foto
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">
                                <i class="fas fa-user mr-2 text-blue-500"></i>Nama Lengkap
                            </label>
                            <div class="relative">
                                <input type="text" class="input-field w-full border border-gray-200 p-4 rounded-xl focus:outline-none focus:border-blue-300 pl-12 bg-gray-50/50" 
                                       value="Admin DonGiv" placeholder="Nama lengkap">
                                <i class="fas fa-user absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-400"></i>
                            </div>
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">
                                <i class="fas fa-envelope mr-2 text-blue-500"></i>Email
                            </label>
                            <div class="relative">
                                <input type="email" class="input-field w-full border border-gray-200 p-4 rounded-xl focus:outline-none focus:border-blue-300 pl-12 bg-gray-50/50" 
                                       value="admin@dongiv.org" placeholder="Alamat email">
                                <i class="fas fa-envelope absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-400"></i>
                            </div>
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">
                                <i class="fas fa-phone mr-2 text-blue-500"></i>Nomor Telepon
                            </label>
                            <div class="relative">
                                <input type="tel" class="input-field w-full border border-gray-200 p-4 rounded-xl focus:outline-none focus:border-blue-300 pl-12 bg-gray-50/50" 
                                       value="+62 812 3456 7890" placeholder="Nomor telepon">
                                <i class="fas fa-phone absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-400"></i>
                            </div>
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">
                                <i class="fas fa-map-marker-alt mr-2 text-blue-500"></i>Lokasi
                            </label>
                            <div class="relative">
                                <input type="text" class="input-field w-full border border-gray-200 p-4 rounded-xl focus:outline-none focus:border-blue-300 pl-12 bg-gray-50/50" 
                                       value="Jakarta, Indonesia" placeholder="Lokasi Anda">
                                <i class="fas fa-map-marker-alt absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-400"></i>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="button" class="btn-primary py-3 px-8 rounded-xl text-white font-semibold relative overflow-hidden">
                            <span class="relative z-10"><i class="fas fa-save mr-2"></i>Perbarui Profil</span>
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-blue-400 opacity-0 hover:opacity-100 transition-opacity duration-300"></div>
                        </button>
                    </div>
                </form>
            </div>

            <div class="tab-content hidden" id="security-tab">
                <form class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">
                                <i class="fas fa-lock mr-2 text-blue-500"></i>Kata Sandi Lama
                            </label>
                            <div class="relative">
                                <input type="password" class="input-field w-full border border-gray-200 p-4 rounded-xl focus:outline-none focus:border-blue-300 pl-12 bg-gray-50/50" 
                                       placeholder="Kata sandi lama">
                                <i class="fas fa-lock absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-400"></i>
                            </div>
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">
                                <i class="fas fa-key mr-2 text-blue-500"></i>Kata Sandi Baru
                            </label>
                            <div class="relative">
                                <input type="password" class="input-field w-full border border-gray-200 p-4 rounded-xl focus:outline-none focus:border-blue-300 pl-12 bg-gray-50/50" 
                                       placeholder="Kata sandi baru">
                                <i class="fas fa-key absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-400"></i>
                            </div>
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">
                                <i class="fas fa-key mr-2 text-blue-500"></i>Konfirmasi Kata Sandi Baru
                            </label>
                            <div class="relative">
                                <input type="password" class="input-field w-full border border-gray-200 p-4 rounded-xl focus:outline-none focus:border-blue-300 pl-12 bg-gray-50/50" 
                                       placeholder="Konfirmasi kata sandi baru">
                                <i class="fas fa-key absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-400"></i>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="button" class="btn-primary py-3 px-8 rounded-xl text-white font-semibold relative overflow-hidden">
                            <span class="relative z-10"><i class="fas fa-shield-alt mr-2"></i>Perbarui Keamanan</span>
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-blue-400 opacity-0 hover:opacity-100 transition-opacity duration-300"></div>
                        </button>
                    </div>
                </form>
            </div>

            <div class="tab-content hidden" id="notification-tab">
                <form class="space-y-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-xl">
                            <div>
                                <h3 class="font-semibold text-gray-800">Email Notifikasi</h3>
                                <p class="text-sm text-gray-600">Terima notifikasi penting melalui email</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-xl">
                            <div>
                                <h3 class="font-semibold text-gray-800">Notifikasi Push</h3>
                                <p class="text-sm text-gray-600">Terima notifikasi di perangkat Anda</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-xl">
                            <div>
                                <h3 class="font-semibold text-gray-800">Notifikasi Kampanye Baru</h3>
                                <p class="text-sm text-gray-600">Dapatkan pemberitahuan saat ada kampanye baru</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-xl">
                            <div>
                                <h3 class="font-semibold text-gray-800">Notifikasi Donasi Masuk</h3>
                                <p class="text-sm text-gray-600">Dapatkan pemberitahuan saat ada donasi masuk</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="button" class="btn-primary py-3 px-8 rounded-xl text-white font-semibold relative overflow-hidden">
                            <span class="relative z-10"><i class="fas fa-bell mr-2"></i>Simpan Preferensi</span>
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-blue-400 opacity-0 hover:opacity-100 transition-opacity duration-300"></div>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Tab switching functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');

            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const tabId = this.getAttribute('data-tab');
                    
                    // Update active tab button
                    tabButtons.forEach(btn => {
                        btn.classList.remove('active');
                        btn.classList.add('text-gray-500');
                        btn.classList.remove('text-blue-600');
                    });
                    
                    this.classList.add('active');
                    this.classList.add('text-blue-600');
                    this.classList.remove('text-gray-500');
                    
                    // Show active tab content
                    tabContents.forEach(content => {
                        content.classList.add('hidden');
                    });
                    
                    document.getElementById(tabId + '-tab').classList.remove('hidden');
                });
            });
        });
    </script>
</body>
</html>