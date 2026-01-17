@extends('admin.layouts.master')

@section('content')
<div class="container px-6 mx-auto grid mb-20">

    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row justify-between items-center my-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Edit Kampanye</h2>
            <p class="text-sm text-gray-500 mt-1">Perbarui data kampanye relawan.</p>
        </div>
        <a href="{{ route('admin.relawan.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    {{-- Form dengan Method PUT --}}
    <form action="{{ route('admin.relawan.update', $campaign->id) }}" method="POST" enctype="multipart/form-data" class="w-full">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- KOLOM KIRI --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Card Judul & Deskripsi --}}
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <h3 class="font-semibold text-gray-800 border-b pb-3 mb-4">Detail Utama</h3>

                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Judul Kegiatan <span class="text-red-500">*</span></label>
                        <input name="judul" type="text"
                            class="w-full pl-4 pr-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                            value="{{ old('judul', $campaign->judul) }}" required />
                        @error('judul') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Lengkap <span class="text-red-500">*</span></label>
                        <textarea name="deskripsi" rows="8"
                            class="w-full p-4 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                            required>{{ old('deskripsi', $campaign->deskripsi) }}</textarea>
                        @error('deskripsi') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Card Lokasi & Waktu --}}
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <h3 class="font-semibold text-gray-800 border-b pb-3 mb-4">Lokasi & Waktu</h3>

                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi Kegiatan</label>
                        <input name="lokasi" type="text"
                            class="w-full pl-4 pr-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            value="{{ old('lokasi', $campaign->lokasi) }}" required />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai"
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300"
                                value="{{ old('tanggal_mulai', $campaign->tanggal_mulai) }}" required />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai"
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300"
                                value="{{ old('tanggal_selesai', $campaign->tanggal_selesai) }}" required />
                        </div>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN --}}
            <div class="lg:col-span-1 space-y-6">

                {{-- Card Upload Gambar --}}
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <h3 class="font-semibold text-gray-800 mb-4">Banner Kampanye</h3>

                    {{-- Preview Gambar Lama --}}
                    @if($campaign->image)
                    <div class="mb-4">
                        <p class="text-xs text-gray-500 mb-2">Gambar Saat Ini:</p>
                        <img src="{{ $campaign->image }}"
                             class="w-full h-32 object-cover rounded-lg border border-gray-200"
                             onerror="this.onerror=null; this.src='https://placehold.co/400x128?text=No+Image';">
                    </div>
                    @endif

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Ganti Gambar (Opsional)</label>
                        <input type="file" name="image"
                            class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none"
                            accept="image/*" />
                        <p class="text-xs text-gray-500 mt-2">Biarkan kosong jika tidak ingin mengganti gambar.</p>
                    </div>
                </div>

                {{-- Card Kategori & Kuota --}}
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <h3 class="font-semibold text-gray-800 mb-4">Pengaturan</h3>

                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kategori Isu</label>
                        <select name="kategori" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 bg-white">
                            @foreach(['Lingkungan', 'Pendidikan', 'Kesehatan', 'Sosial', 'Bencana'] as $cat)
                            <option value="{{ $cat }}" {{ old('kategori', $campaign->kategori) == $cat ? 'selected' : '' }}>
                                {{ $cat }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Target Relawan</label>
                        <div class="flex items-center">
                            <input type="number" name="kuota_total"
                                class="w-full px-4 py-2.5 rounded-l-lg border border-gray-300"
                                value="{{ old('kuota_total', $campaign->kuota_total) }}" required min="1" />
                            <span class="inline-flex items-center px-4 py-2.5 rounded-r-lg border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">Orang</span>
                        </div>
                    </div>
                </div>

                {{-- Submit Button --}}
                <button type="submit" class="w-full py-3.5 px-4 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-xl shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                    <i class="fas fa-save mr-2"></i> Simpan Perubahan
                </button>
            </div>
        </div>
    </form>
</div>
@endsection