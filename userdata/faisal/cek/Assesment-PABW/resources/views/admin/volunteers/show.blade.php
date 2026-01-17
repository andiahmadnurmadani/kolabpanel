@extends('admin.layouts.master')

@section('content')
<div class="max-w-5xl mx-auto p-4 sm:p-6">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <h1 class="text-2xl font-bold text-gray-800">Detail Pendaftaran Relawan</h1>
                <a href="javascript:history.back()" class="bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white py-2 px-4 rounded-lg flex items-center justify-center transition-all duration-200 shadow-sm hover:shadow-md">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 text-green-700 px-6 py-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <div class="bg-gray-50 rounded-lg p-5">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200 flex items-center">
                        <i class="fas fa-user-circle text-blue-500 mr-2"></i> Informasi Pribadi
                    </h3>
                    <div class="space-y-4">
                        <div class="flex flex-col sm:flex-row sm:justify-between">
                            <span class="font-medium text-gray-600 w-1/3">Nama Lengkap:</span>
                            <span class="text-gray-800 sm:text-right">{{ $volunteer->nama_lengkap }}</span>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:justify-between">
                            <span class="font-medium text-gray-600 w-1/3">Email:</span>
                            <span class="text-gray-800 sm:text-right">{{ $volunteer->email }}</span>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:justify-between">
                            <span class="font-medium text-gray-600 w-1/3">WhatsApp:</span>
                            <span class="text-gray-800 sm:text-right">{{ $volunteer->whatsapp }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-5">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200 flex items-center">
                        <i class="fas fa-clipboard-list text-green-500 mr-2"></i> Kampanye & Status
                    </h3>
                    <div class="space-y-4">
                        <div class="flex flex-col sm:flex-row sm:justify-between">
                            <span class="font-medium text-gray-600 w-1/3">Kampanye:</span>
                            <span class="sm:text-right">
                                @if($volunteer->campaign)
                                    <span class="text-blue-600 font-medium">{{ $volunteer->campaign->judul }}</span>
                                @else
                                    <span class="text-gray-500">-</span>
                                @endif
                            </span>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:justify-between">
                            <span class="font-medium text-gray-600 w-1/3">Status Verifikasi:</span>
                            <span class="sm:text-right">
                                <span class="px-3 py-1 rounded-full text-sm font-medium
                                    @if($volunteer->status_verifikasi == 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($volunteer->status_verifikasi == 'disetujui') bg-green-100 text-green-800
                                    @elseif($volunteer->status_verifikasi == 'ditolak') bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($volunteer->status_verifikasi) }}
                                </span>
                            </span>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:justify-between">
                            <span class="font-medium text-gray-600 w-1/3">Tgl Pendaftaran:</span>
                            <span class="text-gray-800 sm:text-right">{{ $volunteer->created_at->format('d M Y H:i') }}</span>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:justify-between">
                            <span class="font-medium text-gray-600 w-1/3">Tgl Update:</span>
                            <span class="text-gray-800 sm:text-right">{{ $volunteer->updated_at->format('d M Y H:i') }}</span>
                        </div>
                    </div>

                    <!-- Campaign Image -->
                    @if($volunteer->campaign && $volunteer->campaign->image)
                    <div class="mt-4">
                        <h4 class="font-medium text-gray-600 mb-2">Gambar Kampanye:</h4>
                        <div class="flex justify-center">
                            <img src="{{ $volunteer->campaign->image }}"
                                 alt="Gambar Kampanye"
                                 class="max-w-full max-h-48 rounded-lg border shadow-sm object-contain"
                                 onerror="this.onerror=null; this.src='https://placehold.co/400x300?text=No+Image';">
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200 flex items-center">
                    <i class="fas fa-heart text-red-500 mr-2"></i> Motivasi
                </h3>
                <div class="bg-blue-50 border border-blue-100 p-5 rounded-lg">
                    <p class="text-gray-700 leading-relaxed">{{ $volunteer->motivasi }}</p>
                </div>
            </div>

            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200 flex items-center">
                    <i class="fas fa-tools text-purple-500 mr-2"></i> Keahlian
                </h3>
                <div class="bg-indigo-50 border border-indigo-100 p-5 rounded-lg">
                    <p class="text-gray-700 leading-relaxed">{{ $volunteer->keahlian }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection