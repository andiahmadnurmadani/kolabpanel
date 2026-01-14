@extends('layouts.main-nav')
@section('title','File Materi')

@section('content')
{{-- Wrapper Utama --}}
<div class="min-h-screen">

    <!-- Konten Utama -->
    <main class="flex-grow bg-white rounded-tl-3xl p-6 md:p-10">
        <div class="max-w-7xl mx-auto">
            <!-- Baris Judul dan Aksi -->
            <div class="flex flex-wrap justify-between items-center gap-4 mb-8">
                <div class="flex items-center gap-3">
                    <h2 class="text-2xl md:text-3xl font-bold text-slate-800">Materi Saya</h2>
                    <div class="p-2 bg-slate-100 rounded-lg text-slate-600">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                        </svg>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="openUploadModal()" class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white transition flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="17 8 12 3 7 8"></polyline>
                            <line x1="12" y1="3" x2="12" y2="15"></line>
                        </svg>
                        <span>Upload File</span>
                    </button>
                    <button onclick="openFolderModal()" class="px-4 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white transition flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                        </svg>
                        <span>Folder Baru</span>
                    </button>
                </div>
            </div>

            <!-- Breadcrumb Navigation -->
            @if(count($breadcrumb) > 0)
            <div class="mb-6">
                <nav class="flex items-center gap-2 text-sm">
                    <a href="{{ route('materi.index') }}" class="text-blue-600 hover:text-blue-800">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline">
                            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                    </a>
                    @foreach($breadcrumb as $crumb)
                        <span class="text-slate-400">/</span>
                        @if($loop->last)
                            <span class="text-slate-700 font-medium">{{ $crumb['name'] }}</span>
                        @else
                            <a href="{{ route('materi.index', ['folder' => $crumb['id']]) }}" class="text-blue-600 hover:text-blue-800">
                                {{ $crumb['name'] }}
                            </a>
                        @endif
                    @endforeach
                </nav>
            </div>
            @endif

            <!-- Folders & Files List -->
            <div>
                <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-4">
                    @if($currentFolder)
                        Isi Folder: {{ $currentFolder->name }}
                    @else
                        Semua Materi
                    @endif
                </h3>
                
                <div class="space-y-2">
                    <!-- Header Tabel -->
                    <div class="grid grid-cols-12 gap-4 px-4 text-xs font-semibold text-slate-400 uppercase">
                        <div class="col-span-12 md:col-span-5">Nama</div>
                        <div class="hidden md:block md:col-span-2">Pemilik</div>
                        <div class="hidden md:block md:col-span-3">Terakhir Diubah</div>
                        <div class="hidden md:block md:col-span-1">Ukuran</div>
                        <div class="hidden md:block md:col-span-1"></div>
                    </div>

                    <!-- Daftar Folders -->
                    @foreach($folders as $folder)
                    <div class="grid grid-cols-12 gap-4 items-center p-4 rounded-xl hover:bg-slate-50 transition">
                        <a href="{{ route('materi.index', ['folder' => $folder->id]) }}" class="col-span-12 md:col-span-5 flex items-center gap-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6 text-{{ $folder->color }}-500 flex-shrink-0">
                                <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                            </svg>
                            <span class="font-medium text-slate-700 truncate">{{ $folder->name }}</span>
                        </a>
                        <div class="hidden md:block md:col-span-2">
                            <span class="text-sm text-slate-500">{{ $folder->user->name }}</span>
                        </div>
                        <div class="hidden md:block md:col-span-3 text-sm text-slate-500">
                            {{ $folder->updated_at->format('M d, Y - h:i A') }}
                        </div>
                        <div class="hidden md:block md:col-span-1 text-sm text-slate-500">
                            {{ $folder->formatted_size }}
                        </div>
                        <div class="col-span-12 md:col-span-1 text-right">
                            <div class="relative inline-block">
                                <button onclick="toggleMenu(event, 'folder-{{ $folder->id }}')" class="p-2 rounded-full hover:bg-slate-200 text-slate-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="1"></circle>
                                        <circle cx="12" cy="5" r="1"></circle>
                                        <circle cx="12" cy="19" r="1"></circle>
                                    </svg>
                                </button>
                                <div id="folder-{{ $folder->id }}" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-slate-200 z-10">
                                    <button onclick="openEditFolderModal('{{ $folder->id }}', '{{ $folder->name }}', '{{ $folder->color }}')" class="w-full text-left px-4 py-2 hover:bg-slate-50 flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"></path>
                                        </svg>
                                        <span>Rename</span>
                                    </button>
                                    <button onclick="deleteFolder('{{ $folder->id }}')" class="w-full text-left px-4 py-2 hover:bg-red-50 text-red-600 flex items-center gap-2 rounded-b-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 6h18"></path>
                                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                                        </svg>
                                        <span>Delete</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach

                    <!-- Daftar Files -->
                    @foreach($files as $file)
                    <div class="grid grid-cols-12 gap-4 items-center p-4 rounded-xl hover:bg-slate-50">
                        <div class="col-span-12 md:col-span-5 flex items-center gap-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6 {{ $file->getIconClass() }} flex-shrink-0">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                            </svg>
                            <span class="font-medium text-slate-700 truncate">{{ $file->original_name }}</span>
                        </div>
                        <div class="hidden md:block md:col-span-2">
                            <span class="text-sm text-slate-500">{{ $file->user->name }}</span>
                        </div>
                        <div class="hidden md:block md:col-span-3 text-sm text-slate-500">
                            {{ $file->updated_at->format('M d, Y - h:i A') }}
                        </div>
                        <div class="hidden md:block md:col-span-1 text-sm text-slate-500">
                            {{ $file->formatted_size }}
                        </div>
                        <div class="col-span-12 md:col-span-1 text-right">
                            <div class="relative inline-block">
                                <button onclick="toggleMenu(event, 'file-{{ $file->id }}')" class="p-2 rounded-full hover:bg-slate-200 text-slate-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="1"></circle>
                                        <circle cx="12" cy="5" r="1"></circle>
                                        <circle cx="12" cy="19" r="1"></circle>
                                    </svg>
                                </button>
                                <div id="file-{{ $file->id }}" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-slate-200 z-10">
                                    <a href="{{ route('materi.file.preview', $file->id) }}" target="_blank" class="px-4 py-2 hover:bg-slate-50 flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                        <span>Preview</span>
                                    </a>
                                    <a href="{{ route('materi.file.download', $file->id) }}" class="px-4 py-2 hover:bg-slate-50 flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                            <polyline points="7 10 12 15 17 10"></polyline>
                                            <line x1="12" y1="15" x2="12" y2="3"></line>
                                        </svg>
                                        <span>Download</span>
                                    </a>
                                    <button onclick="openRenameFileModal('{{ $file->id }}', '{{ $file->original_name }}')" class="w-full text-left px-4 py-2 hover:bg-slate-50 flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"></path>
                                        </svg>
                                        <span>Rename</span>
                                    </button>
                                    <button onclick="deleteFile('{{ $file->id }}')" class="w-full text-left px-4 py-2 hover:bg-red-50 text-red-600 flex items-center gap-2 rounded-b-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 6h18"></path>
                                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                                        </svg>
                                        <span>Delete</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach

                    @if($folders->isEmpty() && $files->isEmpty())
                    <div class="text-center py-12 text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-auto mb-4 opacity-50">
                            <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                        </svg>
                        <p class="text-lg font-medium">Folder ini masih kosong</p>
                        <p class="text-sm mt-2">Buat folder atau upload file untuk memulai</p>
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </main>

