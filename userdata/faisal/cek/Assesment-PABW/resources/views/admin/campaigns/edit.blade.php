@extends('admin.layouts.master')

@section('content')
<div class="container px-6 mx-auto grid">
    <h2 class="my-6 text-2xl font-semibold text-gray-700">Edit Kampanye Donasi</h2>

    <div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md">
        <form action="{{ route('admin.campaigns.update', $campaign->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT') {{-- PENTING: Method PUT untuk update --}}

            {{-- Title --}}
            <div class="mb-4">
                <label class="block text-sm text-gray-700 font-bold mb-2">Judul</label>
                <input name="title" value="{{ old('title', $campaign->title) }}" class="w-full border px-3 py-2 rounded-lg focus:outline-none focus:border-blue-500" required>
            </div>

            {{-- Description --}}
            <div class="mb-4">
                <label class="block text-sm text-gray-700 font-bold mb-2">Deskripsi</label>
                <textarea name="description" rows="5" class="w-full border px-3 py-2 rounded-lg" required>{{ old('description', $campaign->description) }}</textarea>
            </div>

            {{-- Target Amount & Current Amount --}}
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm text-gray-700 font-bold mb-2">Target Donasi (Rp)</label>
                    <input type="number" name="target_amount" value="{{ old('target_amount', $campaign->target_amount) }}" class="w-full border px-3 py-2 rounded-lg" required>
                </div>
                <div>
                    <label class="block text-sm text-gray-700 font-bold mb-2">Donasi Terkumpul (Rp)</label>
                    <input type="number" name="current_amount" value="{{ old('current_amount', $campaign->current_amount) }}" class="w-full border px-3 py-2 rounded-lg">
                </div>
            </div>

            {{-- Status & End Date --}}
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm text-gray-700 font-bold mb-2">Status</label>
                    <select name="status" class="w-full border px-3 py-2 rounded-lg">
                        <option value="Active" {{ $campaign->status == 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ $campaign->status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="Completed" {{ $campaign->status == 'Completed' ? 'selected' : '' }}>Completed</option>
                        <option value="Pending" {{ $campaign->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-700 font-bold mb-2">Tanggal Berakhir</label>
                    <input type="date" name="end_date" value="{{ old('end_date', $campaign->end_date) }}" class="w-full border px-3 py-2 rounded-lg" required>
                </div>
            </div>

            {{-- Yayasan --}}
            <div class="mb-4">
                <label class="block text-sm text-gray-700 font-bold mb-2">Nama Yayasan</label>
                <input name="yayasan" value="{{ old('yayasan', $campaign->yayasan) }}" class="w-full border px-3 py-2 rounded-lg" placeholder="Contoh: Yayasan Peduli Lingkungan Indonesia">
            </div>

            {{-- Kategori --}}
            <div class="mb-4">
                <label class="block text-sm text-gray-700 font-bold mb-2">Kategori</label>
                <select name="kategori" class="w-full border px-3 py-2 rounded-lg" required>
                    <option value="">Pilih Kategori</option>
                    <option value="Lingkungan" {{ old('kategori', $campaign->kategori) == 'Lingkungan' ? 'selected' : '' }}>Lingkungan</option>
                    <option value="Kesehatan" {{ old('kategori', $campaign->kategori) == 'Kesehatan' ? 'selected' : '' }}>Kesehatan</option>
                    <option value="Pendidikan" {{ old('kategori', $campaign->kategori) == 'Pendidikan' ? 'selected' : '' }}>Pendidikan</option>
                    <option value="Sosial Kemanusiaan" {{ old('kategori', $campaign->kategori) == 'Sosial Kemanusiaan' ? 'selected' : '' }}>Sosial Kemanusiaan</option>
                    <option value="Bencana Alam" {{ old('kategori', $campaign->kategori) == 'Bencana Alam' ? 'selected' : '' }}>Bencana Alam</option>
                </select>
            </div>

            {{-- Gambar --}}
            <div class="mb-6">
                <label class="block text-sm text-gray-700 font-bold mb-2">Ganti Gambar (Opsional)</label>
                <input type="file" name="image" class="w-full border px-3 py-2 rounded-lg">
                @if($campaign->image)
                <p class="text-xs text-gray-500 mt-2">Gambar saat ini:</p>
                <img src="{{ asset('storage/' . $campaign->image) }}" class="h-20 w-auto rounded mt-1">
                @endif
            </div>

            {{-- Buttons --}}
            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 transition">Update Kampanye Donasi</button>
                <a href="{{ route('admin.campaigns.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg font-bold hover:bg-gray-400 transition">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection