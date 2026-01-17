<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Notifikasi - DonGiv Admin</title>
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
                <a href="{{ route('admin.notifications.index') }}" class="nav-item active flex items-center space-x-3 py-3 px-4 mb-2">
                    <i class="fas fa-bell"></i>
                    <span>Notifikasi</span>
                </a>
                <a href="#" class="nav-item flex items-center space-x-3 py-3 px-4">
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
            <h2 class="text-3xl font-bold text-gray-800">Edit Notifikasi</h2>
            <p class="text-gray-600">Perbarui pesan notifikasi untuk pengguna</p>
        </header>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6 fade-in">
                {{ session('success') }}
            </div>
        @endif

        <div class="card p-8 rounded-2xl fade-in max-w-3xl mx-auto">
            <form action="{{ route('admin.notifications.update', $notification['id']) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="mb-6">
                    <label for="title" class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-heading mr-2 text-blue-500"></i>Judul Notifikasi
                    </label>
                    <div class="relative">
                        <input type="text" name="title" value="{{ old('title', $notification['title']) }}" class="input-field w-full border border-gray-200 p-4 rounded-xl focus:outline-none focus:border-blue-300 pl-12 bg-gray-50/50 @error('title') border-red-300 @enderror"
                               required placeholder="Masukkan judul notifikasi">
                        <i class="fas fa-heading absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-400"></i>
                    </div>
                    @error('title')
                        <div class="text-red-500 text-sm mt-1 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>{{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="message" class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-align-left mr-2 text-blue-500"></i>Isi Notifikasi
                    </label>
                    <div class="relative">
                        <textarea name="message" class="input-field w-full border border-gray-200 p-4 rounded-xl focus:outline-none focus:border-blue-300 pl-12 bg-gray-50/50 @error('message') border-red-300 @enderror"
                                  rows="5" required placeholder="Tulis pesan notifikasi">{{ old('message', $notification['message']) }}</textarea>
                        <i class="fas fa-align-left absolute left-4 top-4 text-blue-400"></i>
                    </div>
                    @error('message')
                        <div class="text-red-500 text-sm mt-1 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>{{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="flex space-x-4 pt-4">
                    <button type="submit" class="btn-primary flex-1 py-4 px-6 rounded-xl text-white font-semibold text-lg relative overflow-hidden">
                        <span class="relative z-10"><i class="fas fa-edit mr-2"></i>Perbarui Notifikasi</span>
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-blue-400 opacity-0 hover:opacity-100 transition-opacity duration-300"></div>
                    </button>
                    <a href="{{ route('admin.notifications.index') }}" class="flex-1 py-4 px-6 rounded-xl text-center text-gray-700 font-semibold bg-gray-100 hover:bg-gray-200 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
