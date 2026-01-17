<x-app title="Riwayat Donasi - DonGiv">
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
        <div class="max-w-6xl mx-auto">
            <!-- Profile Header -->
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                            <span class="text-2xl font-bold text-blue-700">{{ Auth::user()->name[0] ?? 'U' }}</span>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800">{{ Auth::user()->name }}</h1>
                            <p class="text-gray-600">Total Koin Keamanan: <span class="font-semibold text-green-600">{{ Auth::user()->coins ?? 0 }}</span></p>
                        </div>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('profiles.edit') }}" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-medium py-2 px-4 rounded-lg transition">
                            <i class="fas fa-edit mr-2"></i>Edit Profil
                        </a>
                        <a href="{{ route('profiles.index') }}" class="bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white font-medium py-2 px-4 rounded-lg transition">
                            <i class="fas fa-user mr-2"></i>Profil Saya
                        </a>
                    </div>
                </div>
            </div>

            <!-- Navigation Tabs -->
            <div class="bg-white rounded-2xl shadow-lg mb-8 border border-gray-100">
                <div class="border-b border-gray-200">
                    <nav class="flex -mb-px">
                        <a href="{{ route('profiles.index') }}" class="w-1/2 py-4 px-1 text-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 border-b-2 border-transparent">
                            <i class="fas fa-user mr-2"></i>Profil
                        </a>
                        <a href="{{ route('profiles.index') }}#transaction-history" class="w-1/2 py-4 px-1 text-center text-sm font-medium text-blue-600 border-b-2 border-blue-600">
                            <i class="fas fa-history mr-2"></i>Riwayat Donasi
                        </a>
                    </nav>
                </div>

                <div class="p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-6">Riwayat Donasi</h2>

                    <!-- Donation Transactions Tab -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4"><i class="fas fa-receipt mr-2 text-blue-600"></i>Riwayat Donasi</h3>
                        
                        @if($donationTransactions->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Transaksi</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Donasi</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kampanye</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bukti Transfer</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($donationTransactions as $transaction)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $transaction->order_id }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $transaction->campaign ? $transaction->campaign->title : 'Donasi Umum' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                    {{ $transaction->status === 'VERIFIED' ? 'bg-green-100 text-green-800' :
                                                       ($transaction->status === 'PENDING' ? 'bg-yellow-100 text-yellow-800' :
                                                       'bg-red-100 text-red-800') }}">
                                                    {{ $transaction->status_label }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $transaction->created_at->format('d M Y H:i') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @if($transaction->proof_of_transfer_path)
                                                    <a href="{{ asset('storage/' . $transaction->proof_of_transfer_path) }}" target="_blank" class="text-blue-600 hover:text-blue-900 text-sm">
                                                        <i class="fas fa-image mr-1"></i>Lihat Bukti
                                                    </a>
                                                @else
                                                    <span class="text-gray-400 text-sm">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @if($transaction->status === 'AWAITING_TRANSFER' || $transaction->status === 'PENDING_VERIFICATION')
                                                    <!-- Upload proof form for transfers that are awaiting transfer or pending verification -->
                                                    @if($transaction->proof_of_transfer_path)
                                                        <!-- If proof already exists, show both proof and invoice -->
                                                        <div class="flex flex-col space-y-1">
                                                            <a href="{{ asset('storage/' . $transaction->proof_of_transfer_path) }}" target="_blank" class="text-blue-600 hover:text-blue-900 text-sm">
                                                                <i class="fas fa-image mr-1"></i>Lihat Bukti
                                                            </a>
                                                            <a href="{{ route('profiles.invoice', ['id' => $transaction->id]) }}" class="text-blue-600 hover:text-blue-900 text-sm">
                                                                <i class="fas fa-file-invoice mr-1"></i>Lihat Invoice
                                                            </a>
                                                        </div>
                                                    @else
                                                        <!-- If no proof exists, show upload form -->
                                                        <form action="{{ route('profiles.upload.proof', $transaction->order_id) }}" method="POST" enctype="multipart/form-data" class="inline">
                                                            @csrf
                                                            <div class="flex flex-col space-y-2">
                                                                <label class="text-blue-600 hover:text-blue-900 cursor-pointer text-sm">
                                                                    <i class="fas fa-upload mr-1"></i>Upload Bukti
                                                                    <input type="file" name="proof" accept="image/*" class="hidden" onchange="this.form.submit()" required>
                                                                </label>
                                                                <a href="{{ route('profiles.invoice', ['id' => $transaction->id]) }}" class="text-blue-600 hover:text-blue-900 text-sm">
                                                                    <i class="fas fa-file-invoice mr-1"></i>Lihat Invoice
                                                                </a>
                                                            </div>
                                                        </form>
                                                    @endif
                                                @else
                                                    <!-- For other statuses, show proof if exists, otherwise just invoice -->
                                                    <div class="flex flex-col space-y-1">
                                                        @if($transaction->proof_of_transfer_path)
                                                            <a href="{{ asset('storage/' . $transaction->proof_of_transfer_path) }}" target="_blank" class="text-blue-600 hover:text-blue-900 text-sm">
                                                                <i class="fas fa-image mr-1"></i>Lihat Bukti
                                                            </a>
                                                        @endif
                                                        <a href="{{ route('profiles.invoice', ['id' => $transaction->id]) }}" class="text-blue-600 hover:text-blue-900 text-sm">
                                                            <i class="fas fa-file-invoice mr-1"></i>Lihat Invoice
                                                        </a>
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-4">
                                {{ $donationTransactions->links() }}
                            </div>
                        @else
                            <div class="text-center py-8">
                                <i class="fas fa-receipt text-5xl text-gray-300 mb-4"></i>
                                <p class="text-gray-600">Belum ada riwayat donasi</p>
                            </div>
                        @endif
                    </div>

                    <!-- Legacy Donations Tab (if using the old donations table) -->
                    @if($donations->count() > 0)
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700 mb-4"><i class="fas fa-gift mr-2 text-green-600"></i>Donasi Lainnya</h3>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Transaksi</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Donasi</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kampanye</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bukti Transfer</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($donations as $donation)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $donation->order_id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Rp {{ number_format($donation->amount, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $donation->campaign ? $donation->campaign->title : 'Donasi Umum' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                {{ $donation->status === 'paid' ? 'bg-green-100 text-green-800' :
                                                   ($donation->status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                                   'bg-red-100 text-red-800') }}">
                                                {{ ucfirst($donation->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $donation->created_at->format('d M Y H:i') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="text-gray-400 text-sm">-</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <a href="{{ route('profiles.invoice', ['id' => $donation->id]) }}" class="text-blue-600 hover:text-blue-900">
                                                <i class="fas fa-file-invoice mr-1"></i>Lihat
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $donations->links() }}
                        </div>
                    </div>
                    @endif
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
</x-app>