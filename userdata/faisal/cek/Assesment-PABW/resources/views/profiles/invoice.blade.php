<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Invoice Donasi - DonGiv</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
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
        <div class="max-w-4xl mx-auto">
            <!-- Invoice Header -->
            <div class="bg-white rounded-2xl shadow-lg p-8 mb-8 border border-gray-100">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">Invoice Donasi</h1>
                        <p class="text-gray-600 mt-2">ID Transaksi: {{ $transaction->order_id }}</p>
                    </div>
                    <div class="mt-4 md:mt-0 bg-blue-50 px-4 py-2 rounded-lg">
                        <span class="font-semibold text-blue-800">Status:</span>
                        <span class="ml-2 px-3 py-1 rounded-full text-sm font-semibold 
                            {{ $transaction->status === 'VERIFIED' || $transaction->status === 'paid' ? 'bg-green-100 text-green-800' : 
                               ($transaction->status === 'PENDING' ? 'bg-yellow-100 text-yellow-800' : 
                               'bg-red-100 text-red-800') }}">
                            {{ isset($isOldDonation) ? ucfirst($transaction->status) : $transaction->status_label }}
                        </span>
                    </div>
                </div>

                <!-- Donor Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div>
                        <h3 class="font-bold text-gray-800 mb-3">Informasi Donatur</h3>
                        <p class="font-medium">{{ $transaction->donor_name }}</p>
                        <p class="text-gray-600">{{ $transaction->donor_email }}</p>
                        @if($transaction->donor_phone)
                            <p class="text-gray-600">{{ $transaction->donor_phone }}</p>
                        @endif
                    </div>
                    
                    <div>
                        <h3 class="font-bold text-gray-800 mb-3">Detail Donasi</h3>
                        <p class="font-medium">Rp {{ number_format($transaction->amount ?? $transaction->amount, 0, ',', '.') }}</p>
                        <p class="text-gray-600">Tanggal: {{ $transaction->created_at->format('d F Y H:i') }}</p>
                        <p class="text-gray-600">
                            Metode Pembayaran:
                            @if($transaction->payment_method == 'bank_transfer')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-university mr-1"></i> Transfer Bank
                                </span>
                            @elseif($transaction->payment_method == 'e_wallet')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-wallet mr-1"></i> E-Wallet
                                </span>
                            @elseif($transaction->payment_method == 'qris')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    <i class="fas fa-qrcode mr-1"></i> QRIS
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}
                                </span>
                            @endif
                        </p>
                        @if($transaction->payment_method_data)
                            @php
                                $paymentData = json_decode($transaction->payment_method_data, true);
                            @endphp
                            @if($paymentData && isset($paymentData['selected_bank']))
                                <p class="text-gray-600">Bank Tujuan: {{ $paymentData['selected_bank'] }}</p>
                            @elseif($paymentData && isset($paymentData['selected_ewallet']))
                                <p class="text-gray-600">E-Wallet: {{ $paymentData['selected_ewallet'] }}</p>
                            @elseif($paymentData && isset($paymentData['selected_qris']))
                                <p class="text-gray-600">Aplikasi QRIS: {{ $paymentData['selected_qris'] }}</p>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- Campaign Information -->
                @if($transaction->campaign)
                <div class="mb-8">
                    <h3 class="font-bold text-gray-800 mb-3">Kampanye</h3>
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <p class="font-medium text-gray-800">{{ $transaction->campaign->title }}</p>
                        <p class="text-gray-600 text-sm mt-1">{{ Str::limit($transaction->campaign->description, 150) }}</p>
                    </div>
                </div>
                @endif

                <!-- Proof of Transfer (if exists) -->
                @if($transaction->proof_of_transfer_path)
                <div class="mb-8">
                    <h3 class="font-bold text-gray-800 mb-3">Bukti Transfer</h3>
                    <div class="bg-green-50 p-4 rounded-lg border border-green-100">
                        <p class="text-green-800 mb-3">Bukti transfer telah diupload dan sedang menunggu verifikasi admin.</p>
                        <div class="flex justify-center">
                            <img src="{{ $transaction->proof_of_transfer_path }}"
                                 alt="Bukti Transfer"
                                 class="max-w-full max-h-64 rounded-lg border border-gray-200 object-contain"
                                 onerror="this.onerror=null; this.src='https://placehold.co/400x300?text=No+Image';">
                        </div>
                        <div class="mt-3 text-center">
                            <a href="{{ $transaction->proof_of_transfer_path }}"
                               target="_blank"
                               class="inline-flex items-center px-4 py-2 border border-green-300 text-sm font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200">
                                <i class="fas fa-external-link-alt mr-2"></i> Lihat Gambar Penuh
                            </a>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Transaction Details Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                    Donasi untuk {{ $transaction->campaign ? $transaction->campaign->title : 'Kampanye Umum' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    Rp {{ number_format($transaction->amount ?? $transaction->amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th scope="col" class="px-6 py-3 text-sm font-bold text-gray-900">
                                    Rp {{ number_format($transaction->amount ?? $transaction->amount, 0, ',', '.') }}
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Action Buttons -->
                <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-end">
                    <button onclick="window.print()" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg">
                        <i class="fas fa-print mr-2"></i>Cetak Invoice
                    </button>
                    <a href="{{ route('profiles.index') }}" class="bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali ke Riwayat
                    </a>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <h3 class="font-bold text-gray-800 mb-4">Informasi Tambahan</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <i class="fas fa-shield-alt text-green-600 text-2xl mb-2"></i>
                        <h4 class="font-semibold text-gray-800">Aman & Terpercaya</h4>
                        <p class="text-sm text-gray-600 mt-1">Donasi Anda telah diproses dengan aman</p>
                    </div>
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <i class="fas fa-receipt text-blue-600 text-2xl mb-2"></i>
                        <h4 class="font-semibold text-gray-800">Bukti Donasi</h4>
                        <p class="text-sm text-gray-600 mt-1">Invoice ini sebagai bukti donasi sah Anda</p>
                    </div>
                    <div class="text-center p-4 bg-amber-50 rounded-lg">
                        <i class="fas fa-hand-holding-heart text-amber-600 text-2xl mb-2"></i>
                        <h4 class="font-semibold text-gray-800">Dampak Nyata</h4>
                        <p class="text-sm text-gray-600 mt-1">Kontribusi Anda membantu masyarakat</p>
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
</body>
</html>