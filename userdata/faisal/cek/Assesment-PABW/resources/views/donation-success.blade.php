<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Donasi Berhasil | Terima Kasih atas Kebaikan Anda</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: radial-gradient(circle at top right, #eff6ff, #f8fafc);
        }
        .success-checkmark {
            width: 80px;
            height: 80px;
            margin: 0 auto;
            background: #dcfce7;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: scaleIn 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes scaleIn {
            from { transform: scale(0); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        .receipt-dashed {
            background-image: linear-gradient(to right, #e2e8f0 50%, rgba(255,255,255,0) 0%);
            background-position: bottom;
            background-size: 15px 1px;
            background-repeat: repeat-x;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    <div class="max-w-xl w-full">
        <!-- MAIN SUCCESS CARD -->
        <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-blue-100/50 border border-slate-100 overflow-hidden relative">
            
            <!-- Top Banner Accent -->
            <div class="h-2 bg-gradient-to-r from-blue-600 to-cyan-400"></div>

            <div class="p-8 md:p-12">
                <!-- Icon & Greeting -->
                <div class="text-center mb-10">
                    <div class="success-checkmark mb-6">
                        <i class="fas fa-check text-green-600 text-3xl"></i>
                    </div>
                    <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-3">Transaksi Berhasil!</h1>
                    <p class="text-slate-500 text-sm leading-relaxed max-w-xs mx-auto">
                        Terima kasih atas kontribusi Anda. Kebaikan ini akan sangat berarti bagi mereka yang membutuhkan.
                    </p>
                </div>

                <!-- TRANSACTION DETAILS (RECEIPT STYLE) -->
                <div class="bg-slate-50 rounded-3xl p-6 mb-8 border border-slate-100">
                    <div class="flex justify-between items-center mb-4 pb-4 border-b border-slate-200 border-dashed">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Status Pembayaran</span>
                        <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-[10px] font-bold uppercase">Paid / Success</span>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-400 font-medium">Order ID</span>
                            <span class="text-slate-900 font-bold uppercase tracking-tight">#{{ request()->query('order_id') ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-400 font-medium">Metode</span>
                            <span class="text-slate-900 font-bold">Secure Payment Gateway</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-400 font-medium">Waktu</span>
                            <span class="text-slate-900 font-bold">{{ now()->format('d M Y, H:i') }} WIB</span>
                        </div>
                    </div>
                </div>

                <!-- ACTIONS -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <a href="{{ route('home') }}" class="flex items-center justify-center gap-3 bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-2xl transition-all shadow-lg shadow-blue-200 active:scale-95">
                        <i class="fas fa-home text-sm"></i>
                        <span class="text-sm">Kembali ke Beranda</span>
                    </a>
                    @auth
                    <a href="{{ route('profiles.index') }}" class="flex items-center justify-center gap-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-4 rounded-2xl transition-all active:scale-95">
                        <i class="fas fa-history text-sm"></i>
                        <span class="text-sm">Riwayat Donasi</span>
                    </a>
                    @endauth
                </div>

                <!-- MANUAL TRANSFER / UPLOAD PROOF (IF NEEDED) -->
                @auth
                    @php
                        $orderId = request()->route('order_id') ?? request()->query('order_id');
                        $transaction = $orderId ? \App\Models\DonationTransaction::where('order_id', $orderId)->where('user_id', auth()->id())->first() : null;
                    @endphp

                    @if($transaction)
                        @if($transaction->status === 'AWAITING_TRANSFER')
                        <div class="mt-8 pt-8 border-t border-slate-100">
                            <div class="bg-amber-50 border border-amber-100 rounded-3xl p-6">
                                <h3 class="text-sm font-bold text-amber-800 mb-2 flex items-center gap-2">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    Konfirmasi Transfer Manual
                                </h3>
                                <p class="text-[11px] text-amber-700 mb-5 leading-relaxed">
                                    Pembayaran Anda sedang menunggu transfer. Mohon unggah bukti pembayaran agar tim kami dapat segera memverifikasi donasi Anda.
                                </p>

                                <form action="{{ route('donation.upload.proof', $transaction->order_id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                    @csrf
                                    <div class="relative group">
                                        <input type="file" name="proof" id="proof_upload" accept="image/*" required class="hidden">
                                        <label for="proof_upload" class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-amber-200 rounded-2xl bg-white hover:bg-amber-100/30 cursor-pointer transition-all">
                                            <i class="fas fa-image text-amber-300 text-2xl mb-2"></i>
                                            <span class="text-[11px] font-bold text-amber-600 uppercase tracking-widest" id="file_label">Pilih Foto Bukti</span>
                                        </label>
                                    </div>
                                    <button type="submit" class="w-full bg-amber-600 hover:bg-amber-700 text-white font-black py-4 rounded-2xl text-xs uppercase tracking-widest shadow-md transition-all">
                                        Kirim Bukti Sekarang
                                    </button>
                                </form>
                            </div>
                        </div>
                        @elseif($transaction->proof_of_transfer_path)
                        <div class="mt-8 pt-8 border-t border-slate-100 text-center">
                            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-slate-50 border border-slate-100">
                                <i class="fas fa-clock text-blue-500 text-xs"></i>
                                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Menunggu Verifikasi Admin</span>
                            </div>
                        </div>
                        @endif
                    @endif
                @endauth
            </div>
        </div>

        <!-- LOWER TRUST BADGES -->
        <div class="mt-10 flex flex-wrap justify-center gap-8 opacity-40 grayscale contrast-125">
            <div class="flex items-center gap-2">
                <i class="fas fa-shield-check text-xl"></i>
                <span class="text-[10px] font-black uppercase tracking-tighter">Verified System</span>
            </div>
            <div class="flex items-center gap-2">
                <i class="fas fa-lock-alt text-xl"></i>
                <span class="text-[10px] font-black uppercase tracking-tighter">End-to-end Encrypted</span>
            </div>
            <div class="flex items-center gap-2">
                <i class="fas fa-heart-circle text-xl"></i>
                <span class="text-[10px] font-black uppercase tracking-tighter">100% Transparent</span>
            </div>
        </div>

        <p class="text-center mt-12 text-[10px] text-slate-400 font-bold uppercase tracking-[0.3em]">
            &copy; {{ date('Y') }} DonGiv Platform &bull; Terimakasih Sudah Berdonasi 
        </p>
    </div>

    <script>
        // Simple file label update
        const fileInput = document.getElementById('proof_upload');
        const fileLabel = document.getElementById('file_label');
        if(fileInput) {
            fileInput.addEventListener('change', (e) => {
                if(e.target.files.length > 0) {
                    fileLabel.innerText = e.target.files[0].name;
                }
            });
        }
    </script>
</body>
</html>