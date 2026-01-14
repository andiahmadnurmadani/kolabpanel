@extends('layouts.main-nav')

@section('title', 'Catatan')
@section('subtitle', 'Kelola dan atur catatan Anda dengan mudah')

@section('content')
<div class="min-h-screen">
    <main class="flex-grow bg-white rounded-tl-3xl p-6 md:p-10">
        <div class="max-w-7xl mx-auto">
            
            <!-- Page Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <h1 class="text-3xl font-bold text-slate-800">Catatan Saya</h1>
                            <div class="p-2 bg-purple-50 rounded-lg text-purple-600">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                </svg>
                            </div>
                        </div>
                        <p class="text-slate-500">Kelola dan atur semua catatan Anda</p>
                    </div>
                    <button onclick="openModal()" class="px-4 py-2.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition flex items-center gap-2 font-medium">
                        <i data-lucide="plus" class="w-5 h-5"></i>
                        <span>Tambah Catatan</span>
                    </button>
                </div>
            </div>

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="p-5 rounded-xl bg-gradient-to-br from-purple-50 to-purple-100/50 border border-purple-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-purple-600 font-medium mb-1">Total Catatan</p>
                            <p class="text-2xl font-bold text-slate-800">{{ $notes->count() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center">
                            <i data-lucide="file-text" class="w-6 h-6 text-white"></i>
                        </div>
                    </div>
                </div>
                
                <div class="p-5 rounded-xl bg-white border border-slate-200 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-slate-500 font-medium mb-1">Minggu Ini</p>
                            <p class="text-2xl font-bold text-slate-800">{{ $notes->where('created_at', '>=', now()->startOfWeek())->count() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="calendar" class="w-6 h-6 text-blue-600"></i>
                        </div>
                    </div>
                </div>
                
                <div class="p-5 rounded-xl bg-white border border-slate-200 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-slate-500 font-medium mb-1">Terbaru</p>
                            <p class="text-lg font-bold text-slate-800">{{ $notes->first() ? $notes->first()->created_at->diffForHumans() : '-' }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="clock" class="w-6 h-6 text-green-600"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Alert --}}
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @elseif(session('info'))
                <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
                    <i data-lucide="info" class="w-5 h-5"></i>
                    <span>{{ session('info') }}</span>
                </div>
            @elseif(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-5 h-5"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Notes Grid -->
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                <div class="p-5 border-b border-slate-200">
                    <h2 class="text-lg font-bold text-slate-800">Semua Catatan</h2>
                </div>
                
                <div id="catatanContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-5">
                    @forelse ($notes as $note)
                    <div class="p-5 rounded-xl border border-slate-200 bg-white hover:shadow-lg hover:border-purple-300 transition group">
                        <div class="flex items-start justify-between mb-3">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center group-hover:bg-purple-200 transition">
                                <i data-lucide="sticky-note" class="w-5 h-5 text-purple-600"></i>
                            </div>
                            <div class="flex gap-1">
                                <button 
                                    onclick="editNote(this)"
                                    data-id="{{ $note->id }}"
                                    data-title="{{ htmlspecialchars($note->title, ENT_QUOTES) }}"
                                    data-content="{{ htmlspecialchars($note->content, ENT_QUOTES) }}"
                                    class="p-1.5 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition" title="Edit">
                                    <i data-lucide="edit-2" class="w-4 h-4"></i>
                                </button>
                                <a href="{{ route('catatan.delete', $note->id) }}"
                                    class="p-1.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition" title="Hapus"
                                    onclick="return confirm('Yakin ingin menghapus catatan ini?')">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </a>
                            </div>
                        </div>
                        <h3 class="font-bold text-slate-800 mb-2 line-clamp-2">{{ $note->title }}</h3>
                        <p class="text-slate-600 text-sm line-clamp-3 mb-3">{{ $note->content }}</p>
                        <div class="flex items-center gap-2 text-xs text-slate-500">
                            <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                            <span>{{ $note->created_at->format('d M Y') }}</span>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full text-center py-16">
                        <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="file-text" class="w-10 h-10 text-slate-400"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-800 mb-2">Belum Ada Catatan</h3>
                        <p class="text-sm text-slate-500 mb-4">Mulai buat catatan pertama Anda</p>
                        <button onclick="openModal()" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition inline-flex items-center gap-2">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            <span>Buat Catatan</span>
                        </button>
                    </div>
                    @endforelse
                </div>
            </div>

    {{-- Modal Tambah/Edit --}}
    <div id="modalCatatan" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl">
                <div class="flex items-center justify-between mb-6">
                    <h2 id="modalTitle" class="text-xl font-bold text-slate-800">Tambah Catatan</h2>
                    <button onclick="closeModal()" class="p-1 hover:bg-slate-100 rounded-lg transition">
                        <i data-lucide="x" class="w-5 h-5 text-slate-500"></i>
                    </button>
                </div>
                
                <form id="catatanForm" method="POST" action="{{ route('catatan.store') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="edit_id" id="edit_id">
                    
                    <div>
                        <label for="judul" class="block text-sm font-semibold text-slate-700 mb-2">Judul Catatan</label>
                        <input id="judul" name="judul" type="text" required
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" 
                            placeholder="Masukkan judul catatan">
                    </div>
                    
                    <div>
                        <label for="isi" class="block text-sm font-semibold text-slate-700 mb-2">Isi Catatan</label>
                        <textarea id="isi" name="isi" rows="5"
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" 
                            placeholder="Tulis catatan Anda di sini..."></textarea>
                    </div>
                    
                    <div class="flex gap-3 pt-4">
                        <button type="submit" class="flex-1 bg-purple-600 text-white px-4 py-2.5 rounded-lg hover:bg-purple-700 transition font-medium">
                            Simpan
                        </button>
                        <button type="button" onclick="closeModal()" class="flex-1 bg-slate-100 text-slate-700 px-4 py-2.5 rounded-lg hover:bg-slate-200 transition font-medium">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
        </div>
    </main>
</div>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
document.addEventListener("DOMContentLoaded", () => lucide.createIcons());

function openModal() {
    document.getElementById("modalTitle").innerText = "Tambah Catatan";
    document.getElementById("catatanForm").action = "{{ route('catatan.store') }}";
    document.getElementById("edit_id").value = "";
    document.getElementById("judul").value = "";
    document.getElementById("isi").value = "";
    document.getElementById("modalCatatan").classList.remove("hidden");
}

function closeModal() {
    document.getElementById("modalCatatan").classList.add("hidden");
}

function editNote(button) {
    const id = button.getAttribute('data-id');
    const title = button.getAttribute('data-title');
    const content = button.getAttribute('data-content');
    
    document.getElementById("modalTitle").innerText = "Edit Catatan";
    document.getElementById("catatanForm").action = "{{ route('catatan.update', ':id') }}".replace(':id', id);
    document.getElementById("judul").value = title;
    document.getElementById("isi").value = content;
    document.getElementById("modalCatatan").classList.remove("hidden");
}
</script>
@endsection
