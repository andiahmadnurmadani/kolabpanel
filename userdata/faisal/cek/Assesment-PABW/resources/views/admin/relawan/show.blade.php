@extends('admin.layouts.master')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Campaign Header -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl shadow-lg p-6 mb-6 border border-blue-100">
        <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-3">
                    @if($campaign->image)
                        <div class="w-12 h-12 rounded-lg overflow-hidden">
                            <img src="{{ $campaign->image }}" alt="Gambar Kampanye" class="w-full h-full object-cover"
                                 onerror="this.onerror=null; this.src='https://placehold.co/48x48?text=No+Image';">
                        </div>
                    @else
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-people-group text-blue-600 text-xl"></i>
                        </div>
                    @endif
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">{{ $campaign->judul }}</h1>
                        <p class="text-gray-600 text-sm mt-1">{{ $campaign->lokasi }}</p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600">
                    <div class="flex items-center">
                        <i class="fas fa-map-marker-alt text-blue-500 mr-2"></i>
                        <span>{{ $campaign->lokasi }}</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-calendar-alt text-green-500 mr-2"></i>
                        <span>{{ \Carbon\Carbon::parse($campaign->tanggal_mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($campaign->tanggal_selesai)->format('d M Y') }}</span>
                    </div>
                    <div class="flex items-center">
                        <span class="px-3 py-1 rounded-full text-xs font-medium
                            @if($campaign->status == 'Aktif') bg-green-100 text-green-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ $campaign->status }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.relawan.edit', ['id' => $campaign->id]) }}" class="bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white py-2 px-4 rounded-lg flex items-center transition-all duration-200 shadow-md hover:shadow-lg">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
                <a href="{{ route('admin.relawan.index') }}" class="bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white py-2 px-4 rounded-lg flex items-center transition-all duration-200 shadow-md hover:shadow-lg">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
            </div>
        </div>

        <div class="mt-6 pt-6 border-t border-gray-200">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100 text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $campaign->volunteers ? $campaign->volunteers->count() : 0 }}</div>
                    <div class="text-xs text-gray-500 mt-1">Total Relawan</div>
                </div>
                <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100 text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $verifiedCount ?? 0 }}</div>
                    <div class="text-xs text-gray-500 mt-1">Terverifikasi</div>
                </div>
                <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100 text-center">
                    <div class="text-2xl font-bold text-yellow-600">{{ $pendingCount ?? 0 }}</div>
                    <div class="text-xs text-gray-500 mt-1">Menunggu</div>
                </div>
                <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100 text-center">
                    <div class="text-2xl font-bold text-red-600">{{ $campaign->volunteers ? $campaign->volunteers->where('status_verifikasi', 'ditolak')->count() : 0 }}</div>
                    <div class="text-xs text-gray-500 mt-1">Ditolak</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="bg-white rounded-t-xl shadow-lg overflow-hidden">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex" aria-label="Tabs">
                <button type="button" class="tab-button active w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm" data-tab="volunteers">
                    <i class="fas fa-users mr-2"></i> Daftar Relawan
                </button>
                <button type="button" class="tab-button w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="details">
                    <i class="fas fa-info-circle mr-2"></i> Detail Kampanye
                </button>
            </nav>
        </div>
    </div>

    <!-- Tabs Content -->
    <div class="bg-white rounded-b-xl shadow-lg overflow-hidden">
        <!-- Volunteers Tab -->
        <div id="volunteers-tab" class="tab-content active p-6">
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Search and Filter -->
            <div class="mb-6 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="flex-1 max-w-md">
                    <div class="relative">
                        <input type="text" placeholder="Cari relawan..."
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                </div>
                <div class="flex flex-wrap gap-3">
                    <select id="statusFilter" class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        <option value="">Semua Status</option>
                        <option value="pending">Menunggu Verifikasi</option>
                        <option value="disetujui">Terverifikasi</option>
                        <option value="ditolak">Ditolak</option>
                    </select>
                    <a href="{{ route('admin.verifikasi-relawan.by-campaign', $campaign->id) }}" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white py-2 px-4 rounded-lg flex items-center transition-all duration-200 shadow-md hover:shadow-lg">
                        <i class="fas fa-user-check mr-2"></i> Verifikasi Relawan
                    </a>
                </div>
            </div>

            <!-- Volunteers Table -->
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Relawan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kontak</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pendaftaran</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($campaign->volunteers ?? [] as $volunteer)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="text-blue-800 font-medium">{{ substr($volunteer->nama_lengkap, 0, 1) }}</span>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $volunteer->nama_lengkap }}</div>
                                            <div class="text-sm text-gray-500">{{ $volunteer->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $volunteer->whatsapp }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium
                                        @if($volunteer->status_verifikasi == 'disetujui' || $volunteer->status_verifikasi == 'Terverifikasi') bg-green-100 text-green-800
                                        @elseif($volunteer->status_verifikasi == 'pending' || $volunteer->status_verifikasi == 'Menunggu Verifikasi') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ $volunteer->status_verifikasi == 'disetujui' ? 'Terverifikasi' : ($volunteer->status_verifikasi == 'pending' ? 'Menunggu Verifikasi' : ($volunteer->status_verifikasi == 'ditolak' ? 'Ditolak' : $volunteer->status_verifikasi)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($volunteer->created_at)->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.verifikasi-relawan.show', $volunteer->id) }}" class="text-blue-600 hover:text-blue-900 hover:bg-blue-50 p-2 rounded-full transition-colors duration-200">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($volunteer->status_verifikasi == 'pending')
                                        <a href="{{ route('admin.verifikasi-relawan.index') }}" class="text-yellow-600 hover:text-yellow-900 hover:bg-yellow-50 p-2 rounded-full transition-colors duration-200" title="Verifikasi di halaman khusus">
                                            <i class="fas fa-user-check"></i>
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-users text-gray-300 text-5xl mb-4"></i>
                                        <h3 class="text-lg font-medium text-gray-900 mb-1">Belum ada relawan</h3>
                                        <p class="text-gray-500">Tidak ada relawan terdaftar untuk kampanye ini</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Details Tab -->
        <div id="details-tab" class="tab-content p-6 hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">Informasi Kampanye</h3>
                    <div class="space-y-5">
                        @if($campaign->image)
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Gambar Kampanye</label>
                            <div class="p-3 rounded-lg">
                                <img src="{{ $campaign->image }}" alt="Gambar Kampanye" class="w-full max-w-md rounded-lg border border-gray-200 object-cover h-48"
                                     onerror="this.onerror=null; this.src='https://placehold.co/600x200?text=No+Image';">
                            </div>
                        </div>
                        @endif
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Judul Kampanye</label>
                            <p class="text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $campaign->judul }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                            <p class="text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $campaign->lokasi }}</p>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                                <p class="text-gray-900 bg-gray-50 p-3 rounded-lg">{{ \Carbon\Carbon::parse($campaign->tanggal_mulai)->format('d F Y') }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                                <p class="text-gray-900 bg-gray-50 p-3 rounded-lg">{{ \Carbon\Carbon::parse($campaign->tanggal_selesai)->format('d F Y') }}</p>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <span class="px-3 py-1 rounded-full text-sm font-medium
                                    @if($campaign->status == 'Aktif') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ $campaign->status }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">Statistik Kampanye</h3>
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 space-y-4">
                        <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                            <span class="text-gray-700">Total Relawan Terdaftar</span>
                            <span class="text-xl font-bold text-blue-600">{{ $campaign->volunteers ? $campaign->volunteers->count() : 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                            <span class="text-gray-700">Relawan Diverifikasi</span>
                            <span class="text-xl font-bold text-green-600">{{ $verifiedCount ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                            <span class="text-gray-700">Menunggu Verifikasi</span>
                            <span class="text-xl font-bold text-yellow-600">{{ $pendingCount ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                            <span class="text-gray-700">Relawan Ditolak</span>
                            <span class="text-xl font-bold text-red-600">{{ $campaign->volunteers ? $campaign->volunteers->where('status_verifikasi', 'ditolak')->count() : 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-700">Durasi Kampanye</span>
                            <span class="text-xl font-bold text-purple-600">{{ \Carbon\Carbon::parse($campaign->tanggal_mulai)->diffInDays(\Carbon\Carbon::parse($campaign->tanggal_selesai)) }} hari</span>
                        </div>
                    </div>

                    <div class="mt-6 bg-gradient-to-br from-yellow-50 to-orange-50 rounded-xl p-5 border border-yellow-200">
                        <h4 class="font-medium text-gray-900 mb-2 flex items-center">
                            <i class="fas fa-info-circle text-yellow-600 mr-2"></i> Informasi Penting
                        </h4>
                        <p class="text-sm text-gray-700">
                            Kampanye ini saat ini {{ strtolower($campaign->status) }} dan
                            memiliki {{ $campaign->volunteers ? $campaign->volunteers->count() : 0 }} relawan terdaftar.
                            Pastikan untuk meninjau dan memverifikasi pendaftaran relawan secara berkala.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Tab functionality
    document.addEventListener('DOMContentLoaded', function() {
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');

        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Remove active class from all buttons and contents
                tabButtons.forEach(btn => {
                    btn.classList.remove('active', 'border-blue-500', 'text-blue-600');
                    btn.classList.add('text-gray-500');
                });
                tabContents.forEach(content => content.classList.add('hidden'));

                // Add active class to clicked button
                this.classList.add('active', 'border-blue-500', 'text-blue-600');
                this.classList.remove('text-gray-500');

                // Show corresponding content
                const tabId = this.getAttribute('data-tab');
                document.getElementById(tabId + '-tab').classList.remove('hidden');
            });
        });

        // Add filter functionality
        const statusFilter = document.getElementById('statusFilter');
        if (statusFilter) {
            statusFilter.addEventListener('change', function() {
                const filterValue = this.value;
                const rows = document.querySelectorAll('tbody tr:not([style*="display: none"])');

                rows.forEach(row => {
                    const statusCell = row.querySelector('td:nth-child(3)'); // status column
                    if (statusCell) {
                        const statusText = statusCell.textContent.trim().toLowerCase();

                        if (filterValue === '' || statusText.includes(filterValue) ||
                            (filterValue === 'disetujui' && statusText.includes('terverifikasi')) ||
                            (filterValue === 'pending' && statusText.includes('menunggu')) ||
                            (filterValue === 'ditolak' && statusText.includes('ditolak'))) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    }
                });
            });
        }
    });
</script>
@endsection