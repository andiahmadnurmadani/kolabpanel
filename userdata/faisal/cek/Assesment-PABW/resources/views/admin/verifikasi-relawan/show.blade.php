@extends('admin.layouts.master')

@section('content')
<div class="container px-6 mx-auto grid mb-20">
    
    {{-- Header & Tombol Kembali --}}
    <div class="mt-8 mb-6 flex justify-between items-center">
        <a href="{{ route('admin.verifikasi-relawan.index') }}" class="text-gray-500 hover:text-indigo-600 flex items-center gap-2 transition-colors font-medium">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar
        </a>
        <div class="text-sm text-gray-400">
            ID Lamaran: #{{ $volunteer->id }}
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- KOLOM KIRI: Data Diri & Kampanye (Sidebar) --}}
        <div class="lg:col-span-1 space-y-6">
            
            {{-- Card Profil Pelamar --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 text-center relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-24 bg-gradient-to-r from-indigo-500 to-purple-600"></div>
                
                {{-- Avatar --}}
                <div class="relative w-24 h-24 mx-auto -mt-2 mb-4">
                    <div class="w-full h-full bg-white rounded-full p-1 shadow-md">
                        <div class="w-full h-full bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center text-3xl font-bold uppercase">
                            {{ substr($volunteer->user->name ?? 'U', 0, 1) }}
                        </div>
                    </div>
                </div>

                <h2 class="text-xl font-bold text-gray-800">{{ $volunteer->user->name ?? 'User Terhapus' }}</h2>
                <p class="text-sm text-gray-500 mb-4">{{ $volunteer->user->email ?? '-' }}</p>
                
                <div class="border-t border-gray-100 pt-4 text-left space-y-3">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Alamat Domisili</p>
                        <p class="text-sm text-gray-700 font-medium">{{ $volunteer->alamat }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Posisi Dilamar</p>
                        <span class="inline-block mt-1 px-3 py-1 bg-blue-50 text-blue-700 rounded-lg text-sm font-bold border border-blue-100">
                            {{ $volunteer->posisi_dilamar }}
                        </span>
                    </div>
                </div>

                {{-- Tombol Download CV --}}
                <div class="mt-6">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider text-left mb-2">Dokumen Pendukung</p>
                    @if($volunteer->cv_path)
                        <a href="{{ asset('storage/' . $volunteer->cv_path) }}" target="_blank" class="flex items-center justify-between w-full p-3 bg-slate-50 border border-slate-200 rounded-xl hover:bg-slate-100 hover:border-indigo-300 transition-all group cursor-pointer">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center text-red-500">
                                    <i class="fas fa-file-pdf"></i>
                                </div>
                                <div class="text-left">
                                    <p class="text-sm font-bold text-gray-700 group-hover:text-indigo-700">Curriculum Vitae</p>
                                    <p class="text-[10px] text-gray-500">Klik untuk melihat PDF</p>
                                </div>
                            </div>
                            <i class="fas fa-external-link-alt text-gray-300 group-hover:text-indigo-500"></i>
                        </a>
                    @else
                        <div class="p-3 bg-red-50 text-red-600 text-sm rounded-lg border border-red-100">
                            <i class="fas fa-exclamation-circle mr-1"></i> File CV Tidak Ditemukan
                        </div>
                    @endif
                </div>
            </div>

            {{-- Card Info Kampanye --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Kampanye Tujuan</h3>
                <div class="flex gap-4">
                    <img src="{{ $volunteer->campaign->image }}" class="w-20 h-20 rounded-lg object-cover shadow-sm" onerror="this.src='https://via.placeholder.com/80x80?text=No+Image'">
                    <div>
                        <p class="text-sm font-bold text-gray-900 line-clamp-2 leading-tight mb-1">{{ $volunteer->campaign->judul }}</p>
                        <div class="flex items-center gap-1 text-xs text-gray-500 mb-2">
                            <i class="far fa-calendar-alt"></i> 
                            {{ \Carbon\Carbon::parse($volunteer->campaign->tanggal_mulai)->format('d M Y') }}
                        </div>
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-[10px] font-bold uppercase rounded">
                            {{ $volunteer->campaign->kategori }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: Jawaban Esai & Panel Aksi --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Jawaban Esai --}}
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-200">
                <div class="flex justify-between items-center mb-6 border-b pb-4">
                    <h3 class="text-lg font-bold text-gray-800">Detail Jawaban Formulir</h3>
                    
                    {{-- Status Badge --}}
                    @php
                        $statusColor = match($volunteer->status) {
                            'approved' => 'bg-green-100 text-green-700 border-green-200',
                            'rejected' => 'bg-red-100 text-red-700 border-red-200',
                            default    => 'bg-orange-100 text-orange-700 border-orange-200'
                        };
                    @endphp
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase border {{ $statusColor }}">
                        Status: {{ $volunteer->status }}
                    </span>
                </div>
                
                <div class="mb-2">
                    <label class="block text-sm font-bold text-indigo-900 uppercase tracking-wider mb-3">
                        <i class="fas fa-quote-left mr-2 text-indigo-300"></i>
                        Mengapa Anda ingin bergabung?
                    </label>
                    <div class="bg-slate-50 p-6 rounded-xl border border-slate-200 text-gray-700 leading-relaxed text-lg font-serif italic">
                        "{{ $volunteer->alasan_bergabung }}"
                    </div>
                </div>
                
                <div class="mt-4 text-right">
                    <p class="text-xs text-gray-400">Diajukan pada: {{ $volunteer->created_at->format('d F Y, H:i') }} WIB</p>
                </div>
            </div>

            {{-- PANEL KEPUTUSAN (Hanya muncul jika status masih Pending) --}}
            @if($volunteer->status == 'pending')
            <div class="bg-white p-8 rounded-2xl shadow-lg border border-indigo-100 relative overflow-hidden">
                {{-- Hiasan Background --}}
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-indigo-50 rounded-full opacity-50 blur-2xl"></div>
                
                <div class="relative z-10">
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Ambil Keputusan</h3>
                    <p class="text-sm text-gray-500 mb-6">
                        Pastikan Anda sudah meninjau profil dan CV pelamar. Tindakan ini akan memperbarui data kuota kampanye secara otomatis.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4">
                        
                        {{-- FORM TOLAK (ID: form-reject) --}}
                        <form id="form-reject" action="{{ route('admin.verifikasi-relawan.update', $volunteer->id) }}" method="POST" class="flex-1">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="rejected">
                            {{-- Button Type diganti jadi 'button' agar Trigger JS --}}
                            <button type="button" onclick="confirmReject()" class="w-full py-3.5 px-4 bg-white border-2 border-red-100 text-red-600 font-bold rounded-xl hover:bg-red-50 hover:border-red-200 hover:text-red-700 transition-all duration-200 flex justify-center items-center gap-2 group shadow-sm">
                                <i class="fas fa-times-circle text-lg group-hover:scale-110 transition-transform"></i> Tolak Lamaran
                            </button>
                        </form>

                        {{-- FORM TERIMA (ID: form-approve) --}}
                        <form id="form-approve" action="{{ route('admin.verifikasi-relawan.update', $volunteer->id) }}" method="POST" class="flex-1">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="approved">
                            {{-- Button Type diganti jadi 'button' agar Trigger JS --}}
                            <button type="button" onclick="confirmApprove()" class="w-full py-3.5 px-4 bg-indigo-600 text-white font-bold rounded-xl shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:shadow-indigo-300 transition-all duration-200 flex justify-center items-center gap-2 group">
                                <i class="fas fa-check-circle text-lg group-hover:scale-110 transition-transform"></i> Terima Relawan
                            </button>
                        </form>

                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>

{{-- SCRIPT SWEETALERT2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // 1. Logic Popup TOLAK
    function confirmReject() {
        Swal.fire({
            title: 'Tolak Pelamar?',
            text: "Status akan berubah menjadi 'Rejected'. Pelamar tidak dapat diterima kembali untuk kampanye ini.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444', // Merah
            cancelButtonColor: '#94a3b8', // Abu-abu
            confirmButtonText: 'Ya, Tolak',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            background: '#fff',
            customClass: {
                popup: 'rounded-3xl shadow-xl font-sans',
                title: 'text-xl font-bold text-gray-800',
                htmlContainer: 'text-sm text-gray-500',
                confirmButton: 'px-6 py-2.5 rounded-xl font-bold text-sm shadow-md',
                cancelButton: 'px-6 py-2.5 rounded-xl font-bold text-sm text-slate-600 bg-slate-100 hover:bg-slate-200'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-reject').submit();
            }
        })
    }

    // 2. Logic Popup TERIMA
    function confirmApprove() {
        Swal.fire({
            title: 'Terima Sebagai Relawan?',
            html: `
                <div class="text-left bg-indigo-50 p-4 rounded-lg border border-indigo-100 mb-2">
                    <p class="text-sm text-indigo-800 font-semibold mb-1">Konfirmasi:</p>
                    <ul class="list-disc pl-4 text-xs text-indigo-600 space-y-1">
                        <li>Status berubah menjadi <b>Approved</b></li>
                        <li>Kuota kampanye berkurang <b>1 orang</b></li>
                    </ul>
                </div>
            `,
            icon: 'question',
            iconColor: '#4f46e5',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5', // Indigo
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Ya, Terima!',
            cancelButtonText: 'Batal',
            background: '#fff',
            customClass: {
                popup: 'rounded-3xl shadow-xl font-sans',
                title: 'text-xl font-bold text-gray-800',
                confirmButton: 'px-6 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-indigo-200',
                cancelButton: 'px-6 py-2.5 rounded-xl font-bold text-sm text-slate-600 bg-slate-100 hover:bg-slate-200'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-approve').submit();
            }
        })
    }
</script>
@endsection