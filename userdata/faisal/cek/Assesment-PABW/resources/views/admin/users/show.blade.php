@extends('admin.layouts.master')

@section('content')
<div class="p-6 max-w-7xl mx-auto">
    
    {{-- Tombol Kembali --}}
    <div class="mb-6">
        <a href="{{ route('admin.profiles.index') }}" class="text-gray-500 hover:text-blue-600 flex items-center gap-2 transition-colors font-medium">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar User
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- KOLOM KIRI: Form Edit Profil --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="h-24 bg-gradient-to-r from-blue-500 to-indigo-600"></div>
                <div class="px-6 pb-6 text-center relative">
                    {{-- Avatar --}}
                    <div class="w-24 h-24 mx-auto -mt-12 bg-white rounded-full p-1.5 shadow-lg">
                        <div class="w-full h-full bg-slate-100 text-slate-500 rounded-full flex items-center justify-center text-3xl font-bold">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                    </div>
                    
                    <h2 class="mt-3 text-xl font-bold text-gray-800">{{ $user->name }}</h2>
                    <p class="text-sm text-gray-500">Member sejak {{ $user->created_at->format('M Y') }}</p>
                </div>

                <div class="p-6 border-t border-gray-100">
                    <form action="{{ route('admin.profiles.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Nama --}}
                        <div class="mb-4">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ $user->name }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>

                        {{-- Email --}}
                        <div class="mb-4">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Email</label>
                            <input type="email" name="email" value="{{ $user->email }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>

                        {{-- Role --}}
                        <div class="mb-4">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Role Akun</label>
                            <select name="role" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User Biasa</option>
                                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                        </div>

                        {{-- Password (Opsional) --}}
                        <div class="mb-6">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Ubah Password <span class="text-gray-300 font-normal">(Opsional)</span></label>
                            <input type="password" name="password" placeholder="Isi jika ingin merubah..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>

                        <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg transition-colors shadow-lg shadow-blue-200">
                            Simpan Perubahan
                        </button>
                    </form>

                    {{-- Tombol Hapus --}}
                    <form action="{{ route('admin.profiles.destroy', $user->id) }}" method="POST" class="mt-4 pt-4 border-t border-gray-100" onsubmit="return confirm('Yakin ingin menghapus user ini secara permanen?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full py-2.5 text-red-500 hover:bg-red-50 font-bold rounded-lg transition-colors border border-transparent hover:border-red-100">
                            <i class="fas fa-trash-alt mr-2"></i> Hapus User
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: Riwayat --}}
        <div class="lg:col-span-2 space-y-8">
            
            {{-- 1. Riwayat Donasi --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-slate-50 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800"><i class="fas fa-donate text-blue-500 mr-2"></i> Riwayat Donasi</h3>
                    <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2 py-1 rounded-full">{{ $user->donations->count() }} Transaksi</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-white">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase">ID Donasi</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase">Jumlah</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-400 uppercase">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($user->donations as $donation)
                            <tr>
                                <td class="px-6 py-3 text-sm text-gray-600 font-mono">#{{ $donation->id }}</td>
                                <td class="px-6 py-3 text-sm font-bold text-gray-800">Rp {{ number_format($donation->amount, 0, ',', '.') }}</td>
                                <td class="px-6 py-3 text-sm">
                                    @if($donation->status == 'paid')
                                        <span class="text-green-600 font-bold text-xs bg-green-50 px-2 py-1 rounded">Berhasil</span>
                                    @elseif($donation->status == 'pending')
                                        <span class="text-yellow-600 font-bold text-xs bg-yellow-50 px-2 py-1 rounded">Pending</span>
                                    @else
                                        <span class="text-red-600 font-bold text-xs bg-red-50 px-2 py-1 rounded">Gagal</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-sm text-gray-500 text-right">{{ $donation->created_at->format('d M Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-400">Belum ada riwayat donasi.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 2. Riwayat Relawan --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-slate-50 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800"><i class="fas fa-hands-helping text-orange-500 mr-2"></i> Riwayat Relawan</h3>
                    <span class="bg-orange-100 text-orange-700 text-xs font-bold px-2 py-1 rounded-full">{{ $user->volunteerApplications->count() }} Lamaran</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-white">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase">Kampanye</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase">Posisi</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-400 uppercase">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($user->volunteerApplications as $app)
                            <tr>
                                <td class="px-6 py-3 text-sm text-gray-800 font-medium">{{ $app->campaign->judul ?? 'Kampanye Dihapus' }}</td>
                                <td class="px-6 py-3 text-sm text-gray-600">{{ $app->posisi_dilamar }}</td>
                                <td class="px-6 py-3 text-sm">
                                    @if($app->status == 'approved')
                                        <span class="text-green-600 font-bold text-xs bg-green-50 px-2 py-1 rounded">Diterima</span>
                                    @elseif($app->status == 'pending')
                                        <span class="text-yellow-600 font-bold text-xs bg-yellow-50 px-2 py-1 rounded">Menunggu</span>
                                    @else
                                        <span class="text-red-600 font-bold text-xs bg-red-50 px-2 py-1 rounded">Ditolak</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-sm text-gray-500 text-right">{{ $app->created_at->format('d M Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-400">Belum ada pendaftaran relawan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection