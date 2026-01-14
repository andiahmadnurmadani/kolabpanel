@extends('layouts.main-nav')
@section('title', 'Kelola Tugas')
@section('subtitle', 'Kelola dan lacak semua tugas Anda dengan mudah.')

@section('content')
<div class="min-h-screen">
    <main class="flex-grow bg-white rounded-tl-3xl p-6 md:p-10">
        <div class="max-w-7xl mx-auto">


            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
                <div class="p-5 rounded-xl bg-gradient-to-br from-blue-50 to-blue-100/50 border border-blue-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-blue-600 font-medium mb-1">Total Tugas</p>
                            <p class="text-2xl font-bold text-slate-800">{{ $tasks->count() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center">
                            <i data-lucide="clipboard-list" class="w-6 h-6 text-white"></i>
                        </div>
                    </div>
                </div>

                <div class="p-5 rounded-xl bg-white border border-slate-200 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-slate-500 font-medium mb-1">Pending</p>
                            <p class="text-2xl font-bold text-slate-800">{{ $tasks->where('status', 'pending')->count() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="clock" class="w-6 h-6 text-yellow-600"></i>
                        </div>
                    </div>
                </div>

                <div class="p-5 rounded-xl bg-white border border-slate-200 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-slate-500 font-medium mb-1">Selesai</p>
                            <p class="text-2xl font-bold text-slate-800">{{ $tasks->where('status', 'completed')->count() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="check-circle" class="w-6 h-6 text-green-600"></i>
                        </div>
                    </div>
                </div>

                <div class="p-5 rounded-xl bg-white border border-slate-200 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-slate-500 font-medium mb-1">Prioritas Tinggi</p>
                            <p class="text-2xl font-bold text-slate-800">{{ $tasks->where('priority', 'high')->count() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="alert-circle" class="w-6 h-6 text-red-600"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow cursor-pointer">
                    <div class="flex items-center justify-between mb-3">
                        <button onclick="openTaskModal()" class="px-4 py-2.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition flex items-center gap-2 font-medium">
                            <i data-lucide="plus" class="w-5 h-5"></i>
                        </button>

                    </div>
                    <p class="text-xs font-semibold text-white uppercase">Tambah Tugas</p>
                </div>
            </div>

            <!-- Task List -->
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                <div class="p-5 border-b border-slate-200">
                    <h2 class="text-lg font-bold text-slate-800">Daftar Tugas</h2>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($tasks as $task)
                    <div class="p-5 hover:bg-slate-50 transition group">
                        <div class="flex gap-4">
                            <!-- Priority Indicator -->
                            <div class="flex-shrink-0 mt-1">
                                <div class="w-1 h-16 rounded-full {{ $task->priority === 'high' ? 'bg-red-500' : ($task->priority === 'medium' ? 'bg-yellow-500' : 'bg-green-500') }}"></div>
                            </div>

                            <!-- Task Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-4 mb-2">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-bold text-slate-800 mb-1">{{ $task->title }}</h3>
                                        @if($task->description)
                                        <p class="text-sm text-slate-600 line-clamp-2">{{ $task->description }}</p>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2 flex-shrink-0">
                                        @if($task->status !== 'completed')
                                        <form action="{{ route('tugas.complete', $task->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="p-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition" title="Tandai Selesai">
                                                <i data-lucide="check" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                        @endif
                                        <button onclick="editTask('{{ $task->id }}', '{{ addslashes($task->title) }}', '{{ addslashes($task->description) }}', '{{ $task->due_date }}', '{{ $task->priority }}', '{{ $task->status }}')"
                                            class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition" title="Edit">
                                            <i data-lucide="edit" class="w-4 h-4"></i>
                                        </button>
                                        <form action="{{ route('tugas.destroy', $task->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus tugas ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition" title="Hapus">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <div class="flex items-center gap-4 flex-wrap">
                                    @if($task->due_date)
                                    <span class="text-xs text-slate-500 flex items-center gap-1">
                                        <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                                        {{ \Carbon\Carbon::parse($task->due_date)->format('d M Y') }}
                                    </span>
                                    @endif

                                    @if($task->priority)
                                    <span class="text-xs px-2.5 py-1 rounded-full font-medium {{ $task->priority === 'high' ? 'bg-red-100 text-red-700' : ($task->priority === 'medium' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700') }}">
                                        {{ ucfirst($task->priority) }} Priority
                                    </span>
                                    @endif

                                    <span class="text-xs px-2.5 py-1 rounded-full font-medium {{ $task->status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                                        {{ $task->status === 'completed' ? 'Completed' : 'In Progress' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="p-12 text-center">
                        <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="clipboard-list" class="w-8 h-8 text-slate-400"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-800 mb-2">Belum Ada Tugas</h3>
                        <p class="text-sm text-slate-500 mb-4">Mulai tambahkan tugas untuk melacak pekerjaan Anda</p>
                        <button onclick="openTaskModal()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition inline-flex items-center gap-2">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            <span>Tambah Tugas Pertama</span>
                        </button>
                    </div>
                    @endforelse
                </div>
            </div>

        </div>
    </main>
</div>

<!-- Modal Tambah/Edit Tugas -->
<div id="taskModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl p-6 max-w-md w-full shadow-2xl">
            <div class="flex items-center justify-between mb-6">
                <h3 id="modalTitle" class="text-xl font-bold text-slate-800">Tambah Tugas</h3>
                <button onclick="closeTaskModal()" class="p-1 hover:bg-slate-100 rounded-lg transition">
                    <i data-lucide="x" class="w-5 h-5 text-slate-500"></i>
                </button>
            </div>

            <form id="taskForm" method="POST" action="{{ route('tugas.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" id="methodInput" name="_method" value="POST">

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Tugas</label>
                    <input type="text" name="nama_tugas" id="inputNamaTugas"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Masukkan nama tugas" required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Detail Tugas</label>
                    <textarea name="detail_tugas" id="inputDetailTugas"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        rows="3" placeholder="Deskripsi tugas (opsional)"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Tenggat Waktu</label>
                    <input type="date" name="tenggat" id="inputTenggat"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Prioritas</label>
                    <select name="priority" id="inputPriority"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Pilih Prioritas</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Status</label>
                    <select name="status" id="inputStatus"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="pending">Pending</option>
                        <option value="completed">Selesai</option>
                    </select>
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2.5 rounded-lg hover:bg-blue-700 transition font-medium">
                        Simpan
                    </button>
                    <button type="button" onclick="closeTaskModal()" class="flex-1 bg-slate-100 text-slate-700 px-4 py-2.5 rounded-lg hover:bg-slate-200 transition font-medium">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });

    function openTaskModal() {
        document.getElementById('taskModal').classList.remove('hidden');
        document.getElementById('modalTitle').textContent = 'Tambah Tugas';
        document.getElementById('taskForm').action = '{{ route("tugas.store") }}';
        document.getElementById('methodInput').value = 'POST';
        document.getElementById('taskForm').reset();
    }

    function closeTaskModal() {
        document.getElementById('taskModal').classList.add('hidden');
    }

    function editTask(id, nama, detail, tenggat, priority, status) {
        document.getElementById('taskModal').classList.remove('hidden');
        document.getElementById('modalTitle').textContent = 'Edit Tugas';
        document.getElementById('taskForm').action = `/tugas/${id}`;
        document.getElementById('methodInput').value = 'PUT';

        document.getElementById('inputNamaTugas').value = nama;
        document.getElementById('inputDetailTugas').value = detail;
        document.getElementById('inputTenggat').value = tenggat;
        document.getElementById('inputPriority').value = priority;
        document.getElementById('inputStatus').value = status;
    }

    // Close modal on outside click
    document.getElementById('taskModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeTaskModal();
        }
    });
</script>
@endsection