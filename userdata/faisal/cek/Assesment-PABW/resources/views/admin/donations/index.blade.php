@extends('admin.layouts.master')

@section('title', 'Verifikasi Donasi')

@section('content')
<div class="min-h-screen pb-20">
    <!-- HEADER & STATS -->
    <div class="mb-10">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
            <div>
                <h2 class="text-3xl font-black text-slate-900 tracking-tight flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-blue-600 flex items-center justify-center text-white shadow-lg shadow-blue-200">
                        <i class="fas fa-hand-holding-heart text-xl"></i>
                    </div>
                    Verifikasi Donasi
                </h2>
                <p class="text-slate-500 mt-2 font-medium">Manajemen transparansi dan validasi kontribusi donatur.</p>
            </div>
            
            <div class="flex items-center gap-3 no-print">
                <div class="relative group">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-600 transition-colors"></i>
                    <input type="text" id="searchInput" 
                           class="pl-12 pr-6 py-4 rounded-2xl border-none bg-white shadow-sm focus:ring-4 focus:ring-blue-500/10 w-full md:w-80 font-semibold text-sm outline-none transition-all" 
                           placeholder="Cari Order ID atau Nama...">
                </div>
            </div>
        </div>

        <!-- QUICK STATS -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl shadow-inner">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Transaksi</p>
                    <h4 class="text-2xl font-black text-slate-900">{{ number_format($transactions->count()) }}</h4>
                </div>
            </div>
            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl shadow-inner">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Butuh Verifikasi</p>
                    <h4 class="text-2xl font-black text-slate-900">{{ number_format($transactions->where('status', 'PENDING_VERIFICATION')->count()) }}</h4>
                </div>
            </div>
            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center text-2xl shadow-inner">
                    <i class="fas fa-check-double"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Donasi Berhasil</p>
                    <h4 class="text-2xl font-black text-slate-900">{{ number_format($transactions->where('status', 'VERIFIED')->count()) }}</h4>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
    <div class="mb-8 bg-green-600 text-white px-6 py-4 rounded-2xl shadow-lg shadow-green-200 flex items-center justify-between animate-bounce">
        <div class="flex items-center gap-3">
            <i class="fas fa-check-circle"></i>
            <span class="font-bold text-sm uppercase tracking-wide">{{ session('success') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" class="text-white/50 hover:text-white"><i class="fas fa-times"></i></button>
    </div>
    @endif

    <!-- MAIN TABLE CARD -->
    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-200/50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Order ID & Tanggal</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Donatur</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Kampanye / Nominal</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Metode & Status</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($transactions as $transaction)
                    <tr class="hover:bg-slate-50/80 transition-all duration-200 group">
                        <td class="px-8 py-6">
                            <div class="text-sm font-black text-slate-900 mb-1 group-hover:text-blue-600 transition-colors">#{{ $transaction->order_id }}</div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-tight">{{ $transaction->created_at->format('d M Y, H:i') }}</div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 font-bold text-xs border border-white shadow-sm">
                                    {{ substr($transaction->donor_name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-slate-900">{{ $transaction->donor_name }}</div>
                                    <div class="text-xs text-slate-400 font-medium">{{ $transaction->donor_email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="text-xs font-bold text-blue-600 mb-1">
                                {{ $transaction->campaign ? Str::limit($transaction->campaign->title, 30) : 'Donasi Umum' }}
                            </div>
                            <div class="text-lg font-black text-slate-900 tracking-tighter">
                                Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex flex-col items-center gap-2">
                                <div class="px-3 py-1 rounded-lg bg-slate-100 text-slate-600 text-[10px] font-black uppercase tracking-tighter border border-slate-200">
                                    {{ str_replace('_', ' ', $transaction->payment_method) }}
                                </div>
                                
                                @if($transaction->status === 'VERIFIED')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase bg-green-100 text-green-700 border border-green-200">
                                        <i class="fas fa-check-circle"></i> Paid
                                    </span>
                                @elseif($transaction->status === 'PENDING_VERIFICATION')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase bg-amber-100 text-amber-700 border border-amber-200 animate-pulse">
                                        <i class="fas fa-clock"></i> Waiting
                                    </span>
                                @elseif($transaction->status === 'AWAITING_TRANSFER')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase bg-blue-100 text-blue-700 border border-blue-200">
                                        <i class="fas fa-hourglass-half"></i> Pending
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase bg-red-100 text-red-700 border border-red-200">
                                        <i class="fas fa-times-circle"></i> Rejected
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex items-center justify-end gap-2">
                                <!-- Proof View -->
                                @if($transaction->proof_of_transfer_path)
                                <a href="{{ asset('storage/' . $transaction->proof_of_transfer_path) }}" target="_blank" 
                                   class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-blue-600 hover:text-white transition-all shadow-sm"
                                   title="Lihat Bukti Transfer">
                                    <i class="fas fa-image"></i>
                                </a>
                                @endif

                                <!-- Invoice -->
                                <a href="{{ route('admin.donations.invoice', $transaction->order_id) }}" 
                                   class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-blue-600 hover:text-white transition-all shadow-sm"
                                   title="Buka Invoice">
                                    <i class="fas fa-file-invoice"></i>
                                </a>

                                <!-- Verification Aksi Cepat -->
                                @if($transaction->status === 'PENDING_VERIFICATION')
                                <div class="flex gap-1">
                                    <form action="{{ route('admin.donations.updateStatus', $transaction->order_id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="VERIFIED">
                                        <button type="submit" class="w-10 h-10 rounded-xl bg-green-600 text-white flex items-center justify-center hover:bg-green-700 transition-all shadow-lg shadow-green-200"
                                                onclick="return confirm('Verifikasi pembayaran ini?')">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.donations.updateStatus', $transaction->order_id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="CANCELLED">
                                        <button type="submit" class="w-10 h-10 rounded-xl bg-red-600 text-white flex items-center justify-center hover:bg-red-700 transition-all shadow-lg shadow-red-200"
                                                onclick="return confirm('Tolak pembayaran ini?')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                </div>
                                @else
                                <div class="relative">
                                    <!-- Ikon diganti menjadi Clipboard Check untuk representasi Verifikasi -->
                                    <button onclick="toggleDropdown('{{ $transaction->id }}')" 
                                            class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center hover:bg-slate-700 transition-all shadow-lg"
                                            id="statusDropdown{{ $transaction->id }}">
                                        <i class="fas fa-clipboard-check"></i>
                                    </button>
                                    <!-- Dropdown Menu -->
                                    <div id="dropdownMenu{{ $transaction->id }}" class="hidden absolute right-0 mt-3 w-48 bg-white shadow-2xl rounded-2xl py-2 z-50 border border-slate-100 overflow-hidden">
                                        @foreach(['VERIFIED' => 'Set Paid', 'CANCELLED' => 'Set Rejected', 'PENDING_VERIFICATION' => 'Set Waiting', 'AWAITING_TRANSFER' => 'Set Pending'] as $status => $label)
                                        <form action="{{ route('admin.donations.updateStatus', $transaction->order_id) }}" method="POST">
                                            @csrf @method('PUT')
                                            <input type="hidden" name="status" value="{{ $status }}">
                                            <button type="submit" class="w-full px-5 py-3 text-left text-xs font-bold text-slate-600 hover:bg-blue-50 hover:text-blue-600 transition-colors uppercase tracking-tight">
                                                {{ $label }}
                                            </button>
                                        </form>
                                        @endforeach
                                        <div class="h-[1px] bg-slate-50 my-1"></div>
                                        <form action="{{ route('admin.donations.destroy', $transaction->order_id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="w-full px-5 py-3 text-left text-xs font-bold text-red-500 hover:bg-red-50 transition-colors uppercase tracking-tight"
                                                    onclick="return confirm('Hapus data transaksi secara permanen?')">
                                                <i class="fas fa-trash-alt mr-2"></i> Hapus Data
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 rounded-full bg-slate-50 flex items-center justify-center text-slate-200 text-4xl mb-4">
                                    <i class="fas fa-folder-open"></i>
                                </div>
                                <h5 class="text-slate-900 font-black uppercase tracking-widest">Tidak Ada Transaksi</h5>
                                <p class="text-slate-400 text-sm mt-1">Data donasi akan muncul di sini setelah ada transaksi masuk.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Search Functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const rowText = row.textContent.toLowerCase();
            if (rowText.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Dropdown Logic
    function toggleDropdown(id) {
        const menu = document.getElementById('dropdownMenu' + id);
        const allMenus = document.querySelectorAll('[id^="dropdownMenu"]');
        
        allMenus.forEach(m => {
            if(m.id !== 'dropdownMenu' + id) m.classList.add('hidden');
        });
        
        menu.classList.toggle('hidden');
    }

    // Close on click outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('[id^="statusDropdown"]') && !e.target.closest('[id^="dropdownMenu"]')) {
            document.querySelectorAll('[id^="dropdownMenu"]').forEach(m => m.classList.add('hidden'));
        }
    });
</script>
@endsection