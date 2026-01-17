<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Secure Donation Checkout | Modern Giving Experience</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #F8FAFC;
            color: #1E293B;
            -webkit-font-smoothing: antialiased;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }

        .amount-card {
            @apply relative border-2 border-slate-100 rounded-2xl p-5 cursor-pointer
                   transition-all duration-300 hover:border-blue-300 hover:shadow-md;
        }

        .amount-input:checked + .amount-card {
            @apply border-blue-600 bg-blue-50 ring-4 ring-blue-500/10;
        }

        .quote-box {
            position: relative;
            overflow: hidden;
        }

        .quote-box::before {
            content: '"';
            position: absolute;
            top: -20px;
            left: 10px;
            font-size: 8rem;
            color: rgba(37, 99, 235, 0.05);
            font-family: serif;
        }

        /* Smooth scroll behavior */
        html { scroll-behavior: smooth; }

        /* Consistent border radius */
        .rounded-card { @apply rounded-2xl; }
        .rounded-section { @apply rounded-[2rem]; }
        .rounded-button { @apply rounded-3xl; }
    </style>
</head>

<body class="min-h-screen flex flex-col">

<!-- NAVBAR -->
<nav class="bg-white/70 backdrop-blur-lg border-b border-slate-100 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-600 to-blue-700 flex items-center justify-center shadow-lg shadow-blue-200">
                <i class="fas fa-hand-holding-heart text-white text-lg"></i>
            </div>
            <div class="hidden sm:block">
                <h1 class="text-sm font-extrabold text-slate-900 tracking-tight leading-none">Donasi Kebaikan</h1>
                <p class="text-[10px] text-blue-600 font-bold uppercase tracking-widest mt-1">Langkah Nyata Anda</p>
            </div>
        </div>

        <div class="flex items-center gap-8">
            <div class="hidden lg:flex items-center gap-4 text-[11px] font-bold uppercase tracking-widest text-slate-400">
                <span class="text-blue-600">01. Detail</span>
                <span class="w-10 h-[1px] bg-slate-100"></span>
                <span>02. Pembayaran</span>
            </div>
            <a href="{{ route('home') }}" class="text-xs font-bold text-slate-400 hover:text-red-500 transition-colors uppercase tracking-widest">
                Batal
            </a>
        </div>
    </div>
</nav>

