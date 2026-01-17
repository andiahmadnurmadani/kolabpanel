<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Invoice #{{ $transaction->order_id }} - DonGiv Admin</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-print-color-adjust: exact;
        }
        @media print {
            body { background: white !important; padding: 0 !important; }
            .no-print { display: none !important; }
            .print-shadow-none { shadow: none !important; border: none !important; }
            .main-container { padding: 0 !important; max-width: 100% !important; }
        }
        .invoice-box {
            box-shadow: 0 20px 50px -12px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen pb-20">

    <!-- Header / Nav (No Print) -->
    <header class="bg-white/80 backdrop-blur-md border-b border-slate-200 py-4 sticky top-0 z-50 no-print">
        <div class="max-w-5xl mx-auto px-6 flex items-center justify-between">
            <div class="flex items-center gap-6">
                <!-- Brand Logo in Nav -->
                <div class="flex items-center gap-2">
                    <img src="{{ asset('images/dongiv-logo.png') }}" alt="DonGiv" class="h-8 w-auto">
                </div>
                
                <div class="h-6 w-[1px] bg-slate-200"></div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.donations.index') }}" class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 hover:bg-blue-600 hover:text-white transition-all">
                        <i class="fas fa-arrow-left text-xs"></i>
                    </a>
                    <span class="text-xs font-bold text-slate-900">Kembali</span>
                </div>
            </div>
            <div class="flex gap-3">
                <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm transition-all flex items-center gap-2 shadow-lg shadow-blue-200">
                    <i class="fas fa-print"></i> Cetak Invoice
                </button>
            </div>
        </div>
    </header>

    <main class="py-12 px-6 main-container">
        <div class="max-w-4xl mx-auto">
            
            <!-- THE INVOICE DOCUMENT -->
            <div class="bg-white rounded-[2.5rem] invoice-box border border-slate-100 overflow-hidden print-shadow-none">
                
                <!-- Invoice Header Section -->
                <div class="p-10 md:p-14 border-b border-slate-100 relative">
                    <!-- Status Stamp -->
                    <div class="absolute top-10 right-10 text-right">
                        <div class="inline-block px-5 py-2 rounded-2xl font-black text-xs uppercase tracking-[0.2em] mb-3
                            {{ $transaction->status === 'VERIFIED' ? 'bg-green-100 text-green-700 border border-green-200' :
                               ($transaction->status === 'CANCELLED' ? 'bg-red-100 text-red-700 border border-red-200' : 
                               'bg-amber-100 text-amber-700 border border-amber-200') }}">
                            {{ $transaction->status_label }}
                        </div>
                        <h1 class="text-4xl font-black text-slate-900 tracking-tighter uppercase">Invoice</h1>
                        <p class="text-slate-400 font-bold text-xs mt-1">NO. #{{ $transaction->order_id }}</p>
                    </div>

                    <!-- Brand Info with Actual Logo (Text removed) -->
                    <div class="mb-12">
                        <div class="flex items-center gap-4 mb-4">
                            <img src="{{ asset('images/dongiv-logo.png') }}" alt="DonGiv Logo" class="h-14 w-auto object-contain">
                        </div>
                        <p class="text-sm text-slate-500 font-medium leading-relaxed">
                            Sistem Verifikasi Kebaikan Digital<br>
                            Gedung Sinergi Lt. 5, Jakarta Selatan<br>
                            dongiv@gmail.com | +62 21 4455 6677
                        </p>
                    </div>

                    <!-- Billing Grid -->
                    <div class="grid grid-cols-2 gap-10">
                        <div>
                            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Ditujukan Untuk:</h4>
                            <div class="space-y-1">
                                <p class="text-lg font-extrabold text-slate-900">{{ $transaction->donor_name }}</p>
                                <p class="text-sm text-slate-500 font-medium">{{ $transaction->donor_email }}</p>
                                <p class="text-sm text-slate-500 font-medium">{{ $transaction->donor_phone ?? '-' }}</p>
                                <div class="mt-3 inline-flex items-center gap-2 px-3 py-1 bg-slate-100 rounded-lg text-[10px] font-bold text-slate-500">
                                    <i class="fas fa-user-secret"></i>
                                    {{ $transaction->anonymous ? 'ANONIM' : 'NAMA TERLIHAT' }}
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Detail Transaksi:</h4>
                            <div class="space-y-2">
                                <div class="flex justify-end gap-4">
                                    <span class="text-sm text-slate-400 font-medium">Tanggal Invoice</span>
                                    <span class="text-sm text-slate-900 font-bold">{{ $transaction->created_at->format('d/m/Y') }}</span>
                                </div>
                                <div class="flex justify-end gap-4">
                                    <span class="text-sm text-slate-400 font-medium">Metode Pembayaran</span>
                                    <span class="text-sm text-slate-900 font-bold uppercase">{{ str_replace('_', ' ', $transaction->payment_method) }}</span>
                                </div>
                                @if($transaction->transfer_deadline)
                                <div class="flex justify-end gap-4">
                                    <span class="text-sm text-slate-400 font-medium">Batas Waktu</span>
                                    <span class="text-sm text-red-600 font-bold">{{ $transaction->transfer_deadline->format('d/m/Y H:i') }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content Body -->
                <div class="p-10 md:p-14">
                    
                    <!-- Campaign Info Card -->
                    @if($transaction->campaign)
                    <div class="mb-10 bg-blue-50/50 rounded-3xl p-6 border border-blue-100 flex items-start gap-5">
                        <div class="w-12 h-12 rounded-2xl bg-white flex flex-shrink-0 items-center justify-center text-blue-600 shadow-sm border border-blue-100">
                            <i class="fas fa-bullhorn text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-900 mb-1 tracking-tight">{{ $transaction->campaign->title }}</h3>
                            <p class="text-xs text-slate-500 leading-relaxed italic line-clamp-2">"{{ $transaction->campaign->description }}"</p>
                        </div>
                    </div>
                    @endif

                    <!-- Items Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b-2 border-slate-100">
                                    <th class="text-left py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Deskripsi Layanan / Donasi</th>
                                    <th class="text-right py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Jumlah (IDR)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                <tr>
                                    <td class="py-8">
                                        <p class="font-bold text-slate-900">Donasi Program Kebaikan</p>
                                        <p class="text-xs text-slate-400 mt-1">Kontribusi sukarela untuk bantuan sosial dan kemanusiaan</p>
                                    </td>
                                    <td class="py-8 text-right font-black text-slate-900 text-lg">
                                        Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="bg-slate-900 text-white">
                                    <td class="p-8 rounded-l-3xl">
                                        <span class="text-xs font-bold uppercase tracking-widest opacity-60">Total Donasi Terverifikasi</span>
                                    </td>
                                    <td class="p-8 text-right rounded-r-3xl">
                                        <span class="text-3xl font-black">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Bank Details (Conditional) -->
                    @if(in_array($transaction->payment_method, ['bank_transfer', 'manual_transfer']))
                    <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-6 bg-slate-50 rounded-[2rem] p-8 border border-slate-100">
                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Bank Tujuan</p>
                            <p class="font-bold text-slate-900">{{ $transaction->bank_name }}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Nomor Rekening</p>
                            <p class="font-mono font-black text-blue-600 text-lg tracking-tight">{{ $transaction->bank_account_number }}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Atas Nama</p>
                            <p class="font-bold text-slate-900">{{ $transaction->bank_account_name }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Admin Notes Section -->
                    <div class="mt-12 grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div class="space-y-4">
                            <h5 class="text-xs font-black text-slate-900 uppercase tracking-widest border-l-4 border-blue-600 pl-3">Catatan Verifikasi</h5>
                            <ul class="space-y-2 text-[11px] text-slate-500 font-medium leading-relaxed">
                                <li class="flex items-start gap-2">
                                    <i class="fas fa-check-circle text-blue-500 mt-0.5"></i>
                                    Pastikan mutasi bank sesuai dengan nominal yang tertera di invoice.
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="fas fa-check-circle text-blue-500 mt-0.5"></i>
                                    Gunakan tombol update status jika pembayaran telah divalidasi manual.
                                </li>
                            </ul>
                        </div>
                        <div class="text-right flex flex-col items-end justify-end">
                            <div class="w-32 h-32 border border-slate-100 rounded-2xl flex items-center justify-center opacity-20 grayscale grayscale-0">
                                <!-- Placeholder for QR Code / Digital Signature -->
                                <i class="fas fa-qrcode text-6xl"></i>
                            </div>
                            <p class="text-[9px] font-black text-slate-300 uppercase tracking-[0.3em] mt-4 italic">Digitally Verified by DonGiv</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ADMIN ACTION PANEL (NO PRINT) -->
            <div class="no-print mt-10 space-y-6">
                <div class="bg-slate-900 rounded-[2.5rem] p-10 text-white shadow-2xl shadow-slate-300">
                    <div class="flex flex-col md:flex-row items-center justify-between gap-8">
                        <div>
                            <h3 class="text-xl font-black mb-2 flex items-center gap-3">
                                <i class="fas fa-user-shield text-blue-400"></i>
                                Panel Kontrol Verifikasi
                            </h3>
                            <p class="text-sm text-slate-400">Lakukan perubahan status transaksi setelah melakukan pengecekan manual.</p>
                        </div>
                        
                        <div class="flex flex-wrap gap-4 justify-center">
                            @if($transaction->status === 'PENDING_VERIFICATION')
                                <form action="{{ route('admin.donations.updateStatus', $transaction->order_id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="status" value="VERIFIED">
                                    <button type="submit" class="bg-green-600 hover:bg-green-500 text-white px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest transition-all shadow-xl shadow-green-900/20 active:scale-95 flex items-center gap-2" onclick="return confirm('Verifikasi pembayaran ini?')">
                                        <i class="fas fa-check-double"></i> Terima Donasi
                                    </button>
                                </form>

                                <form action="{{ route('admin.donations.updateStatus', $transaction->order_id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="status" value="CANCELLED">
                                    <button type="submit" class="bg-red-600 hover:bg-red-500 text-white px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest transition-all shadow-xl shadow-red-900/20 active:scale-95 flex items-center gap-2" onclick="return confirm('Tolak pembayaran ini?')">
                                        <i class="fas fa-times"></i> Tolak Donasi
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('admin.donations.updateStatus', $transaction->order_id) }}" method="POST" class="flex items-center gap-3 bg-white/5 p-3 rounded-2xl border border-white/10">
                                    @csrf @method('PUT')
                                    <select name="status" class="bg-slate-800 border-none rounded-xl text-xs font-bold py-3 pr-10 focus:ring-blue-500">
                                        <option value="AWAITING_TRANSFER" {{ $transaction->status === 'AWAITING_TRANSFER' ? 'selected' : '' }}>Pending</option>
                                        <option value="PENDING_VERIFICATION" {{ $transaction->status === 'PENDING_VERIFICATION' ? 'selected' : '' }}>Waiting</option>
                                        <option value="VERIFIED" {{ $transaction->status === 'VERIFIED' ? 'selected' : '' }}>Paid</option>
                                        <option value="CANCELLED" {{ $transaction->status === 'CANCELLED' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                    <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white px-6 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all">
                                        Update
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Guidance Footer Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 flex items-start gap-4 shadow-sm">
                        <div class="w-10 h-10 rounded-xl bg-green-50 text-green-600 flex flex-shrink-0 items-center justify-center"><i class="fas fa-search-dollar"></i></div>
                        <div>
                            <p class="text-xs font-black text-slate-900 uppercase mb-1">Cek Mutasi</p>
                            <p class="text-[10px] text-slate-400 font-medium">Bandingkan bukti unggahan dengan saldo rekening bank.</p>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 flex items-start gap-4 shadow-sm">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex flex-shrink-0 items-center justify-center"><i class="fas fa-shield-alt"></i></div>
                        <div>
                            <p class="text-xs font-black text-slate-900 uppercase mb-1">Keamanan</p>
                            <p class="text-[10px] text-slate-400 font-medium">Setiap perubahan status akan terekam dalam log sistem.</p>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 flex items-start gap-4 shadow-sm">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex flex-shrink-0 items-center justify-center"><i class="fas fa-envelope-open-text"></i></div>
                        <div>
                            <p class="text-xs font-black text-slate-900 uppercase mb-1">Notifikasi</p>
                            <p class="text-[10px] text-slate-400 font-medium">Email otomatis akan dikirim ke donatur setelah update.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- Simple Footer (No Print) -->
    <footer class="py-10 border-t border-slate-200 text-center no-print bg-white">
        <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.4em]">&copy; {{ date('Y') }} DonGiv Admin System &bull; Secure Management</p>
    </footer>

</body>
</html>