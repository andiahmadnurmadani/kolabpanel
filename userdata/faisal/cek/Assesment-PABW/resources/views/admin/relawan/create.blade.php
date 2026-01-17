@extends('admin.layouts.master')

@section('content')
<div class="container px-6 mx-auto grid mb-20">

    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row justify-between items-center my-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Buat Kampanye Baru</h2>
            <p class="text-sm text-gray-500 mt-1">Isi formulir di bawah untuk mempublikasikan kegiatan relawan.</p>
        </div>
        <a href="{{ route('admin.relawan.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    {{-- Main Form Card --}}
    <form action="{{ route('admin.relawan.store') }}" method="POST" enctype="multipart/form-data" class="w-full">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- KOLOM KIRI: Informasi Utama (2/3 lebar) --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Card Judul & Deskripsi --}}
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <h3 class="font-semibold text-gray-800 border-b pb-3 mb-4">Detail Utama</h3>

                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Judul Kegiatan <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input name="judul" type="text"
                                class="w-full pl-4 pr-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors text-gray-800 placeholder-gray-400"
                                placeholder="Contoh: Aksi Bersih Pantai & Daur Ulang"
                                value="{{ old('judul') }}" required />
                        </div>
                        @error('judul') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Lengkap <span class="text-red-500">*</span></label>
                        <textarea name="deskripsi" rows="8"
                            class="w-full p-4 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors text-gray-800 placeholder-gray-400"
                            placeholder="Jelaskan tujuan kegiatan, tugas relawan, dan persyaratan yang dibutuhkan..." required>{{ old('deskripsi') }}</textarea>
                        <p class="text-xs text-gray-500 mt-2 text-right">Minimal 50 karakter agar relawan paham.</p>
                        @error('deskripsi') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Card Lokasi & Waktu --}}
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <h3 class="font-semibold text-gray-800 border-b pb-3 mb-4">Lokasi & Waktu Pelaksanaan</h3>

                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi Kegiatan</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-map-marker-alt text-gray-400"></i>
                            </span>
                            <input name="lokasi" type="text"
                                class="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                placeholder="Nama Gedung / Jalan / Kota"
                                value="{{ old('lokasi') }}" required />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai"
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                value="{{ old('tanggal_mulai') }}" required />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai"
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                value="{{ old('tanggal_selesai') }}" required />
                        </div>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: Sidebar --}}
            <div class="lg:col-span-1 space-y-6">

                {{-- Card Upload Gambar (VERSI STABIL) --}}
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <h3 class="font-semibold text-gray-800 mb-4">Banner Kampanye</h3>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Pilih Gambar</label>
                        {{-- Input File Standar dengan Styling Tailwind --}}
                        <input type="file" name="image"
                            class="block w-full text-sm text-slate-500
                file:mr-4 file:py-2 file:px-4
                file:rounded-full file:border-0
                file:text-sm file:font-semibold
                file:bg-indigo-50 file:text-indigo-700
                hover:file:bg-indigo-100
                border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none"
                            accept="image/*"
                            required />
                        <p class="text-xs text-gray-500 mt-2">Format: JPG, PNG. Maks: 2MB.</p>
                    </div>
                    @error('image') <p class="text-xs text-red-600 mt-2">{{ $message }}</p> @enderror
                </div>

                {{-- Card Kategori & Kuota --}}
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <h3 class="font-semibold text-gray-800 mb-4">Pengaturan</h3>

                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kategori Isu</label>
                        <select name="kategori" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                            <option value="" disabled selected>Pilih Kategori...</option>
                            <option value="Lingkungan">Lingkungan</option>
                            <option value="Pendidikan">Pendidikan</option>
                            <option value="Kesehatan">Kesehatan</option>
                            <option value="Sosial">Sosial Kemanusiaan</option>
                            <option value="Bencana">Bencana Alam</option>
                        </select>
                    </div>

                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Target Relawan</label>
                        <div class="flex items-center">
                            <input type="number" name="kuota_total"
                                class="w-full px-4 py-2.5 rounded-l-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="0" value="{{ old('kuota_total') }}" required min="1" />
                            <span class="inline-flex items-center px-4 py-2.5 rounded-r-lg border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                Orang
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Submit Button --}}
                <button type="submit" class="w-full py-3.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-200 transform hover:-translate-y-0.5 transition-all duration-200 flex justify-center items-center gap-2">
                    <i class="fas fa-paper-plane"></i> Terbitkan Kampanye
                </button>
            </div>
    </form>
</div>

{{-- Script Sederhana untuk Preview Nama File (Opsional) --}}
<script>
    const fileInput = document.getElementById('dropzone-file');
    const dropzoneLabel = document.querySelector('label[for="dropzone-file"]');

    fileInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            dropzoneLabel.innerHTML = `
                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                    <i class="fas fa-check-circle text-3xl text-green-500 mb-3"></i>
                    <p class="mb-2 text-sm text-gray-700 font-semibold">${this.files[0].name}</p>
                    <p class="text-xs text-gray-500">Siap untuk diupload</p>
                </div>
            `;
            dropzoneLabel.classList.remove('bg-indigo-50');
            dropzoneLabel.classList.add('bg-green-50', 'border-green-300');
        }
    });
</script>
@endsection