<main class="flex-grow py-10">
    <div class="max-w-7xl mx-auto px-6 flex justify-center w-full">
        @auth
        <div class="w-full">
        <form id="donationForm" method="POST" action="{{ route('donation.process') }}">
            @csrf
            <input type="hidden" name="campaign_id" value="{{ $campaign->id }}">

            <input type="hidden" name="payment_method" value="bank_transfer">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                <!-- KOLOM KIRI: FORMULIR -->
                <div class="lg:col-span-8 space-y-6">

                    <!-- HERO SECTION DALAM FORM -->
                    <div class="relative rounded-section bg-blue-600 p-8 md:p-10 overflow-hidden shadow-2xl shadow-blue-200">
                        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-20 -mt-20 blur-3xl"></div>
                        <div class="relative z-10 max-w-lg">
                            <h2 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-white leading-tight mb-3 sm:mb-4">
                                Ubah Dunia Menjadi Lebih Baik.
                            </h2>
                            <p class="text-blue-100 text-sm md:text-base leading-relaxed opacity-90">
                                Setiap rupiah yang Anda berikan adalah harapan bagi mereka yang membutuhkan. Mari salurkan kebaikan hari ini.
                            </p>
                        </div>
                    </div>

                    <!-- STEP 1: NOMINAL -->
                    <section class="bg-white rounded-section p-6 md:p-8 border border-slate-100 shadow-sm">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-6">
                            <div>
                                <span class="text-[10px] font-black text-blue-600 uppercase tracking-[0.2em] mb-2 block">Step 01</span>
                                <h3 class="text-2xl font-extrabold text-slate-900">Pilih Nominal Donasi</h3>
                            </div>
                            <!-- QUOTE INTERNATIONAL 1 -->
                            <div class="max-w-xs italic text-right text-[11px] text-slate-400 leading-relaxed border-r-2 border-blue-500 pr-4">
                                "No one has ever become poor by giving."
                                <span class="block font-bold mt-1 text-slate-600">— Anne Frank</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                            @foreach([50000, 100000, 250000, 500000] as $amount)
                            <label class="block relative">
                                <input type="radio" name="preset_selector" value="{{ $amount }}" class="amount-input hidden">
                                <div class="amount-card text-center">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase mb-2">Donasi</p>
                                    <p class="text-base font-extrabold text-slate-900">Rp{{ number_format($amount, 0, ',', '.') }}</p>
                                </div>
                            </label>
                            @endforeach
                        </div>

                        <div class="space-y-4">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest ml-1">Masukkan Nominal Lain</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-8 flex items-center pointer-events-none">
                                    <span class="text-2xl font-black text-blue-600">Rp</span>
                                </div>
                                <input type="number" id="real_amount" name="amount"
                                       class="w-full pl-20 pr-10 py-6 rounded-button border-2 border-slate-50 bg-slate-50/50 focus:border-blue-600 focus:bg-white focus:ring-0 outline-none text-3xl font-black transition-all"
                                       placeholder="0">
                            </div>
                        </div>
                    </section>

                    <!-- STEP 2: DATA DIRI -->
                    <section class="bg-white rounded-section p-6 md:p-8 border border-slate-100 shadow-sm space-y-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-[10px] font-black text-blue-600 uppercase tracking-[0.2em] mb-2 block">Step 02</span>
                                <h3 class="text-2xl font-extrabold text-slate-900">Data Pengirim</h3>
                            </div>
                            <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400">
                                <i class="fas fa-id-card text-xl"></i>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-3">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest">Nama Lengkap</label>
                                <input type="text" name="donor_name" value="{{ Auth::user()->name }}"
                                       class="w-full px-6 py-5 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:border-blue-500 outline-none transition-all font-semibold">
                            </div>
                            <div class="space-y-3">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest">Email Notifikasi</label>
                                <input type="email" name="donor_email" value="{{ Auth::user()->email }}"
                                       class="w-full px-6 py-5 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:border-blue-500 outline-none transition-all font-semibold">
                            </div>
                        </div>

                        <!-- FEATURE: SEMBUNYIKAN NAMA -->
                        <div class="p-6 rounded-section border-2 border-dashed border-blue-100 bg-blue-50/20 flex flex-col md:flex-row md:items-center justify-between gap-4 group">
                            <div class="flex items-start gap-5">
                                <div class="w-14 h-14 rounded-2xl bg-white flex items-center justify-center text-blue-600 shadow-sm border border-blue-50">
                                    <i class="fas fa-user-secret text-2xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-extrabold text-slate-900 text-lg">Sembunyikan Nama Saya</h4>
                                    <p class="text-sm text-slate-500 leading-relaxed mt-1">Gunakan identitas "Hamba Allah" pada daftar donatur publik.</p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer scale-110">
                                <input type="checkbox" name="anonymous" value="1" class="sr-only peer">
                                <div class="w-14 h-7 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </section>

                    <!-- QUOTE INTERNATIONAL 2 -->
                    <div class="bg-slate-900 rounded-section p-8 md:p-12 text-center relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-full h-full opacity-10">
                            <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                                <path d="M0 100 C 20 0 50 0 100 100 Z" fill="white"></path>
                            </svg>
                        </div>
                        <div class="relative z-10">
                            <p class="text-xl md:text-2xl font-medium text-blue-100 italic leading-relaxed">
                                "It's not how much we give but how much love we put into giving."
                            </p>
                            <h5 class="mt-6 text-white font-black uppercase tracking-[0.3em] text-xs">— Mother Teresa —</h5>
                        </div>
                    </div>

                </div>

                <!-- KOLOM KANAN: RINGKASAN (STICKY) -->
                <div class="lg:col-span-4 space-y-8">
                    <div class="lg:sticky lg:top-28 space-y-6">

                        <!-- SUMMARY CARD -->
                        <div class="bg-white rounded-section p-6 border border-slate-100 shadow-xl shadow-slate-200/50">
                            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6 border-b border-slate-50 pb-3">
                                Rincian Donasi Anda
                            </h4>

                            <div class="space-y-4 mb-8">
                                <div class="flex justify-between items-start gap-4">
                                    <span class="text-xs font-bold text-slate-400 uppercase">Program</span>
                                    <span class="text-sm font-black text-slate-900 text-right leading-tight max-w-[60%]">
                                        {{ $campaign?->title ?? 'Bantuan Kemanusiaan Global' }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-bold text-slate-400 uppercase">Gateway</span>
                                    <span class="text-[10px] font-black bg-blue-600 text-white px-3 py-1 rounded-full uppercase">SECURE PAYMENT</span>
                                </div>

                                <div class="pt-6 border-t border-slate-100">
                                    <p class="text-xs font-bold text-slate-400 uppercase mb-2">Total Pembayaran</p>
                                    <div class="flex items-baseline gap-2 text-blue-600">
                                        <span class="text-xl font-black">Rp</span>
                                        <span id="display_amount" class="text-5xl font-black tracking-tighter">0</span>
                                    </div>
                                </div>
                            </div>

                            <!-- PEMBAYARAN SECTION -->
                            <div class="mt-6">
                                <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 border-b border-slate-50 pb-3">
                                    Metode Pembayaran
                                </h4>

                                <div class="space-y-4">
                                    <!-- Bank Transfer Option -->
                                    <div class="payment-option">
                                        <label class="flex items-center p-4 border-2 border-slate-100 rounded-2xl cursor-pointer hover:border-blue-300 transition-all">
                                            <input type="radio" name="payment_method" value="bank_transfer" class="hidden peer" checked>
                                            <div class="flex items-center justify-between w-full">
                                                <div class="flex items-center gap-4">
                                                    <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
                                                        <i class="fas fa-university text-blue-600"></i>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-slate-900">Transfer Bank</p>
                                                        <p class="text-xs text-slate-500">BCA, BNI, BRI, Mandiri</p>
                                                    </div>
                                                </div>
                                                <div class="w-6 h-6 rounded-full border-2 border-slate-300 flex items-center justify-center peer-checked:border-blue-600 peer-checked:bg-blue-600">
                                                    <div class="w-3 h-3 rounded-full bg-blue-600 hidden peer-checked:block"></div>
                                                </div>
                                            </div>
                                        </label>
                                        <div class="bank-transfer-options mt-3 pl-14 space-y-3 hidden">
                                            <label class="block">
                                                <span class="text-xs font-medium text-slate-500">Pilih Bank</span>
                                                <select name="selected_bank" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none">
                                                    <option value="">Pilih Bank Tujuan</option>
                                                    <option value="BCA">BCA - Bank Central Asia</option>
                                                    <option value="BNI">BNI - Bank Negara Indonesia</option>
                                                    <option value="BRI">BRI - Bank Rakyat Indonesia</option>
                                                    <option value="Mandiri">Mandiri - Bank Mandiri</option>
                                                    <option value="BTN">BTN - Bank Tabungan Negara</option>
                                                    <option value="BTPN">BTPN - Bank BTPN</option>
                                                </select>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- E-Wallet Option -->
                                    <div class="payment-option">
                                        <label class="flex items-center p-4 border-2 border-slate-100 rounded-2xl cursor-pointer hover:border-blue-300 transition-all">
                                            <input type="radio" name="payment_method" value="e_wallet" class="hidden peer">
                                            <div class="flex items-center justify-between w-full">
                                                <div class="flex items-center gap-4">
                                                    <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center">
                                                        <i class="fas fa-wallet text-green-600"></i>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-slate-900">E-Wallet</p>
                                                        <p class="text-xs text-slate-500">OVO, GoPay, DANA, LinkAja</p>
                                                    </div>
                                                </div>
                                                <div class="w-6 h-6 rounded-full border-2 border-slate-300 flex items-center justify-center peer-checked:border-blue-600 peer-checked:bg-blue-600">
                                                    <div class="w-3 h-3 rounded-full bg-blue-600 hidden peer-checked:block"></div>
                                                </div>
                                            </div>
                                        </label>
                                        <div class="ewallet-options mt-3 pl-14 space-y-3 hidden">
                                            <label class="block">
                                                <span class="text-xs font-medium text-slate-500">Pilih E-Wallet</span>
                                                <select name="selected_ewallet" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none">
                                                    <option value="">Pilih E-Wallet</option>
                                                    <option value="OVO">OVO</option>
                                                    <option value="GoPay">GoPay</option>
                                                    <option value="DANA">DANA</option>
                                                    <option value="LinkAja">LinkAja</option>
                                                    <option value="ShopeePay">ShopeePay</option>
                                                </select>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- QRIS Option -->
                                    <div class="payment-option">
                                        <label class="flex items-center p-4 border-2 border-slate-100 rounded-2xl cursor-pointer hover:border-blue-300 transition-all">
                                            <input type="radio" name="payment_method" value="qris" class="hidden peer">
                                            <div class="flex items-center justify-between w-full">
                                                <div class="flex items-center gap-4">
                                                    <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center">
                                                        <i class="fas fa-qrcode text-purple-600"></i>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-slate-900">QRIS</p>
                                                        <p class="text-xs text-slate-500">Scan QR untuk pembayaran</p>
                                                    </div>
                                                </div>
                                                <div class="w-6 h-6 rounded-full border-2 border-slate-300 flex items-center justify-center peer-checked:border-blue-600 peer-checked:bg-blue-600">
                                                    <div class="w-3 h-3 rounded-full bg-blue-600 hidden peer-checked:block"></div>
                                                </div>
                                            </div>
                                        </label>
                                        <div class="qris-options mt-3 pl-14 space-y-3 hidden">
                                            <label class="block">
                                                <span class="text-xs font-medium text-slate-500">Pilih Aplikasi QRIS</span>
                                                <select name="selected_qris" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none">
                                                    <option value="">Pilih Aplikasi Pembayaran</option>
                                                    <option value="OVO">OVO</option>
                                                    <option value="GoPay">GoPay</option>
                                                    <option value="DANA">DANA</option>
                                                    <option value="LinkAja">LinkAja</option>
                                                    <option value="ShopeePay">ShopeePay</option>
                                                    <option value="BCA Mobile">BCA Mobile</option>
                                                    <option value="BRI Mobile">BRI Mobile</option>
                                                </select>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" id="submitBtn"
                                    class="w-full bg-slate-900 hover:bg-blue-600 text-white font-black py-6 rounded-button transition-all duration-500 transform active:scale-[0.97] shadow-xl hover:shadow-blue-500/20 flex items-center justify-center gap-3 group">
                                <span class="uppercase tracking-widest text-sm">Konfirmasi Donasi</span>
                                <i class="fas fa-arrow-right text-xs transition-transform group-hover:translate-x-1"></i>
                            </button>

                            <p class="text-center text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em] mt-8">
                                Secured by SSL & Bank Indonesia
                            </p>
                        </div>

                        <!-- SIDEBAR QUOTE 3 -->
                        <div class="bg-blue-50 rounded-section p-6 border border-blue-100/50">
                            <p class="text-xs text-blue-700 font-medium italic leading-relaxed">
                                "We make a living by what we get, but we make a life by what we give."
                            </p>
                            <p class="mt-4 text-[10px] font-black text-blue-900 uppercase tracking-widest">— Winston Churchill</p>
                        </div>

                        <!-- TRUST BADGES -->
                        <div class="grid grid-cols-3 gap-3 opacity-30 grayscale">
                            <div class="flex justify-center"><i class="fab fa-cc-visa text-3xl"></i></div>
                            <div class="flex justify-center"><i class="fab fa-cc-mastercard text-3xl"></i></div>
                            <div class="flex justify-center"><i class="fas fa-university text-3xl"></i></div>
                        </div>
                    </div>
                </div>

            </div>
        </form>
        </div>
        @endauth
    </div>
</main>

<footer class="py-16 bg-white border-t border-slate-50 mt-20">
    <div class="max-w-7xl mx-auto px-6 text-center">
        <div class="flex justify-center gap-8 mb-8 text-slate-300">
            <i class="fab fa-instagram text-xl"></i>
            <i class="fab fa-facebook text-xl"></i>
            <i class="fab fa-twitter text-xl"></i>
        </div>
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em]">
            &copy; {{ date('Y') }} &bull; DonGiv &bull; Terdaftar & Diawasi
        </p>
    </div>
