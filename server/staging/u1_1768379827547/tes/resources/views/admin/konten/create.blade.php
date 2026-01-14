@extends('layouts.admin')

@section('page-title', 'Tambah Konten Baru')
@section('page-subtitle', 'Buat konten statis baru untuk halaman')

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

    <!-- Create Form -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-800">Formulir Konten Baru</h2>
            <p class="text-sm text-gray-500 mt-1">Isi formulir di bawah untuk membuat konten statis baru</p>
        </div>

        <form action="{{ route('admin.content.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf

            <!-- Content Key -->
            <div class="mb-6">
                <label for="key" class="block text-sm font-semibold text-gray-700 mb-3">
                    <i class="fas fa-key mr-1"></i>
                    Key Konten <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="key" 
                       id="key" 
                       value="{{ old('key') }}"
                       class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                       placeholder="e.g., site_logo, hero_title, about_description"
                       required>
                @error('key')
                <p class="mt-2 text-sm text-red-600">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    {{ $message }}
                </p>
                @enderror
                <p class="mt-2 text-xs text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Gunakan format snake_case (huruf kecil dengan underscore). Contoh: hero_title, footer_text
                </p>
            </div>

            <!-- Content Type -->
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-3">
                    <i class="fas fa-list mr-1"></i>
                    Tipe Konten <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-2 gap-4">
                    <label class="relative">
                        <input type="radio" 
                               name="type" 
                               value="text" 
                               class="peer sr-only" 
                               checked
                               onchange="toggleContentInput()">
                        <div class="p-4 border-2 border-gray-300 rounded-lg cursor-pointer peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all hover:border-gray-400">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-file-alt text-blue-600 text-xl"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">Teks</p>
                                    <p class="text-xs text-gray-500">Konten berupa teks</p>
                                </div>
                            </div>
                        </div>
                    </label>

                    <label class="relative">
                        <input type="radio" 
                               name="type" 
                               value="image" 
                               class="peer sr-only"
                               onchange="toggleContentInput()">
                        <div class="p-4 border-2 border-gray-300 rounded-lg cursor-pointer peer-checked:border-purple-500 peer-checked:bg-purple-50 transition-all hover:border-gray-400">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-image text-purple-600 text-xl"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">Gambar</p>
                                    <p class="text-xs text-gray-500">Konten berupa file gambar</p>
                                </div>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Text Content Input -->
            <div id="text-input" class="mb-6">
                <label for="value" class="block text-sm font-semibold text-gray-700 mb-3">
                    <i class="fas fa-edit mr-1"></i>
                    Konten Teks
                </label>
                <textarea 
                    name="value" 
                    id="value" 
                    rows="6"
                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                    placeholder="Masukkan konten teks...">{{ old('value') }}</textarea>
                @error('value')
                <p class="mt-2 text-sm text-red-600">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    {{ $message }}
                </p>
                @enderror
                <p class="mt-2 text-xs text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Karakter: <span id="char-count">0</span>
                </p>
            </div>

            <!-- Image Content Input -->
            <div id="image-input" class="mb-6 hidden">
                <label class="block text-sm font-semibold text-gray-700 mb-3">
                    <i class="fas fa-upload mr-1"></i>
                    Upload Gambar
                </label>
                <div class="relative">
                    <input type="file" 
                           name="file" 
                           id="file"
                           accept="image/png,image/jpeg,image/jpg,image/svg+xml,image/webp"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-3 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 cursor-pointer border border-gray-300 rounded-lg">
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

                <!-- Image Preview -->
                <div id="preview-container" class="mt-4 hidden">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        <i class="fas fa-eye mr-1"></i>
                        Preview Gambar
                    </label>
                    <img id="preview-image" src="" alt="Preview" class="max-w-md w-full h-auto rounded-lg border border-gray-300 shadow-sm">
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
                    Simpan Konten
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Toggle between text and image input
    function toggleContentInput() {
        const type = document.querySelector('input[name="type"]:checked').value;
        const textInput = document.getElementById('text-input');
        const imageInput = document.getElementById('image-input');
        
        if (type === 'text') {
            textInput.classList.remove('hidden');
            imageInput.classList.add('hidden');
        } else {
            textInput.classList.add('hidden');
            imageInput.classList.remove('hidden');
        }
    }

    // Character counter for text content
    const textarea = document.getElementById('value');
    const charCount = document.getElementById('char-count');
    
    if (textarea && charCount) {
        textarea.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });
    }

    // Image preview for file upload
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

    // Auto-format key input
    const keyInput = document.getElementById('key');
    if (keyInput) {
        keyInput.addEventListener('input', function(e) {
            // Convert to lowercase and replace spaces with underscores
            this.value = this.value.toLowerCase().replace(/\s+/g, '_').replace(/[^a-z0-9_]/g, '');
        });
    }
</script>
@endpush
@endsection