</div>

<!-- Modal Create/Edit Folder -->
<div id="folderModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl p-6 w-full max-w-md mx-4">
        <h3 id="folderModalTitle" class="text-xl font-bold text-slate-800 mb-4">Folder Baru</h3>
        <form id="folderForm" onsubmit="submitFolder(event)">
            <input type="hidden" id="folderId" value="">
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Nama Folder</label>
                <input type="text" id="folderName" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Warna</label>
                <div class="grid grid-cols-5 gap-2">
                    <button type="button" onclick="selectColor('blue')" data-color="blue" class="color-btn h-10 rounded-lg bg-blue-500 hover:ring-2 ring-blue-600"></button>
                    <button type="button" onclick="selectColor('green')" data-color="green" class="color-btn h-10 rounded-lg bg-green-500 hover:ring-2 ring-green-600"></button>
                    <button type="button" onclick="selectColor('purple')" data-color="purple" class="color-btn h-10 rounded-lg bg-purple-500 hover:ring-2 ring-purple-600"></button>
                    <button type="button" onclick="selectColor('yellow')" data-color="yellow" class="color-btn h-10 rounded-lg bg-yellow-500 hover:ring-2 ring-yellow-600"></button>
                    <button type="button" onclick="selectColor('red')" data-color="red" class="color-btn h-10 rounded-lg bg-red-500 hover:ring-2 ring-red-600"></button>
                    <button type="button" onclick="selectColor('pink')" data-color="pink" class="color-btn h-10 rounded-lg bg-pink-500 hover:ring-2 ring-pink-600"></button>
                    <button type="button" onclick="selectColor('indigo')" data-color="indigo" class="color-btn h-10 rounded-lg bg-indigo-500 hover:ring-2 ring-indigo-600"></button>
                    <button type="button" onclick="selectColor('orange')" data-color="orange" class="color-btn h-10 rounded-lg bg-orange-500 hover:ring-2 ring-orange-600"></button>
                    <button type="button" onclick="selectColor('teal')" data-color="teal" class="color-btn h-10 rounded-lg bg-teal-500 hover:ring-2 ring-teal-600"></button>
                    <button type="button" onclick="selectColor('cyan')" data-color="cyan" class="color-btn h-10 rounded-lg bg-cyan-500 hover:ring-2 ring-cyan-600"></button>
                </div>
                <input type="hidden" id="folderColor" value="blue">
            </div>
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="closeFolderModal()" class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded-lg">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">Simpan</button>
            </div>
        </form>
        </div>
    </div>