</footer>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('real_amount');
    const display = document.getElementById('display_amount');
    const presetRadios = document.querySelectorAll('input[name=preset_selector]');
    const submitBtn = document.getElementById('submitBtn');
    const form = document.getElementById('donationForm');

    const formatIDR = (num) => {
        return Number(num).toLocaleString('id-ID');
    };

    presetRadios.forEach(radio => {
        radio.addEventListener('change', e => {
            const val = e.target.value;
            input.value = val;
            display.innerText = formatIDR(val);

            // Animasi kecil saat berubah
            display.classList.add('scale-110');
            setTimeout(() => display.classList.remove('scale-110'), 200);
        });
    });

    input.addEventListener('input', e => {
        const val = e.target.value;
        display.innerText = formatIDR(val || 0);

        // Hapus seleksi radio jika input manual
        if(val) {
            presetRadios.forEach(r => r.checked = false);
        }
    });

    form.addEventListener('submit', () => {
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
        submitBtn.innerHTML = `
            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="uppercase tracking-widest text-sm">Sedang Memproses...</span>
        `;
    });
});

// Toggle payment method options
document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
    radio.addEventListener('change', function() {
        // Hide all options
        document.querySelectorAll('.bank-transfer-options, .ewallet-options, .qris-options').forEach(el => {
            el.classList.add('hidden');
        });

        // Show selected option
        if(this.value === 'bank_transfer') {
            document.querySelector('.bank-transfer-options').classList.remove('hidden');
        } else if(this.value === 'e_wallet') {
            document.querySelector('.ewallet-options').classList.remove('hidden');
        } else if(this.value === 'qris') {
            document.querySelector('.qris-options').classList.remove('hidden');
        }
    });
});

// Initialize with bank transfer options visible
document.querySelector('.bank-transfer-options').classList.remove('hidden');
</script>

</body>
</html>