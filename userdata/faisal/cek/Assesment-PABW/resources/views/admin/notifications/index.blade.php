<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi Admin - DonGiv Admin</title>
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
        .table th {
            border-bottom-width: 2px;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(59, 130, 246, 0.05);
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
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">Notifikasi Admin</h2>
                    <p class="text-gray-600">Kelola pesan dan notifikasi untuk pengguna</p>
                </div>
                <a href="{{ route('admin.notifications.create') }}" class="btn-primary py-3 px-6 rounded-xl text-white font-semibold relative overflow-hidden">
                    <span class="relative z-10"><i class="fas fa-plus mr-2"></i>Tambah Notifikasi</span>
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-blue-400 opacity-0 hover:opacity-100 transition-opacity duration-300"></div>
                </a>
            </div>
        </header>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6 fade-in">
                {{ session('success') }}
            </div>
        @endif

        <div class="card p-6 rounded-2xl fade-in">
            @if (empty($notifications))
                <div class="text-center py-12">
                    <div class="mx-auto w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-bell text-blue-500 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Belum ada notifikasi</h3>
                    <p class="text-gray-500">Buat notifikasi pertama Anda untuk mulai menginformasikan pengguna</p>
                    <a href="{{ route('admin.notifications.create') }}" class="inline-block mt-4 bg-blue-100 hover:bg-blue-200 text-blue-700 font-semibold py-2 px-6 rounded-lg transition-colors">
                        Buat Notifikasi
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-blue-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Judul</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Pesan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Dibuat Pada</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Update Terakhir</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($notifications as $notification)
                                <tr class="hover:bg-blue-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $notification['title'] }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-600 max-w-xs truncate" title="{{ $notification['message'] }}">
                                            {{ $notification['message'] }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $notification['created_at'] ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $notification['updated_at'] ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <a href="{{ route('admin.notifications.edit', $notification['id']) }}"
                                           class="text-blue-600 hover:text-blue-900 hover:bg-blue-100 py-1 px-3 rounded-lg transition-colors">
                                            <i class="fas fa-edit mr-1"></i>Edit
                                        </a>
                                        <form action="{{ route('admin.notifications.destroy', $notification['id']) }}"
                                              method="POST" class="inline"
                                              onsubmit="return confirm('Yakin ingin menghapus notifikasi ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="text-red-600 hover:text-red-900 hover:bg-red-100 py-1 px-3 rounded-lg transition-colors">
                                                <i class="fas fa-trash mr-1"></i>Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
