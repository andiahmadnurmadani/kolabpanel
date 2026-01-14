@extends('layouts.admin')

@section('page-title', 'Edit Konten')
@section('page-subtitle', 'Edit konten statis halaman')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('admin.content.index') }}" 
           class="inline-flex items-center text-gray-600 hover:text-gray-800 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali ke Daftar Konten
        </a>
    </div>

    <!-- Edit Form -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center space-x-3">
                @if($konten->type == 'image')
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-image text-purple-600 text-xl"></i>
                </div>
                @else
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-alt text-blue-600 text-xl"></i>
                </div>
                @endif
                <div>
                    <h2 class="text-xl font-bold text-gray-800">{{ ucwords(str_replace('_', ' ', $konten->key)) }}</h2>
                    <p class="text-sm text-gray-500">Key: {{ $konten->key }}</p>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.content.update', $konten) }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')

            <!-- Current Content Preview -->
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-3">
                    <i class="fas fa-eye mr-1"></i>
                    Konten Saat Ini
                </label>
                @if($konten->type == 'image')
                    @if($konten->value)
                    <div class="relative inline-block">
                        <img src="{{ $konten->value }}" alt="{{ $konten->key }}" class="max-w-md w-full h-auto rounded-lg border border-gray-300 shadow-sm">
                        <div class="mt-2 text-xs text-gray-500">
                            <i class="fas fa-link mr-1"></i>
                            {{ $konten->value }}
                        </div>
                    </div>
                    @else
                    <div class="w-full max-w-md h-48 bg-gray-100 rounded-lg border border-gray-300 flex items-center justify-center">
                        <div class="text-center">
                            <i class="fas fa-image text-gray-300 text-4xl mb-2"></i>
                            <p class="text-gray-500 text-sm">Belum ada gambar</p>
                        </div>
                    </div>
                    @endif
                @else
                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-300">
                        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $konten->value ?? 'Belum ada konten' }}</p>
                    </div>
                @endif
            </div>

            <!-- Edit Content -->
            @if($konten->type == 'image')
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-3">
                    <i class="fas fa-upload mr-1"></i>
                    Upload Gambar Baru
                </label>
                <div class="relative">
                    <input type="file" 
                           name="file" 
                           id="file"
                           accept="image/png,image/jpeg,image/jpg,image/svg+xml,image/webp"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-3 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer border border-gray-300 rounded-lg">
                    <p class="mt-2 text-xs text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        Format yang didukung: PNG, JPG, JPEG, SVG, WEBP (Maks. 2MB)
                    </p>
                </div>
                @error('file')
                <p class="mt-2 text-sm text-red-600">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    {{ $message }}
                </p>
                @enderror
            </div>

            <!-- Image Preview -->
            <div id="preview-container" class="mb-6 hidden">
                <label class="block text-sm font-semibold text-gray-700 mb-3">
                    <i class="fas fa-eye mr-1"></i>
                    Preview Gambar Baru
                </label>
                <img id="preview-image" src="" alt="Preview" class="max-w-md w-full h-auto rounded-lg border border-gray-300 shadow-sm">
            </div>
            @else
            <div class="mb-6">
                <label for="value" class="block text-sm font-semibold text-gray-700 mb-3">
                    <i class="fas fa-edit mr-1"></i>
                    Konten Teks
                </label>
                <textarea 
                    name="value" 
                    id="value" 
                    rows="6"
                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                    placeholder="Masukkan konten teks...">{{ old('value', $konten->value) }}</textarea>
                @error('value')
                <p class="mt-2 text-sm text-red-600">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    {{ $message }}
                </p>
                @enderror
                <p class="mt-2 text-xs text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Karakter: <span id="char-count">{{ strlen($konten->value ?? '') }}</span>
                </p>
            </div>
            @endif

            <!-- Meta Information -->
            <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <h3 class="font-semibold text-gray-700 mb-3">
                    <i class="fas fa-info-circle mr-1"></i>
                    Informasi
                </h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">Tipe Konten</p>
                        <p class="font-semibold text-gray-800">{{ ucfirst($konten->type) }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Terakhir Diupdate</p>
                        <p class="font-semibold text-gray-800">{{ $konten->updated_at ? $konten->updated_at->format('d M Y, H:i') : '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <a href="{{ route('admin.content.index') }}" 
                   class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-semibold">
                    <i class="fas fa-times mr-2"></i>
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Character counter for text content
    @if($konten->type == 'text')
    const textarea = document.getElementById('value');
    const charCount = document.getElementById('char-count');
    
    if (textarea && charCount) {
        textarea.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });
    }
    @endif

    // Image preview for file upload
    @if($konten->type == 'image')
    const fileInput = document.getElementById('file');
    const previewContainer = document.getElementById('preview-container');
    const previewImage = document.getElementById('preview-image');
    
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            
            if (file) {
                // Validate file type
                const validTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/svg+xml', 'image/webp'];
                if (!validTypes.includes(file.type)) {
                    alert('Tipe file tidak valid. Gunakan PNG, JPG, JPEG, SVG, atau WEBP.');
                    this.value = '';
                    previewContainer.classList.add('hidden');
                    return;
                }
                
                // Validate file size (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('Ukuran file terlalu besar. Maksimal 2MB.');
                    this.value = '';
                    previewContainer.classList.add('hidden');
                    return;
                }
                
                // Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                previewContainer.classList.add('hidden');
            }
        });
    }
    @endif
</script>
@endpush
@endsection