</div>

<!-- Modal Upload File -->
<div id="uploadModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl p-6 w-full max-w-md mx-4">
        <h3 class="text-xl font-bold text-slate-800 mb-4">Upload File</h3>
        <form id="uploadForm" onsubmit="submitUpload(event)">
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Pilih File</label>
                <input type="file" id="fileInput" required accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <p class="text-xs text-slate-500 mt-1">Max 10MB - Format: PDF, DOCX, XLSX, Images</p>
            </div>
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="closeUploadModal()" class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded-lg">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">Upload</button>
            </div>
        </form>
        </div>
    </div>
</div>

<!-- Modal Rename File -->
<div id="renameModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl p-6 w-full max-w-md mx-4">
        <h3 class="text-xl font-bold text-slate-800 mb-4">Rename File</h3>
        <form id="renameForm" onsubmit="submitRename(event)">
            <input type="hidden" id="renameFileId" value="">
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Nama File</label>
                <input type="text" id="renameFileName" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="closeRenameModal()" class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded-lg">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">Simpan</button>
            </div>
        </form>
        </div>
    </div>
</div>

<script>
    // Get current folder from URL
    const urlParams = new URLSearchParams(window.location.search);
    const currentFolderId = urlParams.get('folder');

    // Toggle dropdown menu
    function toggleMenu(event, menuId) {
        event.stopPropagation();
        const menu = document.getElementById(menuId);
        const allMenus = document.querySelectorAll('[id^="folder-"], [id^="file-"]');
        allMenus.forEach(m => {
            if (m.id !== menuId) m.classList.add('hidden');
        });
        menu.classList.toggle('hidden');
    }

    // Close menus when clicking outside
    document.addEventListener('click', function() {
        const allMenus = document.querySelectorAll('[id^="folder-"], [id^="file-"]');
        allMenus.forEach(m => m.classList.add('hidden'));
    });

    // Folder Modal Functions
    function openFolderModal() {
        document.getElementById('folderModalTitle').textContent = 'Folder Baru';
        document.getElementById('folderId').value = '';
        document.getElementById('folderName').value = '';
        document.getElementById('folderColor').value = 'blue';
        selectColor('blue');
        document.getElementById('folderModal').classList.remove('hidden');
    }

    function openEditFolderModal(id, name, color) {
        document.getElementById('folderModalTitle').textContent = 'Edit Folder';
        document.getElementById('folderId').value = id;
        document.getElementById('folderName').value = name;
        document.getElementById('folderColor').value = color;
        selectColor(color);
        document.getElementById('folderModal').classList.remove('hidden');
    }

    function closeFolderModal() {
        document.getElementById('folderModal').classList.add('hidden');
    }

    function selectColor(color) {
        document.querySelectorAll('.color-btn').forEach(btn => btn.classList.remove('ring-2'));
        document.querySelector(`[data-color="${color}"]`).classList.add('ring-2');
        document.getElementById('folderColor').value = color;
    }

    async function submitFolder(event) {
        event.preventDefault();
        const folderId = document.getElementById('folderId').value;
        const name = document.getElementById('folderName').value;
        const color = document.getElementById('folderColor').value;

        const url = folderId 
            ? `{{ url('/materi/folder') }}/${folderId}`
            : '{{ route("materi.folder.store") }}';
        
        const method = folderId ? 'PUT' : 'POST';

        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    name: name,
                    color: color,
                    parent_id: currentFolderId
                })
            });

            const data = await response.json();
            
            if (data.success) {
                closeFolderModal();
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Terjadi kesalahan'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menyimpan folder');
        }
    }

    async function deleteFolder(id) {
        if (!confirm('Hapus folder ini? Folder harus kosong untuk bisa dihapus.')) return;

        try {
            const response = await fetch(`{{ url('/materi/folder') }}/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const data = await response.json();
            
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Gagal menghapus folder');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghapus folder');
        }
    }

    // Upload Modal Functions
    function openUploadModal() {
        document.getElementById('uploadModal').classList.remove('hidden');
    }

    function closeUploadModal() {
        document.getElementById('uploadModal').classList.add('hidden');
        document.getElementById('uploadForm').reset();
    }

    async function submitUpload(event) {
        event.preventDefault();
        const fileInput = document.getElementById('fileInput');
        const file = fileInput.files[0];

        if (!file) {
            alert('Pilih file terlebih dahulu');
            return;
        }

        const formData = new FormData();
        formData.append('file', file);
        if (currentFolderId) {
            formData.append('folder_id', currentFolderId);
        }

        try {
            const response = await fetch('{{ route("materi.file.upload") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            });

            const data = await response.json();
            
            if (data.success) {
                closeUploadModal();
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Terjadi kesalahan'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat upload file');
        }
    }

    // Rename File Modal Functions
    function openRenameFileModal(id, name) {
        document.getElementById('renameFileId').value = id;
        document.getElementById('renameFileName').value = name;
        document.getElementById('renameModal').classList.remove('hidden');
    }

    function closeRenameModal() {
        document.getElementById('renameModal').classList.add('hidden');
    }

    async function submitRename(event) {
        event.preventDefault();
        const fileId = document.getElementById('renameFileId').value;
        const name = document.getElementById('renameFileName').value;

        try {
            const response = await fetch(`{{ url('/materi/file') }}/${fileId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ name: name })
            });

            const data = await response.json();
            
            if (data.success) {
                closeRenameModal();
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Terjadi kesalahan'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat rename file');
        }
    }

    async function deleteFile(id) {
        if (!confirm('Hapus file ini?')) return;

        try {
            const response = await fetch(`{{ url('/materi/file') }}/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const data = await response.json();
            
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Gagal menghapus file');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghapus file');
        }
    }
</script>

@endsection