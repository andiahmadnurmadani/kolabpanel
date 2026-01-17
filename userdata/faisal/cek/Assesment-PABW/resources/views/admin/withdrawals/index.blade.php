@extends('admin.layouts.master')

@section('content')
<div class="min-h-screen bg-gray-50/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        {{-- SECTION 1: HEADER --}}
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Pusat Kontrol Keuangan</h1>
            <p class="mt-1 text-sm text-gray-500">Ringkasan aset donasi dan manajemen pengeluaran operasional.</p>
        </div>

        {{-- SECTION 2: STATISTIK --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            {{-- Card 1 --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Total Donasi Masuk</p>
                    <p class="text-2xl font-black text-gray-800">
                        Rp {{ number_format($totalDonasiMasuk, 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>

            {{-- Card 2 --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Total Disalurkan</p>
                    <p class="text-2xl font-black text-gray-800">
                        Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center text-red-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </div>
            </div>

            {{-- Card 3 --}}
            <div class="bg-emerald-600 rounded-2xl shadow-lg shadow-emerald-200 p-6 flex items-center justify-between text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white opacity-10 rounded-full blur-2xl"></div>
                <div class="relative z-10">
                    <p class="text-xs font-bold text-emerald-100 uppercase tracking-wider mb-1">Sisa Aset (Saldo)</p>
                    <p class="text-3xl font-black">
                        Rp {{ number_format($totalSaldoTersedia, 0, ',', '.') }}
                    </p>
                </div>
                <div class="relative z-10 w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>

        {{-- SECTION 3: TABEL NERACA --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <h2 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Neraca Per Kampanye
                </h2>
            </div>

            {{-- Alerts --}}
            <div class="px-6 mt-4">
                @if(session('success'))
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm" role="alert">
                        <div class="w-6 h-6 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0"><i class="fas fa-check text-xs"></i></div>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                @endif
                @if(session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm" role="alert">
                        <div class="w-6 h-6 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0"><i class="fas fa-exclamation text-xs"></i></div>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                @endif
            </div>

            <div class="overflow-x-auto p-4">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider rounded-l-xl">Kampanye</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">Masuk (In)</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">Keluar (Out)</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-800 uppercase tracking-wider bg-blue-50/30">Sisa Saldo</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-400 uppercase tracking-wider rounded-r-xl">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($campaigns as $campaign)
                            @php
                                $totalIn = $campaign->current_amount;
                                $totalOut = $campaign->withdrawals_sum_amount ?? 0;
                                $balance = $totalIn - $totalOut;
                            @endphp
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0 rounded-lg bg-gray-100 overflow-hidden border border-gray-200">
                                            @if($campaign->image) <img class="h-10 w-10 object-cover" src="{{ $campaign->image }}"> @else <div class="h-full w-full flex items-center justify-center text-gray-400"><i class="fas fa-image"></i></div> @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-bold text-gray-900 line-clamp-1 max-w-xs">{{ $campaign->title }}</div>
                                            <div class="text-xs text-gray-500">Target: Rp {{ number_format($campaign->target_amount, 0, ',', '.') }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700">+ Rp {{ number_format($totalIn, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-700">- Rp {{ number_format($totalOut, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right bg-blue-50/30">
                                    <span class="text-sm font-bold {{ $balance > 0 ? 'text-gray-900' : 'text-gray-400' }}">Rp {{ number_format($balance, 0, ',', '.') }}</span>
                                </td>
                                
                                {{-- REVISI 1: Tombol Selalu Tampil (Hapus Opacity Group Hover) --}}
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex justify-center items-center gap-2">
                                        
                                        {{-- Tombol Lihat Riwayat --}}
                                        <a href="{{ route('admin.withdrawals.history', $campaign->id) }}" 
                                           class="inline-flex items-center px-3 py-1.5 border border-gray-200 text-xs font-bold rounded-lg text-gray-600 bg-white hover:bg-gray-50 hover:text-blue-600 hover:border-blue-200 shadow-sm transition-all" 
                                           title="Lihat Buku Kas">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            Riwayat
                                        </a>

                                        {{-- Tombol Pakai Dana --}}
                                        @if($balance > 0)
                                            <button type="button" 
                                                onclick="openExpenseModal({{ $campaign->id }}, '{{ addslashes($campaign->title) }}', {{ $balance }})"
                                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-bold rounded-lg text-white bg-red-600 hover:bg-red-700 shadow-md shadow-red-200 transition-all transform hover:-translate-y-0.5 active:scale-95">
                                                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                                Pakai Dana
                                            </button>
                                        @else
                                            <button disabled class="inline-flex items-center px-3 py-1.5 border border-gray-100 text-xs font-medium rounded-lg text-gray-300 bg-gray-50 cursor-not-allowed">
                                                <i class="fas fa-lock mr-1.5 text-[10px]"></i> Kosong
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4 text-gray-300">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                        </div>
                                        <h3 class="text-sm font-bold text-gray-900">Belum Ada Data</h3>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                {{ $campaigns->links() }}
            </div>
        </div>
    </div>
</div>

{{-- MODAL FORMULIR PENGELUARAN --}}
<div id="expenseModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-40 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="closeExpenseModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-100">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-red-600"><i class="fas fa-minus text-xs"></i></div>
                    Catat Pengeluaran
                </h3>
                <button type="button" onclick="closeExpenseModal()" class="text-gray-400 hover:text-gray-600 transition-colors"><i class="fas fa-times text-lg"></i></button>
            </div>

            <form action="{{ route('admin.withdrawals.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="px-6 py-6">
                    <input type="hidden" name="campaign_id" id="modal_campaign_id">
                    
                    <div class="bg-blue-50/50 border border-blue-100 rounded-xl p-4 mb-6 flex items-start gap-3">
                        <i class="fas fa-info-circle text-blue-500 mt-1"></i>
                        <div>
                            <p class="text-xs font-bold text-blue-500 uppercase tracking-wider mb-0.5">Sumber Dana</p>
                            <p class="text-sm font-bold text-gray-800 line-clamp-1" id="modal_campaign_title">-</p>
                            <p class="text-xs text-gray-500 mt-1">Saldo Tersedia: <span class="font-bold text-gray-800" id="modal_max_balance">Rp 0</span></p>
                        </div>
                    </div>

                    <div class="space-y-5">
                        <div class="relative">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1">Nominal (Rp)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><span class="text-gray-400 font-bold text-sm">Rp</span></div>
                                <input type="number" name="amount" id="modal_amount" min="1000" required class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-xl leading-5 bg-white font-bold text-gray-800 focus:outline-none focus:ring-2 focus:ring-red-500 transition-shadow" placeholder="0">
                            </div>
                        </div>

                        {{-- REVISI 2: Kategori dengan Logic Lainnya --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1">Kategori Pengeluaran</label>
                            <select name="expense_category" id="expense_category" required onchange="toggleOtherInput()"
                                class="block w-full pl-3 pr-10 py-2.5 text-base border border-gray-300 focus:outline-none focus:ring-red-500 sm:text-sm rounded-xl transition-shadow bg-white">
                                <option value="Logistik Bantuan">📦 Logistik Bantuan (Sembako, Pakaian)</option>
                                <option value="Operasional Lapangan">⛽ Operasional Lapangan (Bensin, Makan)</option>
                                <option value="Obat & Medis">💊 Kesehatan & Obat-obatan</option>
                                <option value="Pembangunan">🏗️ Material Pembangunan</option>
                                <option value="Santunan Tunai">💵 Santunan Tunai Langsung</option>
                                <option value="Lainnya">🔹 Lainnya (Tulis Sendiri)</option>
                            </select>
                        </div>

                        {{-- Input Khusus Lainnya (Hidden by Default) --}}
                        <div id="other_category_container" class="hidden">
                            <label class="block text-xs font-bold text-blue-600 uppercase mb-1 ml-1">Sebutkan Kategori Lainnya</label>
                            <input type="text" name="custom_category" id="custom_category" 
                                class="block w-full border border-blue-300 bg-blue-50 rounded-xl py-2 px-3 focus:outline-none focus:ring-blue-500 sm:text-sm transition-shadow font-medium text-blue-800"
                                placeholder="Misal: Sewa Tenda, Jasa Tukang, dll">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1">Detail Keterangan</label>
                            <textarea name="description" id="description" rows="3" required class="block w-full border border-gray-300 rounded-xl py-2 px-3 focus:outline-none focus:ring-red-500 sm:text-sm transition-shadow" placeholder="Contoh: Pembelian 50 sak semen di Toko Bangunan Jaya..."></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1">Bukti Nota/Struk</label>
                            <input type="file" name="proof_file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 transition-colors"/>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 rounded-b-2xl">
                    <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center rounded-xl border border-transparent shadow-lg shadow-red-200 px-5 py-2.5 bg-red-600 text-sm font-bold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all transform hover:-translate-y-0.5">
                        <i class="fas fa-save mr-2"></i> Simpan
                    </button>
                    <button type="button" onclick="closeExpenseModal()" class="w-full sm:w-auto inline-flex justify-center items-center rounded-xl border border-gray-300 shadow-sm px-5 py-2.5 bg-white text-sm font-bold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 transition-all">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openExpenseModal(id, title, balance) {
        document.getElementById('modal_campaign_id').value = id;
        document.getElementById('modal_campaign_title').innerText = title;
        document.getElementById('modal_max_balance').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(balance);
        document.getElementById('modal_amount').max = balance;
        document.getElementById('expenseModal').classList.remove('hidden');
    }

    function closeExpenseModal() {
        document.getElementById('expenseModal').classList.add('hidden');
    }

    // Logic untuk menampilkan Input Lainnya
    function toggleOtherInput() {
        const select = document.getElementById('expense_category');
        const otherContainer = document.getElementById('other_category_container');
        const customInput = document.getElementById('custom_category');

        if (select.value === 'Lainnya') {
            otherContainer.classList.remove('hidden'); // Munculkan input
            customInput.required = true; // Wajib diisi
        } else {
            otherContainer.classList.add('hidden'); // Sembunyikan input
            customInput.required = false; // Tidak wajib
            customInput.value = ''; // Reset value
        }
    }
</script>
@endsection