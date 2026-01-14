@extends('layouts.main-nav')

@section('title', 'Kalender')
@section('subtitle', 'Lihat dan kelola aktivitas Anda dengan mudah menggunakan kalender interaktif kami.')

@section('content')
{{-- Data untuk JS --}}
<div id="calendarData" 
     data-events='@json($allUpcomingEvents ?? [])'
     data-activities='@json($aktivitasBulanIni ?? [])'
     data-store-route='{{ route("kalender.store") }}'
     data-csrf-token='{{ csrf_token() }}'
     style="display: none;">
</div>

{{-- CSS --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css">
<link rel="stylesheet" href="{{ asset('css/calendar.css') }}">

<div class="min-h-screen">
    <main class="flex-grow bg-white rounded-tl-3xl p-6 md:p-10">
        <div class="max-w-7xl mx-auto">
           
    <!-- Main Grid Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left Sidebar - Upcoming Events -->
        <div class="lg:col-span-4 xl:col-span-3">
            <div class="bg-slate-50 rounded-xl p-4 shadow-sm border border-slate-200">
                <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-4">Acara Mendatang</h3>
                
                <button onclick="openAddModal()" class="w-full mb-4 p-2 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition flex items-center justify-center gap-2">
                    <i data-lucide="plus" class="w-5 h-5"></i>
                    <span class="font-medium">Tambah Acara</span>
                </button>
                
                <!-- Events List -->
                <div class="space-y-2 max-h-[600px] overflow-y-auto pr-2" id="eventsContainer">
                    @php
                        $allUpcomingEvents = ($aktivitasBulanIni ?? collect())->filter(function ($event) {
                            return \Carbon\Carbon::parse($event->date)->isFuture() || \Carbon\Carbon::parse($event->date)->isToday();
                        })->sortBy('date')->values();
                        
                        $totalEvents = $allUpcomingEvents->count();
                        $currentPage = 1;
                        $perPage = 5;
                        $upcomingEvents = $allUpcomingEvents->take($perPage);
                    @endphp
                    
                    @forelse($upcomingEvents as $event)
                        <div class="p-4 rounded-xl hover:bg-slate-50 cursor-pointer border border-slate-200 bg-white" onclick="openEditModalById({{ $event->id }})">
                            <div class="flex items-start justify-between mb-2">
                                <span class="text-xs font-semibold text-slate-400 uppercase">
                                    {{ \Carbon\Carbon::parse($event->date)->format('M d, Y') }}
                                </span>
                                <span class="text-xs px-2 py-1 rounded-full {{ $event->status === 'selesai' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                                    {{ $event->status === 'selesai' ? 'Done' : 'Pending' }}
                                </span>
                            </div>
                            <h3 class="font-bold text-slate-800 text-sm mb-1">{{ $event->title }}</h3>
                            @if($event->desk)
                                <p class="text-xs text-slate-500 line-clamp-2">{{ $event->desk }}</p>
                            @endif
                            @if($event->time)
                                <p class="text-xs text-slate-400 mt-2">⏰ {{ substr($event->time, 0, 5) }}</p>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-8 text-slate-400">
                            <i data-lucide="calendar-x" class="w-12 h-12 mx-auto mb-2 opacity-50"></i>
                            <p class="text-sm">Tidak Ada Acara</p>
                        </div>
                    @endforelse
                </div>
                
                <!-- Pagination -->
                @if($totalEvents > $perPage)
                    <div class="mt-4 pt-4 border-t border-slate-200">
                        <div class="flex items-center justify-between text-sm">
                            <button id="prevBtn" onclick="changePage(-1)" class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed transition" disabled>
                                ← Sebelumnya
                            </button>
                            <span class="text-slate-500 font-medium">
                                <span id="currentPageDisplay">1</span> / <span id="totalPages">{{ ceil($totalEvents / $perPage) }}</span>
                            </span>
                            <button id="nextBtn" onclick="changePage(1)" class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed transition">
                                Selanjutnya →
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Side - Calendar -->
        <div class="lg:col-span-8 xl:col-span-9">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <!-- View Tabs -->
                <div class="flex items-center justify-between mb-4 border-b border-gray-200 pb-4">
                    <div class="flex gap-2">
                        <button onclick="calendar.changeView('timeGridDay')" class="view-btn px-4 py-2 rounded-lg text-sm font-medium transition bg-gray-100 text-gray-600 hover:bg-gray-200">Hari</button>
                        <button onclick="calendar.changeView('timeGridWeek')" class="view-btn px-4 py-2 rounded-lg text-sm font-medium transition bg-gray-100 text-gray-600 hover:bg-gray-200">Minggu</button>
                        <button onclick="calendar.changeView('dayGridMonth')" class="view-btn active px-4 py-2 rounded-lg text-sm font-medium transition bg-blue-600 text-white">Bulan</button>
                    </div>
                </div>
                
                <div id="calendar"></div>
            </div>
        </div>
    </div>
        </div>
    </main>
</div>

<!-- Modal Tambah/Edit Acara -->
<div id="eventModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4 shadow-xl">
        <div class="flex justify-between items-center mb-6">
            <h3 id="modalTitle" class="text-xl font-bold text-gray-900">Tambah Acara</h3>
            <button type="button" onclick="closeEventModal()" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        
        <form id="eventForm" method="POST" action="{{ route('kalender.store') }}">
            @csrf
            <input type="hidden" id="methodInput" name="_method" value="POST">
            <input type="hidden" id="eventId" name="event_id">
            
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i data-lucide="type" class="w-4 h-4 inline mr-1"></i>
                    Judul
                </label>
                <input type="text" name="title" id="inputTitle" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Masukkan judul acara" required>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i data-lucide="file-text" class="w-4 h-4 inline mr-1"></i>
                    Deskripsi
                </label>
                <textarea name="desk" id="inputDesk" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" rows="3" placeholder="Tambahkan deskripsi (opsional)"></textarea>
            </div>
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i data-lucide="calendar" class="w-4 h-4 inline mr-1"></i>
                        Tanggal
                    </label>
                    <input type="date" name="date" id="inputDate" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i data-lucide="clock" class="w-4 h-4 inline mr-1"></i>
                        Waktu
                    </label>
                    <input type="time" name="time" id="inputTime" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>
            
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i data-lucide="check-circle" class="w-4 h-4 inline mr-1"></i>
                    Status
                </label>
                <select name="status" id="inputStatus" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="pending">Pending</option>
                    <option value="selesai">Selesai</option>
                </select>
            </div>
            
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2.5 rounded-lg hover:bg-blue-700 font-medium transition flex items-center justify-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Simpan</span>
                </button>
                <button type="button" id="deleteBtn" onclick="deleteEvent()" class="hidden bg-red-600 text-white px-4 py-2.5 rounded-lg hover:bg-red-700 font-medium transition">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
                <button type="button" onclick="closeEventModal()" class="bg-gray-200 text-gray-700 px-4 py-2.5 rounded-lg hover:bg-gray-300 font-medium transition">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

{{-- FullCalendar JS --}}
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/locales/id.global.min.js"></script>
<script src="https://unpkg.com/lucide@latest"></script>

{{-- Custom Calendar JS --}}
<script src="{{ asset('js/calendar.js') }}"></script>

@endsection
