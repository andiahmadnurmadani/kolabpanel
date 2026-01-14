@extends('layouts.admin')

@section('page-title', 'Statistik & Analisis')
@section('page-subtitle', 'Laporan lengkap aktivitas sistem')

@section('content')
<div class="space-y-6">
    <!-- Overview Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-sm p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-2xl"></i>
                </div>
                <span class="text-xs bg-white/20 px-2 py-1 rounded">Total</span>
            </div>
            <h3 class="text-3xl font-bold mb-1">{{ $totalUsers }}</h3>
            <p class="text-blue-100 text-sm">Total Pengguna</p>
            <div class="mt-3 text-xs text-blue-100">
                <i class="fas fa-user-shield mr-1"></i> {{ $totalAdmins }} Admin, {{ $totalNonAdmins }} User
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-sm p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-check text-2xl"></i>
                </div>
                <span class="text-xs bg-white/20 px-2 py-1 rounded">7 Hari</span>
            </div>
            <h3 class="text-3xl font-bold mb-1">{{ $activeUsers }}</h3>
            <p class="text-green-100 text-sm">Pengguna Aktif</p>
            <div class="mt-3 text-xs text-green-100">
                {{ number_format(($activeUsers / max($totalUsers, 1)) * 100, 1) }}% dari total
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-sm p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-sticky-note text-2xl"></i>
                </div>
                <span class="text-xs bg-white/20 px-2 py-1 rounded">Notes</span>
            </div>
            <h3 class="text-3xl font-bold mb-1">{{ $totalNotes }}</h3>
            <p class="text-purple-100 text-sm">Total Catatan</p>
            <div class="mt-3 text-xs text-purple-100">
                {{ number_format($totalNotes / max($totalUsers, 1), 1) }} catatan/user
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-sm p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-tasks text-2xl"></i>
                </div>
                <span class="text-xs bg-white/20 px-2 py-1 rounded">Tasks</span>
            </div>
            <h3 class="text-3xl font-bold mb-1">{{ $totalTasks }}</h3>
            <p class="text-orange-100 text-sm">Total Tugas</p>
            <div class="mt-3 text-xs text-orange-100">
                {{ $completedTasks }} selesai, {{ $pendingTasks }} pending
            </div>
        </div>
    </div>

    <!-- Task & Activity Stats -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Task Statistics -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-6">Status Tugas</h3>
            
            <div class="space-y-4">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Selesai</span>
                        <span class="text-sm font-bold text-green-600">{{ $completedTasks }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0 }}%"></div>
                    </div>
                </div>
                
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Dalam Proses</span>
                        <span class="text-sm font-bold text-blue-600">{{ $inProgressTasks }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $totalTasks > 0 ? ($inProgressTasks / $totalTasks) * 100 : 0 }}%"></div>
                    </div>
                </div>
                
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Pending</span>
                        <span class="text-sm font-bold text-yellow-600">{{ $pendingTasks }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-yellow-500 h-2 rounded-full" style="width: {{ $totalTasks > 0 ? ($pendingTasks / $totalTasks) * 100 : 0 }}%"></div>
                    </div>
                </div>
            </div>

            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-600">Total Aktivitas Kalender: <span class="font-bold text-gray-800">{{ $totalActivities }}</span></p>
                <p class="text-xs text-gray-500 mt-1">{{ $completedActivities }} selesai, {{ $pendingActivities }} pending</p>
            </div>
        </div>

        <!-- Top Users -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-6">Top 5 Pengguna Aktif</h3>
            
            <div class="space-y-3">
                @forelse($topUsers as $index => $user)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold">
                            {{ $index + 1 }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $user->email }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold text-gray-800">{{ $user->notes_count + $user->tasks_count + $user->activities_count }}</p>
                        <p class="text-xs text-gray-500">konten</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <i class="fas fa-users text-gray-300 text-4xl mb-3"></i>
                    <p class="text-gray-500">Belum ada data pengguna</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Trends Chart -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-6">Tren Aktivitas (30 Hari Terakhir)</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="text-center p-4 bg-blue-50 rounded-lg">
                <i class="fas fa-user-plus text-blue-600 text-2xl mb-2"></i>
                <p class="text-sm text-gray-600">Registrasi User</p>
                <p class="text-2xl font-bold text-gray-800">{{ $userTrend->sum('count') }}</p>
            </div>
            <div class="text-center p-4 bg-purple-50 rounded-lg">
                <i class="fas fa-file-alt text-purple-600 text-2xl mb-2"></i>
                <p class="text-sm text-gray-600">Catatan Dibuat</p>
                <p class="text-2xl font-bold text-gray-800">{{ $notesTrend->sum('count') }}</p>
            </div>
            <div class="text-center p-4 bg-orange-50 rounded-lg">
                <i class="fas fa-check-circle text-orange-600 text-2xl mb-2"></i>
                <p class="text-sm text-gray-600">Tugas Dibuat</p>
                <p class="text-2xl font-bold text-gray-800">{{ $tasksTrend->sum('count') }}</p>
            </div>
        </div>

        <canvas id="trendsChart" height="80"></canvas>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('trendsChart').getContext('2d');
    
    const userTrend = @json($userTrend);
    const notesTrend = @json($notesTrend);
    const tasksTrend = @json($tasksTrend);
    
    // Create labels for last 30 days
    const labels = [];
    for (let i = 29; i >= 0; i--) {
        const date = new Date();
        date.setDate(date.getDate() - i);
        labels.push(date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }));
    }
    
    // Map data to labels
    function mapDataToLabels(data) {
        return labels.map(label => {
            const dateStr = label;
            const item = data.find(d => {
                const itemDate = new Date(d.date);
                const itemLabel = itemDate.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
                return itemLabel === dateStr;
            });
            return item ? item.count : 0;
        });
    }
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Registrasi User',
                    data: mapDataToLabels(userTrend),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Catatan Dibuat',
                    data: mapDataToLabels(notesTrend),
                    borderColor: 'rgb(168, 85, 247)',
                    backgroundColor: 'rgba(168, 85, 247, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Tugas Dibuat',
                    data: mapDataToLabels(tasksTrend),
                    borderColor: 'rgb(249, 115, 22)',
                    backgroundColor: 'rgba(249, 115, 22, 0.1)',
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            }
        }
    });
});
</script>
@endsection
