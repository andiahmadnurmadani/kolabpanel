@extends('admin.layouts.master')

@section('title', 'Dashboard')

@section('content')
{{-- Header Section --}}
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Dashboard Overview</h2>
        <p class="text-slate-500 text-sm mt-1">Pantau performa donasi dan relawan secara real-time.</p>
    </div>

    {{-- Date Filter (Mockup) --}}
    <div class="flex bg-white rounded-lg p-1 shadow-sm border border-slate-200">
        <button class="px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-50 rounded-md transition-colors">Minggu Ini</button>
        <button class="px-3 py-1.5 text-xs font-bold text-blue-600 bg-blue-50 rounded-md shadow-sm transition-colors">Bulan Ini</button>
        <button class="px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-50 rounded-md transition-colors">Tahun Ini</button>
    </div>
</div>

{{-- STATS CARDS (Modern & Clean) --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

    {{-- Card 1: Total Donasi --}}
    <div class="bg-white p-6 rounded-2xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] hover:-translate-y-1 transition-all duration-300 border border-slate-100">
        <div class="flex justify-between items-start mb-4">
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                <i class="fas fa-wallet text-lg"></i>
            </div>
            <span class="flex items-center text-xs font-bold text-green-600 bg-green-50 px-2 py-1 rounded-full">
                <i class="fas fa-arrow-up mr-1"></i> +12.5%
            </span>
        </div>
        <h3 class="text-slate-500 text-xs font-bold uppercase tracking-wider mb-1">Total Donasi</h3>
        <p class="text-2xl font-extrabold text-slate-800">Rp {{ number_format($totalAmount, 0, ',', '.') }}</p>
        <div class="mt-4 w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
            <div class="bg-blue-500 h-1.5 rounded-full" style="width: {{ $monthlyAmount > 0 ? min(100, ($monthlyAmount / max(1, $monthlyAmount)) * 100) : 0 }}%"></div>
        </div>
        <p class="text-[10px] text-slate-400 mt-2 text-right">Rp {{ number_format($monthlyAmount, 0, ',', '.') }} bulan ini</p>
    </div>

    {{-- Card 2: Kampanye Aktif --}}
    <div class="bg-white p-6 rounded-2xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] hover:-translate-y-1 transition-all duration-300 border border-slate-100">
        <div class="flex justify-between items-start mb-4">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                <i class="fas fa-bullhorn text-lg"></i>
            </div>
        </div>
        <h3 class="text-slate-500 text-xs font-bold uppercase tracking-wider mb-1">Kampanye Aktif</h3>
        <p class="text-2xl font-extrabold text-slate-800">{{ $activeCampaigns }} <span class="text-sm font-medium text-slate-400">/ {{ $totalCampaigns }}</span></p>
        <div class="flex items-center gap-2 mt-4 text-xs text-slate-500">
            <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span> {{ $activeVolunteerCampaigns }} Kampanye Relawan Aktif
        </div>
    </div>

    {{-- Card 3: Donasi Bulan Ini --}}
    <div class="bg-white p-6 rounded-2xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] hover:-translate-y-1 transition-all duration-300 border border-slate-100">
        <div class="flex justify-between items-start mb-4">
            <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600">
                <i class="fas fa-chart-line text-lg"></i>
            </div>
            <span class="flex items-center text-xs font-bold text-green-600 bg-green-50 px-2 py-1 rounded-full">
                +{{ $totalDonations > 0 ? number_format(($totalDonations / max(1, $totalDonations)) * 100, 1) : '0' }}%
            </span>
        </div>
        <h3 class="text-slate-500 text-xs font-bold uppercase tracking-wider mb-1">Total Donasi</h3>
        <p class="text-2xl font-extrabold text-slate-800">{{ $totalDonations }}</p>
        <p class="text-xs text-slate-400 mt-1">{{ $totalUsers }} Pengguna Terdaftar</p>
    </div>

    {{-- Card 4: Menunggu Verifikasi (Actionable) --}}
    <div class="bg-gradient-to-br from-orange-500 to-red-500 p-6 rounded-2xl shadow-lg shadow-orange-200 text-white relative overflow-hidden group cursor-pointer hover:shadow-orange-300 transition-all">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-white opacity-10 rounded-full group-hover:scale-150 transition-transform duration-500"></div>

        <div class="relative z-10">
            <div class="flex justify-between items-start mb-4">
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-white">
                    <i class="fas fa-clock text-lg"></i>
                </div>
            </div>
            <h3 class="text-orange-100 text-xs font-bold uppercase tracking-wider mb-1">Perlu Tindakan</h3>
            <p class="text-2xl font-extrabold text-white">{{ $pendingTransactions }} Transaksi</p>
            <a href="{{ route('admin.donations.index') }}" class="inline-flex items-center gap-2 mt-4 text-xs font-bold bg-white/20 px-3 py-2 rounded-lg hover:bg-white/30 transition-colors">
                Verifikasi Sekarang <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>

{{-- CHART & ACTIVITY SECTION --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    {{-- Main Chart (Donasi) --}}
    <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
        <div class="flex justify-between items-center mb-6">
            <h3 class="font-bold text-slate-800">Tren Donasi 12 Bulan Terakhir</h3>
            <button class="text-slate-400 hover:text-blue-600 transition-colors"><i class="fas fa-ellipsis-h"></i></button>
        </div>
        <div class="h-80 w-full relative">
            <canvas id="donationsChart"></canvas>
        </div>
    </div>

    {{-- Side: Quick Activity --}}
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
        <h3 class="font-bold text-slate-800 mb-6">Aktivitas Terbaru</h3>

        <div class="space-y-6 relative">
            {{-- Garis Vertikal --}}
            <div class="absolute left-3.5 top-2 bottom-2 w-0.5 bg-slate-100"></div>

            @forelse($recentDonations as $donation)
            <div class="flex gap-4 relative">
                <div class="w-8 h-8 rounded-full bg-green-100 border-2 border-white shadow-sm flex items-center justify-center shrink-0 z-10">
                    <i class="fas fa-check text-green-600 text-xs"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-800">Donasi {{ $donation->order_id }} Diverifikasi</p>
                    <p class="text-xs text-slate-500 mt-0.5">Rp {{ number_format($donation->amount, 0, ',', '.') }} • {{ $donation->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @empty
            <div class="flex gap-4 relative">
                <div class="w-8 h-8 rounded-full bg-gray-100 border-2 border-white shadow-sm flex items-center justify-center shrink-0 z-10">
                    <i class="fas fa-info text-gray-600 text-xs"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-800">Belum ada aktivitas</p>
                    <p class="text-xs text-slate-500 mt-0.5">Tidak ada donasi baru</p>
                </div>
            </div>
            @endforelse

            @forelse($recentVolunteers as $volunteer)
            <div class="flex gap-4 relative">
                <div class="w-8 h-8 rounded-full bg-blue-100 border-2 border-white shadow-sm flex items-center justify-center shrink-0 z-10">
                    <i class="fas fa-user-plus text-blue-600 text-xs"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-800">{{ $volunteer->user->name ?? 'Relawan' }} mendaftar</p>
                    <p class="text-xs text-slate-500 mt-0.5">Sebagai Relawan • {{ $volunteer->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @empty
            <div class="flex gap-4 relative">
                <div class="w-8 h-8 rounded-full bg-gray-100 border-2 border-white shadow-sm flex items-center justify-center shrink-0 z-10">
                    <i class="fas fa-info text-gray-600 text-xs"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-800">Belum ada pendaftaran relawan</p>
                    <p class="text-xs text-slate-500 mt-0.5">Tidak ada relawan baru</p>
                </div>
            </div>
            @endforelse
        </div>

        {{-- Quick Action Button --}}
        <a href="{{ route('admin.notifications.index') }}" class="block w-full text-center mt-8 py-3 text-xs font-bold text-slate-500 hover:text-blue-600 hover:bg-slate-50 rounded-xl transition-all border border-slate-100 hover:border-blue-100">
            Lihat Semua Aktivitas
        </a>
    </div>
</div>

{{-- CHART SCRIPT --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gradient Fill untuk Chart
        const ctx = document.getElementById('donationsChart').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(59, 130, 246, 0.2)'); // Blue with opacity
        gradient.addColorStop(1, 'rgba(59, 130, 246, 0)'); // Transparent

        // Data from controller
        const monthlyData = @json($monthlyDonationData);
        const labels = monthlyData.map(item => item.month);
        const amounts = monthlyData.map(item => item.amount / 1000000); // Convert to millions for display

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Donasi (Juta Rp)',
                    data: amounts,
                    borderColor: '#3b82f6',
                    backgroundColor: gradient,
                    borderWidth: 2,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#3b82f6',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        titleFont: {
                            size: 13
                        },
                        bodyFont: {
                            size: 12
                        },
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return 'Rp ' + context.parsed.y.toFixed(1) + ' Juta';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            borderDash: [4, 4],
                            color: '#f1f5f9'
                        },
                        ticks: {
                            font: {
                                size: 11
                            },
                            color: '#94a3b8',
                            callback: function(value) {
                                return 'Rp ' + value + 'J';
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 11
                            },
                            color: '#94a3b8'
                        }
                    }
                }
            }
        });
    });
</script>
@endsection