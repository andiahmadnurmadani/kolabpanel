@extends('admin.layouts.master')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    {{-- Header & Navigasi --}}
    <div class="mb-8 flex items-center justify-between">
        <div>
            <a href="{{ route('admin.withdrawals.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center mb-2">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Dashboard Keuangan
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Buku Kas: {{ $campaign->title }}</h1>
            <p class="text-gray-500 text-sm mt-1">Laporan transparansi penggunaan dana kampanye.</p>
        </div>
    </div>

    {{-- Kartu Ringkasan --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <p class="text-xs font-bold text-gray-400 uppercase">Total Dana Masuk</p>
            <p class="text-2xl font-bold text-emerald-600">+ Rp {{ number_format($totalIn, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <p class="text-xs font-bold text-gray-400 uppercase">Total Dana Keluar</p>
            <p class="text-2xl font-bold text-red-600">- Rp {{ number_format($totalOut, 0, ',', '.') }}</p>
        </div>
        <div class="bg-blue-50 p-6 rounded-xl shadow-sm border border-blue-100">
            <p class="text-xs font-bold text-blue-400 uppercase">Sisa Saldo Saat Ini</p>
            <p class="text-2xl font-bold text-blue-700">Rp {{ number_format($balance, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Tabel Riwayat Transaksi --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <h3 class="text-lg font-bold text-gray-800">Detail Pengeluaran</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Kategori & Keterangan</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Nominal</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Bukti Nota</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Admin Pencatat</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($campaign->withdrawals as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($log->transferred_at)->format('d M Y, H:i') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 mb-1">
                                    {{ $log->account_number }} {{-- Kategori disimpan di sini --}}
                                </span>
                                <p class="text-sm text-gray-900 font-medium">{{ $log->account_holder_name }}</p> {{-- Keterangan disimpan di sini --}}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-red-600">
                                - Rp {{ number_format($log->amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($log->proof_file)
                                    <a href="{{ asset('storage/' . $log->proof_file) }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-xs font-bold underline flex justify-center items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        Lihat Foto
                                    </a>
                                @else
                                    <span class="text-gray-400 text-xs italic">Tidak ada bukti</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $log->user ? $log->user->name : 'Admin' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">
                                Belum ada pengeluaran dana untuk kampanye ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection