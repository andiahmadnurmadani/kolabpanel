@extends('layouts.admin')

@section('page-title', 'Dashboard Admin')
@section('page-subtitle', 'Overview dan statistik sistem NotezQue')

@section('content')
        <div class="space-y-6">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Users -->
                <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Total Pengguna</p>
                            <h3 class="text-3xl font-bold text-gray-800 mt-2">{{ $totalUsers ?? 0 }}</h3>
                            <p class="text-xs text-gray-500 mt-2">
                                <span class="text-green-600 font-semibold">+12%</span> dari bulan lalu
                            </p>
                        </div>
                        <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-users text-blue-600 text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Active Users -->
                <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Pengguna Aktif</p>
                            <h3 class="text-3xl font-bold text-gray-800 mt-2">{{ $activeUsers ?? 0 }}</h3>
                            <p class="text-xs text-gray-500 mt-2">
                                <span class="text-green-600 font-semibold">7 hari terakhir</span>
                            </p>
                        </div>
                        <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-check text-green-600 text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Notes -->
                <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-purple-500 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Total Catatan</p>
                            <h3 class="text-3xl font-bold text-gray-800 mt-2">{{ $totalNotes ?? 0 }}</h3>
                            <p class="text-xs text-gray-500 mt-2">
                                <span class="text-purple-600 font-semibold">Semua pengguna</span>
                            </p>
                        </div>
                        <div class="w-14 h-14 bg-purple-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-sticky-note text-purple-600 text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Activities -->
                <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-orange-500 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Total Aktivitas</p>
                            <h3 class="text-3xl font-bold text-gray-800 mt-2">{{ $totalActivities ?? 0 }}</h3>
                            <p class="text-xs text-gray-500 mt-2">
                                <span class="text-orange-600 font-semibold">Events & Tasks</span>
                            </p>
                        </div>
                        <div class="w-14 h-14 bg-orange-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-calendar-check text-orange-600 text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts & Quick Actions -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Activity Chart -->
                <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-800">Aktivitas Pengguna</h3>
                        <select class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option>7 Hari Terakhir</option>
                            <option>30 Hari Terakhir</option>
                            <option>90 Hari Terakhir</option>
                        </select>
                    </div>

                    <!-- Simple Bar Chart Representation -->
                    <div class="space-y-3">
                        @php
    $days = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
    $values = [65, 78, 82, 71, 85, 92, 88];
                        @endphp
                        @foreach($days as $index => $day)
                        <div class="flex items-center space-x-3">
                            <span class="text-xs font-medium text-gray-600 w-8">{{ $day }}</span>
                            <div class="flex-1 bg-gray-100 rounded-full h-8 overflow-hidden">
                                <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-full rounded-full flex items-center justify-end px-3 transition-all duration-500" 
                                     style="width: {{ $values[$index] }}%">
                                    <span class="text-xs font-semibold text-white">{{ $values[$index] }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Quick Actions & Recent Users -->
                <div class="space-y-6">
                    <!-- Quick Actions -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Aksi Cepat</h3>
                        <div class="space-y-3">
                            <a href="{{ route('admin.users.index') }}" 
                               class="flex items-center p-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors group">
                                <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-users text-white"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-800 text-sm">Kelola Pengguna</p>
                                    <p class="text-xs text-gray-500">Lihat & edit user</p>
                                </div>
                                <i class="fas fa-chevron-right text-gray-400 group-hover:text-blue-600"></i>
                            </a>

                            <a href="{{ route('admin.content.index') }}" 
                               class="flex items-center p-3 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors group">
                                <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-file-alt text-white"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-800 text-sm">Konten Statis</p>
                                    <p class="text-xs text-gray-500">Edit konten halaman</p>
                                </div>
                                <i class="fas fa-chevron-right text-gray-400 group-hover:text-purple-600"></i>
                            </a>

                            <a href="{{ route('admin.statistics') }}" 
                               class="flex items-center p-3 bg-green-50 hover:bg-green-100 rounded-lg transition-colors group">
                                <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-chart-bar text-white"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-800 text-sm">Statistik Detail</p>
                                    <p class="text-xs text-gray-500">Analisis lengkap</p>
                                </div>
                                <i class="fas fa-chevron-right text-gray-400 group-hover:text-green-600"></i>
                            </a>
                        </div>
                    </div>

                    <!-- System Status -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Status Sistem</h3>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                    <span class="text-sm text-gray-700">Server</span>
                                </div>
                                <span class="text-xs font-semibold text-green-600">Online</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                    <span class="text-sm text-gray-700">Database</span>
                                </div>
                                <span class="text-xs font-semibold text-green-600">Connected</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <div class="w-2 h-2 bg-yellow-500 rounded-full animate-pulse"></div>
                                    <span class="text-sm text-gray-700">Storage</span>
                                </div>
                                <span class="text-xs font-semibold text-yellow-600">78% Used</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Users Table -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-800">Pengguna Terbaru</h3>
                        <a href="{{ route('admin.users.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-semibold">
                            Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Pengguna</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Bergabung</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($recentUsers ?? [] as $user)
                                @php
                                    $avatarUrl = $user->profile_photo 
                                        ? asset('storage/' . $user->profile_photo) 
                                        : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=6366F1&color=fff&size=40';
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <img src="{{ $avatarUrl }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full object-cover mr-3">
                                            <div>
                                                <p class="font-semibold text-gray-800">{{ $user->name }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $user->email }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $user->is_admin ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-700' }}">
                                            {{ $user->is_admin ? 'Admin' : 'User' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $user->created_at->diffForHumans() }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">
                                            <i class="fas fa-circle text-green-500 text-xs mr-1"></i>
                                            Active
                                        </span>
                                    </td>
                                </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <i class="fas fa-users text-gray-300 text-5xl mb-4"></i>
                                    <p class="text-gray-500">Belum ada pengguna terdaftar</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
@endsection
