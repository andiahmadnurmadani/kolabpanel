@extends('admin.layouts.master')

@section('content')
<div class="container px-6 mx-auto grid">
    <h2 class="my-6 text-2xl font-semibold text-gray-700">
        Buat Kampanye Donasi Baru
    </h2>

    <div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md">

        {{-- Form Start --}}
        <form action="{{ route('admin.campaigns.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- 1. Title --}}
            <label class="block text-sm">
                <span class="text-gray-700">Judul Kampanye Donasi</span>
                <input name="title" class="block w-full mt-1 text-sm border-gray-300 rounded-md focus:border-purple-400 focus:outline-none focus:shadow-outline-purple form-input" placeholder="Contoh: Bantu Korban Bencana Alam..." required />
            </label>

            {{-- 2. Description --}}
            <label class="block mt-4 text-sm">
                <span class="text-gray-700">Deskripsi Lengkap</span>
                <textarea name="description" class="block w-full mt-1 text-sm border-gray-300 rounded-md focus:border-purple-400 focus:outline-none focus:shadow-outline-purple form-textarea" rows="5" placeholder="Jelaskan tujuan dan kebutuhan kampanye donasi ini..." required></textarea>
            </label>

            {{-- 3. Grid (Target Amount & Current Amount) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <label class="block text-sm">
                    <span class="text-gray-700">Target Donasi (Rp)</span>
                    <input type="number" name="target_amount" class="block w-full mt-1 text-sm border-gray-300 rounded-md focus:border-purple-400 focus:outline-none focus:shadow-outline-purple form-input" placeholder="10000000" required />
                </label>

                <label class="block text-sm">
                    <span class="text-gray-700">Donasi Terkumpul (Rp)</span>
                    <input type="number" name="current_amount" class="block w-full mt-1 text-sm border-gray-300 rounded-md focus:border-purple-400 focus:outline-none focus:shadow-outline-purple form-input" placeholder="0" />
                </label>
            </div>

            {{-- 4. Grid (Status & End Date) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <label class="block text-sm">
                    <span class="text-gray-700">Status</span>
                    <select name="status" class="block w-full mt-1 text-sm border-gray-300 rounded-md focus:border-purple-400 focus:outline-none focus:shadow-outline-purple form-select">
                        <option value="Active">Active</option>
                        <option value="Completed">Completed</option>
                        <option value="Inactive">Inactive</option>
                        <option value="Pending">Pending</option>
                    </select>
                </label>

                <label class="block text-sm">
                    <span class="text-gray-700">Tanggal Berakhir</span>
                    <input type="date" name="end_date" class="block w-full mt-1 text-sm border-gray-300 rounded-md focus:border-purple-400 focus:outline-none focus:shadow-outline-purple form-input" required />
                </label>
            </div>

            {{-- 5. Yayasan --}}
            <label class="block mt-4 text-sm">
                <span class="text-gray-700">Nama Yayasan</span>
                <input name="yayasan" class="block w-full mt-1 text-sm border-gray-300 rounded-md focus:border-purple-400 focus:outline-none focus:shadow-outline-purple form-input" placeholder="Contoh: Yayasan Peduli Lingkungan Indonesia" />
            </label>

            {{-- 6. Kategori --}}
            <label class="block mt-4 text-sm">
                <span class="text-gray-700">Kategori Kampanye</span>
                <select name="kategori" class="block w-full mt-1 text-sm border-gray-300 rounded-md focus:border-purple-400 focus:outline-none focus:shadow-outline-purple form-select" required>
                    <option value="">Pilih Kategori</option>
                    <option value="Lingkungan">Lingkungan</option>
                    <option value="Kesehatan">Kesehatan</option>
                    <option value="Pendidikan">Pendidikan</option>
                    <option value="Sosial Kemanusiaan">Sosial Kemanusiaan</option>
                    <option value="Bencana Alam">Bencana Alam</option>
                </select>
            </label>

            {{-- 5. Upload Gambar --}}
            <label class="block mt-4 text-sm">
                <span class="text-gray-700">Foto Sampul (Cover)</span>
                <input type="file" name="image" class="block w-full mt-1 text-sm form-input border border-gray-300 rounded-md p-2" />
                <span class="text-xs text-gray-500">Format: JPG, PNG. Max: 2MB.</span>
            </label>

            {{-- Tombol Submit --}}
            <div class="mt-6">
                <button type="submit" class="px-5 py-3 font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                    Simpan Kampanye Donasi
                </button>
                <a href="{{ route('admin.campaigns.index') }}" class="px-5 py-3 ml-2 font-medium leading-5 text-gray-700 transition-colors duration-150 bg-gray-100 border border-transparent rounded-lg active:bg-gray-200 hover:bg-gray-200 focus:outline-none focus:shadow-outline-gray">
                    Batal
                </a>
            </div>

        </form>
    </div>
</div>
@endsection