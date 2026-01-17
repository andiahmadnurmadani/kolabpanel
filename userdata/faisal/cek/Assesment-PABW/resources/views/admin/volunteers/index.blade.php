@extends('admin.layouts.master')

@section('content')
<div class="p-6 max-w-7xl mx-auto">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-4 sm:mb-0">Verifikasi Pendaftaran Relawan</h1>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif


        <!-- Volunteers Grouped by Campaign -->
        @if(isset($volunteersByCampaign) && !empty($volunteersByCampaign))
            <h2 class="text-xl font-bold text-gray-800 mb-6">Pendaftar Berdasarkan Kampanye</h2>

            @foreach($campaigns as $campaign)
                @if(isset($volunteersByCampaign[$campaign->id]) && $volunteersByCampaign[$campaign->id]->count() > 0)
                <div class="card p-6 mb-6">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start sm:items-center gap-3 mb-4">
                        <div class="flex items-center">
                            <i class="fas fa-people-group text-blue-500 mr-3"></i>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">{{ $campaign->judul }}</h3>
                                <span class="text-sm text-gray-500">({{ $volunteersByCampaign[$campaign->id]->count() }} pendaftar)</span>
                            </div>
                        </div>
                        <span class="px-3 py-1 rounded-full text-sm font-medium whitespace-nowrap
                            @if($campaign->status == 'Aktif') bg-green-100 text-green-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ $campaign->status }}
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-blue-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Gambar</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Nama</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($volunteersByCampaign[$campaign->id]->take(5) as $volunteer) <!-- Take only first 5 -->
                                    <tr class="hover:bg-blue-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($volunteer->campaign && $volunteer->campaign->image)
                                                <div class="w-10 h-10 rounded overflow-hidden bg-gray-100">
                                                    <img src="{{ $volunteer->campaign->image }}"
                                                         alt="Gambar Kampanye"
                                                         class="w-full h-full object-cover"
                                                         onerror="this.onerror=null; this.src='https://placehold.co/40x40?text=No+Image';">
                                                </div>
                                            @else
                                                <div class="w-10 h-10 rounded overflow-hidden bg-gray-100 flex items-center justify-center">
                                                    <i class="fas fa-image text-gray-400"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <div class="text-sm font-medium text-gray-900">{{ substr($volunteer->nama_lengkap, 0, 20) }}{{ strlen($volunteer->nama_lengkap) > 20 ? '...' : '' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <div class="text-sm">{{ substr($volunteer->email, 0, 25) }}{{ strlen($volunteer->email) > 25 ? '...' : '' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <div>
                                                @if($volunteer->status_verifikasi == 'pending')
                                                    <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full">
                                                        Menunggu Verifikasi
                                                    </span>
                                                @elseif($volunteer->status_verifikasi == 'disetujui')
                                                    <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">
                                                        Terverifikasi
                                                    </span>
                                                @elseif($volunteer->status_verifikasi == 'ditolak')
                                                    <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full">
                                                        Ditolak
                                                    </span>
                                                @else
                                                    <span class="bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded-full">
                                                        {{ ucfirst($volunteer->status_verifikasi) }}
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('admin.verifikasi-relawan.show', $volunteer->id) }}"
                                                   class="bg-blue-100 hover:bg-blue-200 text-blue-700 font-medium py-1 px-3 rounded-lg transition-colors whitespace-nowrap">
                                                    Detail
                                                </a>
                                                @if($volunteer->status_verifikasi == 'pending')
                                                    <form action="{{ route('admin.verifikasi-relawan.verify', $volunteer->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit"
                                                                class="bg-green-100 hover:bg-green-200 text-green-700 font-medium py-1 px-3 rounded-lg transition-colors whitespace-nowrap">
                                                            Setujui
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('admin.verifikasi-relawan.reject', $volunteer->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit"
                                                                class="bg-red-100 hover:bg-red-200 text-red-700 font-medium py-1 px-3 rounded-lg transition-colors whitespace-nowrap">
                                                            Tolak
                                                        </button>
                                                    </form>
                                                @endif
                                                <form action="{{ route('admin.verifikasi-relawan.destroy', $volunteer->id) }}" method="POST" class="inline"
                                                      onsubmit="return confirm('PERINGATAN: Yakin ingin menghapus? Data akan hilang permanen.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-1 px-3 rounded-lg transition-colors whitespace-nowrap">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                @if($volunteersByCampaign[$campaign->id]->count() > 5)
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            <div class="text-sm text-gray-500">
                                                Dan {{ $volunteersByCampaign[$campaign->id]->count() - 5 }} lainnya...
                                                <a href="{{ route('admin.verifikasi-relawan.by-campaign', $campaign->id) }}" class="text-blue-600 hover:underline ml-2">Lihat Semua</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            @endforeach
        @else
            <div class="card p-6 text-center">
                <p class="text-gray-500">Tidak ada pendaftaran relawan untuk kampanye manapun.</p>
            </div>
        @endif
</div>
@endsection