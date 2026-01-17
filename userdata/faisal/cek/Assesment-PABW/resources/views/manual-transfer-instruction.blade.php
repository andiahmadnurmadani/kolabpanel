<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Instruksi Transfer - DonGiv</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fadeIn {
            animation: fadeIn 0.5s ease-out forwards;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-cyan-50 text-gray-800 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <img src="{{ asset('images/dongiv-logo.png') }}" alt="DonGiv Logo" class="h-8">
                    <span class="text-xl font-bold text-primary">DonGiv</span>
                </div>
                <a href="{{ route('home') }}" class="text-gray-700 hover:text-primary font-medium transition">
                    <i class="fas fa-home mr-1"></i>Beranda
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-10">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Instruksi Transfer Donasi</h1>
                <p class="text-lg text-gray-600">Silakan ikuti langkah-langkah berikut untuk menyelesaikan donasi Anda</p>
            </div>

            <!-- Success Message -->
            @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl">
                <div class="flex items-start">
                    <div class="flex-shrink-0 mr-3">
                        <i class="fas fa-check-circle text-xl mt-1"></i>
                    </div>
                    <div>
                        <p class="font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Error Message -->
            @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
                <div class="flex items-start">
                    <div class="flex-shrink-0 mr-3">
                        <i class="fas fa-exclamation-circle text-xl mt-1"></i>
                    </div>
                    <div>
                        <p class="font-medium">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column - Instructions -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8 border border-gray-100">
                        <!-- Transaction Summary -->
                        <div class="bg-gradient-to-r from-blue-50 to-cyan-50 p-6 rounded-xl mb-8 border border-blue-100">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
                                <div class="p-4 bg-white rounded-lg shadow-sm">
                                    <p class="text-sm text-gray-600">Order ID</p>
                                    <p class="text-xl font-bold text-primary">{{ $transaction->order_id }}</p>
                                </div>
                                <div class="p-4 bg-white rounded-lg shadow-sm">
                                    <p class="text-sm text-gray-600">Jumlah Donasi</p>
                                    <p class="text-xl font-bold text-green-600">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</p>
                                </div>
                                <div class="p-4 bg-white rounded-lg shadow-sm">
                                    <p class="text-sm text-gray-600">Batas Waktu</p>
                                    <p class="text-xl font-bold text-amber-600">{{ $transaction->transfer_deadline->format('d M Y H:i') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method Info -->
                        <div class="bg-blue-50 p-5 rounded-xl mb-8 border border-blue-200">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 mr-4">
                                    <i class="fas fa-credit-card text-blue-600 text-2xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-800">Metode Pembayaran</h3>
                                    <p class="text-lg text-gray-700">
                                        @if($transaction->payment_method == 'bank_transfer')
                                            <i class="fas fa-university text-blue-600 mr-1"></i> Bank Transfer
                                        @elseif($transaction->payment_method == 'e_wallet')
                                            <i class="fab fa-google-pay text-green-600 mr-1"></i> e-Wallet
                                        @elseif($transaction->payment_method == 'qris')
                                            <i class="fas fa-qrcode text-purple-600 mr-1"></i> QRIS
                                        @else
                                            {{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Transfer Instructions -->
                        <div class="mb-8">
                            <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                                <i class="fas fa-info-circle text-blue-600 mr-3"></i>
                                Langkah-langkah Transfer
                            </h2>

                            <div class="space-y-6">
                                <div class="flex items-start">
                                    <div class="bg-blue-100 w-12 h-12 rounded-full flex items-center justify-center mr-5 flex-shrink-0">
                                        <span class="text-blue-700 font-bold text-lg">1</span>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="font-bold text-lg text-gray-800">
                                            @if($transaction->payment_method == 'bank_transfer')
                                                Transfer ke Rekening Berikut
                                            @elseif($transaction->payment_method == 'e_wallet')
                                                Transfer ke e-Wallet Berikut
                                            @elseif($transaction->payment_method == 'qris')
                                                Scan QRIS Berikut
                                            @else
                                                Lakukan Pembayaran
                                            @endif
                                        </h3>
                                        <p class="text-gray-600 mt-2">
                                            @if($transaction->payment_method == 'bank_transfer')
                                                Silakan transfer jumlah yang tercantum ke rekening berikut:
                                            @elseif($transaction->payment_method == 'e_wallet')
                                                Silakan transfer jumlah yang tercantum ke e-wallet berikut:
                                            @elseif($transaction->payment_method == 'qris')
                                                Silakan scan QRIS dibawah untuk melakukan pembayaran:
                                            @else
                                                Silakan lakukan pembayaran sesuai metode yang dipilih:
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <!-- Payment Method Specific Information -->
                                @if($transaction->payment_method == 'bank_transfer')
                                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 p-6 rounded-xl border border-gray-200">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                                <p class="text-sm text-gray-600">Nama Bank</p>
                                                <p class="font-bold text-gray-800">{{ $transaction->bank_name }}</p>
                                            </div>
                                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                                <p class="text-sm text-gray-600">Nomor Rekening</p>
                                                <p class="font-bold font-mono text-gray-800">{{ $transaction->bank_account_number }}</p>
                                            </div>
                                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                                <p class="text-sm text-gray-600">Atas Nama</p>
                                                <p class="font-bold text-gray-800">{{ $transaction->bank_account_name }}</p>
                                            </div>
                                            <div class="bg-white p-4 rounded-lg shadow-sm border-2 border-red-500">
                                                <p class="text-sm text-gray-600">Jumlah Transfer</p>
                                                <p class="font-bold text-2xl text-red-600">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @elseif($transaction->payment_method == 'e_wallet')
                                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 p-6 rounded-xl border border-gray-200">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                                <p class="text-sm text-gray-600">e-Wallet</p>
                                                <p class="font-bold text-gray-800">
                                                    @if(json_decode($transaction->payment_method_data ?? 'null', true)['selected_ewallet'])
                                                        {{ json_decode($transaction->payment_method_data ?? 'null', true)['selected_ewallet'] }}
                                                    @else
                                                        e-Wallet
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                                <p class="text-sm text-gray-600">Nomor e-Wallet</p>
                                                <p class="font-bold font-mono text-gray-800">{{ $transaction->bank_account_number }}</p>
                                            </div>
                                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                                <p class="text-sm text-gray-600">Nama Pengguna</p>
                                                <p class="font-bold text-gray-800">{{ $transaction->bank_account_name }}</p>
                                            </div>
                                            <div class="bg-white p-4 rounded-lg shadow-sm border-2 border-red-500">
                                                <p class="text-sm text-gray-600">Jumlah Transfer</p>
                                                <p class="font-bold text-2xl text-red-600">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @elseif($transaction->payment_method == 'qris')
                                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 p-6 rounded-xl border border-gray-200">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                                <p class="text-sm text-gray-600">QRIS Format</p>
                                                <p class="font-bold text-gray-800">
                                                    @if(json_decode($transaction->payment_method_data ?? 'null', true)['selected_qris'])
                                                        {{ json_decode($transaction->payment_method_data ?? 'null', true)['selected_qris'] }}
                                                    @else
                                                        QRIS
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                                <p class="text-sm text-gray-600">Nomor Rekening</p>
                                                <p class="font-bold font-mono text-gray-800">{{ $transaction->bank_account_number }}</p>
                                            </div>
                                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                                <p class="text-sm text-gray-600">Atas Nama</p>
                                                <p class="font-bold text-gray-800">{{ $transaction->bank_account_name }}</p>
                                            </div>
                                            <div class="bg-white p-4 rounded-lg shadow-sm border-2 border-red-500">
                                                <p class="text-sm text-gray-600">Jumlah Transfer</p>
                                                <p class="font-bold text-2xl text-red-600">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</p>
                                            </div>
                                        </div>

                                        <div class="mt-6 text-center">
                                            <div class="inline-block bg-white p-4 rounded-lg border-2 border-gray-300">
                                                <div class="bg-gray-200 border-2 border-dashed rounded-xl w-48 h-48 flex items-center justify-center mx-auto">
                                                    <i class="fas fa-qrcode text-4xl text-gray-500"></i>
                                                </div>
                                                <p class="mt-3 text-sm text-gray-600">Scan QRIS untuk pembayaran</p>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 p-6 rounded-xl border border-gray-200">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                                <p class="text-sm text-gray-600">Metode</p>
                                                <p class="font-bold text-gray-800">{{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}</p>
                                            </div>
                                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                                <p class="text-sm text-gray-600">Nomor Rekening</p>
                                                <p class="font-bold font-mono text-gray-800">{{ $transaction->bank_account_number }}</p>
                                            </div>
                                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                                <p class="text-sm text-gray-600">Atas Nama</p>
                                                <p class="font-bold text-gray-800">{{ $transaction->bank_account_name }}</p>
                                            </div>
                                            <div class="bg-white p-4 rounded-lg shadow-sm border-2 border-red-500">
                                                <p class="text-sm text-gray-600">Jumlah Transfer</p>
                                                <p class="font-bold text-2xl text-red-600">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                                <div class="flex items-start mt-6">
                                    <div class="bg-green-100 w-12 h-12 rounded-full flex items-center justify-center mr-5 flex-shrink-0">
                                        <span class="text-green-700 font-bold text-lg">2</span>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="font-bold text-lg text-gray-800">
                                            @if($transaction->payment_method == 'bank_transfer')
                                                Lakukan Transfer Bank
                                            @elseif($transaction->payment_method == 'e_wallet')
                                                Lakukan Pembayaran e-Wallet
                                            @elseif($transaction->payment_method == 'qris')
                                                Lakukan Pembayaran QRIS
                                            @else
                                                Lakukan Pembayaran
                                            @endif
                                        </h3>
                                        <p class="text-gray-600 mt-2">
                                            @if($transaction->payment_method == 'bank_transfer')
                                                Silakan lakukan transfer sesuai dengan jumlah donasi dan rekening yang tercantum di atas.
                                            @elseif($transaction->payment_method == 'e_wallet')
                                                Silakan lakukan pembayaran melalui aplikasi e-wallet yang Anda pilih.
                                            @elseif($transaction->payment_method == 'qris')
                                                Silakan scan QRIS dan lakukan pembayaran sesuai jumlah yang tertera.
                                            @else
                                                Silakan lakukan pembayaran sesuai metode yang Anda pilih.
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-start mt-6">
                                    <div class="bg-amber-100 w-12 h-12 rounded-full flex items-center justify-center mr-5 flex-shrink-0">
                                        <span class="text-amber-700 font-bold text-lg">3</span>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="font-bold text-lg text-gray-800">Upload Bukti Pembayaran</h3>
                                        <p class="text-gray-600 mt-2">Setelah melakukan pembayaran, silakan upload bukti pembayaran Anda pada form di bawah.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Important Notes -->
                        <div class="bg-gradient-to-r from-amber-50 to-yellow-50 p-6 rounded-xl border border-amber-200 mb-8">
                            <h3 class="font-bold text-xl text-amber-800 mb-4 flex items-center">
                                <i class="fas fa-exclamation-triangle text-amber-600 mr-3"></i>
                                Catatan Penting
                            </h3>
                            <ul class="list-disc pl-5 space-y-2 text-amber-700">
                                <li>Pastikan jumlah pembayaran sesuai dengan jumlah donasi yang Anda tentukan</li>
                                @if($transaction->payment_method == 'bank_transfer')
                                <li>Batas waktu transfer adalah <strong>{{ $transaction->transfer_deadline->format('d M Y H:i') }}</strong></li>
                                <li>Jika transfer dilakukan setelah batas waktu, maka otomatis akan dibatalkan</li>
                                <li>Gunakan aplikasi mobile banking atau ATM untuk proses transfer yang lebih mudah</li>
                                @elseif($transaction->payment_method == 'e_wallet')
                                <li>Gunakan aplikasi e-wallet yang Anda pilih untuk melakukan pembayaran</li>
                                <li>Pastikan nomor atau akun e-wallet tujuan benar</li>
                                @elseif($transaction->payment_method == 'qris')
                                <li>Gunakan aplikasi pembayaran yang mendukung QRIS</li>
                                <li>Pastikan QRIS terbaca dengan jelas saat scan</li>
                                @endif
                                <li>Simpan bukti pembayaran Anda sebagai referensi</li>
                            </ul>
                        </div>

                        <!-- Upload Transfer Proof Form -->
                        <div class="bg-white border border-gray-200 rounded-xl p-6 transition-all duration-300 hover:shadow-md">
                            <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center animate-fadeIn">
                                <i class="fas fa-upload text-green-600 mr-3"></i>
                                Upload Bukti Transfer
                            </h2>

                            @if($transaction->proof_of_transfer_path)
                                <!-- Show uploaded proof if exists -->
                                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg animate-fadeIn">
                                    <div class="flex items-center">
                                        <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                                        <div>
                                            <p class="font-medium text-green-800">Bukti Transfer Telah Diupload</p>
                                            @if($transaction->status === 'VERIFIED')
                                                <p class="text-sm text-green-600 flex items-center"><i class="fas fa-badge-check mr-1 text-green-500"></i> Pembayaran Berhasil - Ditambahkan ke Riwayat Transaksi</p>
                                            @elseif($transaction->status === 'PENDING_VERIFICATION')
                                                <p class="text-sm text-amber-600 flex items-center"><i class="fas fa-clock mr-1 text-amber-500"></i> Menunggu Verifikasi Admin - Ditambahkan ke Riwayat Transaksi</p>
                                            @else
                                                <p class="text-sm text-blue-600 flex items-center"><i class="fas fa-history mr-1 text-blue-500"></i> Ditambahkan ke Riwayat Transaksi</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <p class="text-sm font-medium text-gray-700 mb-2">Pratinjau Bukti Transfer:</p>
                                        <div class="flex justify-center">
                                            <img src="{{ asset('storage/' . $transaction->proof_of_transfer_path) }}"
                                                 alt="Bukti Transfer"
                                                 class="max-w-xs max-h-48 rounded-lg border shadow-sm transition-transform duration-300 hover:scale-105"
                                                 onerror="this.onerror=null; this.src='https://placehold.co/300x200?text=Gambar+Tidak+Dapat+Dimuat';">
                                        </div>
                                    </div>

                                    @if($transaction->status === 'VERIFIED')
                                        <div class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                                            <p class="text-sm text-blue-800 flex items-center">
                                                <i class="fas fa-star mr-2"></i>Terima kasih! Pembayaran Anda telah diverifikasi. 1 Koin telah ditambahkan ke akun Anda.
                                            </p>
                                        </div>
                                    @endif

                                    <!-- Selesai Pembayaran Button -->
                                    <div class="mt-8 text-center">
                                        <a href="{{ route('profiles.index') }}" class="inline-flex items-center bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-bold py-4 px-8 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg mb-4" style="background: linear-gradient(to right, #16a34a, #15803d) !important; color: white !important; border: none !important; text-decoration: none;">
                                            <i class="fas fa-check-circle mr-2"></i>Selesai Pembayaran
                                        </a>
                                        <p class="text-sm text-green-700 font-medium mb-2">Pembayaran berhasil ditambahkan ke riwayat transaksi Anda!</p>
                                        <p class="text-sm text-gray-600">Status saat ini: <span class="font-semibold text-amber-600">Pending Verification</span></p>
                                        <div class="mt-4 bg-gradient-to-r from-blue-50 to-cyan-50 border border-blue-200 rounded-xl p-4">
                                            <p class="text-sm text-blue-800 flex items-center">
                                                <i class="fas fa-info-circle mr-2"></i>Cek riwayat transaksi Anda di halaman profil untuk melihat status pembayaran terkini
                                            </p>
                                        </div>
                                        <div class="mt-4 bg-gradient-to-r from-amber-50 to-yellow-50 border border-amber-200 rounded-xl p-4">
                                            <p class="text-sm text-amber-800">
                                                <i class="fas fa-clock mr-2"></i>Pembayaran Anda akan diverifikasi oleh admin maksimal dalam 24 jam
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <!-- Upload form if proof not yet uploaded -->
                                <form action="{{ route('donation.upload.proof', ['order_id' => $transaction->order_id]) }}" method="POST" enctype="multipart/form-data" id="proofForm">
                                    @csrf
                                    <div class="mb-6 transition-all duration-300">
                                        <label class="block text-sm font-medium text-gray-700 mb-3">Pilih File Bukti Transfer</label>
                                        <div class="flex items-center justify-center w-full">
                                            <label for="proof" class="flex flex-col items-center justify-center w-full h-48 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition-all duration-300 hover:border-blue-400">
                                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3 transition-transform duration-300 hover:scale-110"></i>
                                                    <p class="text-sm text-gray-600 mb-2">Klik untuk upload atau drag and drop</p>
                                                    <p class="text-xs text-gray-500">JPG, PNG, GIF(max. 2MB)</p>
                                                </div>
                                                <input id="proof" type="file" name="proof" accept="image/*" required class="hidden" />
                                            </label>
                                        </div>
                                        <p class="text-sm text-gray-500 mt-3">Format: JPG, PNG, maksimal 2MB</p>
                                    </div>

                                    <div id="preview" class="hidden mb-6 animate-fadeIn">
                                        <label class="block text-sm font-medium text-gray-700 mb-3">Pratinjau</label>
                                        <div class="flex justify-center">
                                            <img id="previewImage" src="" alt="Preview" class="max-w-xs max-h-48 rounded-lg border shadow-sm">
                                        </div>
                                    </div>

                                    <button type="submit" id="uploadBtn" class="w-full bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-bold py-4 px-6 rounded-xl transition-all duration-300 transform hover:scale-[1.02] shadow-lg hover:shadow-xl" style="background: linear-gradient(to right, #16a34a, #15803d) !important; color: white !important; border: none !important;">
                                        <i class="fas fa-cloud-upload-alt mr-2"></i>Upload Bukti Transfer
                                    </button>
                                </form>
                            @endif

                            <!-- Navigation Options for Users who choose not to upload now -->
                            @if(!$transaction->proof_of_transfer_path)
                                <div class="mt-6 pt-6 border-t border-gray-200">
                                    <p class="text-center text-sm text-gray-600 mb-4">Anda juga dapat melanjutkan nanti</p>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <a href="{{ route('profiles.index') }}" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-medium py-3 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg flex items-center justify-center">
                                            <i class="fas fa-user mr-2"></i> Lihat Profil Saya
                                        </a>
                                        <a href="{{ route('home') }}" class="bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white font-medium py-3 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg flex items-center justify-center">
                                            <i class="fas fa-home mr-2"></i> Kembali ke Home
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Column - Information -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 sticky top-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-6 text-center">Keamanan Donasi</h3>

                        <div class="space-y-5">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 mr-3">
                                    <div class="bg-green-100 w-10 h-10 rounded-full flex items-center justify-center">
                                        <i class="fas fa-shield-alt text-green-600"></i>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800">Aman & Terpercaya</h4>
                                    <p class="text-sm text-gray-600">Proses transfer terverifikasi secara manual</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="flex-shrink-0 mr-3">
                                    <div class="bg-blue-100 w-10 h-10 rounded-full flex items-center justify-center">
                                        <i class="fas fa-check-circle text-blue-600"></i>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800">Verifikasi Cepat</h4>
                                    <p class="text-sm text-gray-600">Verifikasi dalam waktu 1x24 jam</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="flex-shrink-0 mr-3">
                                    <div class="bg-amber-100 w-10 h-10 rounded-full flex items-center justify-center">
                                        <i class="fas fa-receipt text-amber-600"></i>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800">Laporan Transparan</h4>
                                    <p class="text-sm text-gray-600">Laporan penggunaan dana tersedia</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <h4 class="font-bold text-gray-800 mb-4 text-center">Rekening Donasi</h4>
                            <div class="text-center">
                                <div class="bg-blue-50 rounded-lg p-4">
                                    <p class="text-sm text-gray-600 mb-1">{{ $transaction->bank_name }}</p>
                                    <p class="font-bold text-gray-800">{{ $transaction->bank_account_number }}</p>
                                    <p class="text-xs text-gray-500 mt-1">a.n. {{ $transaction->bank_account_name }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Validation Section -->
                    <div class="bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl shadow-lg p-6 mt-6 text-white">
                        <h3 class="font-bold text-lg mb-3 flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            Verifikasi Bukti Transfer
                        </h3>
                        <p class="text-sm mb-4">Setelah upload bukti transfer, admin akan memverifikasi dalam waktu 1x24 jam</p>
                        <div class="inline-flex items-center bg-white text-green-600 font-medium py-2 px-4 rounded-lg text-sm transition-all w-full justify-center">
                            <i class="fas fa-check mr-2"></i>Verifikasi Otomatis
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-gray-300 py-12 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="text-center md:text-left">
                    <h4 class="text-xl font-bold text-white mb-4">DonGiv</h4>
                    <p class="text-sm">Creating positive change through transparent and effective charitable giving.</p>
                </div>
                <div class="text-center md:text-left">
                    <h5 class="font-semibold text-white mb-4">Explore</h5>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('home') }}" class="hover:text-white transition">Home</a></li>
                        <li><a href="{{ route('donation.details') }}" class="hover:text-white transition">Donations</a></li>
                        <li><a href="#" class="hover:text-white transition">Volunteer</a></li>
                        <li><a href="#" class="hover:text-white transition">About Us</a></li>
                    </ul>
                </div>
                <div class="text-center md:text-left">
                    <h5 class="font-semibold text-white mb-4">Legal</h5>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white transition">Privacy Policy</a></li>
                        <li><a href="#" class="hover:text-white transition">Terms of Service</a></li>
                        <li><a href="#" class="hover:text-white transition">Charity Registration</a></li>
                    </ul>
                </div>
                <div class="text-center md:text-left">
                    <h5 class="font-semibold text-white mb-4">Contact Us</h5>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white transition">Support Center</a></li>
                        <li><a href="#" class="hover:text-white transition">Partnership Inquiry</a></li>
                        <li><a href="#" class="hover:text-white transition">Media Contact</a></li>
                    </ul>
                </div>
            </div>
            <div class="mt-8 pt-8 border-t border-gray-700 text-center text-sm">
                <p>&copy; {{ date('Y') }} DonGiv — Making a Difference Together ❤️</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Image preview functionality
            const proofInput = document.getElementById('proof');
            const previewContainer = document.getElementById('preview');
            const previewImage = document.getElementById('previewImage');

            proofInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        previewImage.src = e.target.result;
                        previewContainer.classList.remove('hidden');
                    }

                    reader.readAsDataURL(file);
                } else {
                    previewContainer.classList.add('hidden');
                }
            });
        });
    </script>
</body>
</html>