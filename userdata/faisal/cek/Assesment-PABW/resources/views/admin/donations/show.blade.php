@extends('admin.layouts.master')

@section('title', 'Detail Donasi')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">
                <i class="fas fa-receipt mr-3 text-blue-600"></i>Detail Donasi
            </h2>
            <p class="text-gray-600">Informasi lengkap tentang transaksi donasi</p>
        </div>
        <a href="{{ route('admin.donations.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>

    @if (session('success'))
        <div class="alert mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl">
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Donation Details Card -->
        <div class="lg:col-span-2 bg-white shadow rounded-lg overflow-hidden">
            <div class="py-4 px-6 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-t-lg">
                <h5 class="m-0 font-bold text-lg text-white">Detail Transaksi</h5>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <h6 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Order ID</h6>
                        <p class="mt-1 text-lg font-bold text-gray-900">{{ $transaction->order_id }}</p>
                    </div>
                    <div>
                        <h6 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Status</h6>
                        <div class="mt-1">
                            @if($transaction->status === 'VERIFIED')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i> Paid
                                </span>
                            @elseif($transaction->status === 'PENDING_VERIFICATION')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i> Waiting
                                </span>
                            @elseif($transaction->status === 'AWAITING_TRANSFER')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-hourglass-half mr-1"></i> Pending
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i> Rejected
                                </span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <h6 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Jumlah Donasi</h6>
                        <p class="mt-1 text-lg font-bold text-gray-900">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <h6 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Metode Pembayaran</h6>
                        <p class="mt-1 text-gray-900">
                            @if($transaction->payment_method == 'bank_transfer')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-university mr-1"></i> Bank Transfer
                                </span>
                            @elseif($transaction->payment_method == 'e_wallet')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-wallet mr-1"></i> e-Wallet
                                </span>
                            @elseif($transaction->payment_method == 'qris')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    <i class="fas fa-qrcode mr-1"></i> QRIS
                                </span>
                            @elseif($transaction->payment_method == 'midtrans')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    <i class="fas fa-credit-card mr-1"></i> Bank Transfer (Lama)
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <i class="fas fa-credit-card mr-1"></i> {{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}
                                </span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <h6 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Tanggal</h6>
                        <p class="mt-1 text-gray-900">{{ $transaction->created_at->format('d M Y H:i') }}</p>
                    </div>
                    @if($transaction->transfer_deadline)
                    <div>
                        <h6 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Batas Transfer</h6>
                        <p class="mt-1 text-gray-900">{{ $transaction->transfer_deadline->format('d M Y H:i') }}</p>
                    </div>
                    @endif
                    @if($transaction->payment_method_data)
                        @php
                            $paymentData = json_decode($transaction->payment_method_data, true);
                        @endphp
                        @if($paymentData && isset($paymentData['selected_bank']))
                        <div>
                            <h6 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Bank Tujuan</h6>
                            <p class="mt-1 text-gray-900">{{ $paymentData['selected_bank'] }}</p>
                        </div>
                        @elseif($paymentData && isset($paymentData['selected_ewallet']))
                        <div>
                            <h6 class="text-sm font-medium text-gray-500 uppercase tracking-wider">E-Wallet</h6>
                            <p class="mt-1 text-gray-900">{{ $paymentData['selected_ewallet'] }}</p>
                        </div>
                        @elseif($paymentData && isset($paymentData['selected_qris']))
                        <div>
                            <h6 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Aplikasi QRIS</h6>
                            <p class="mt-1 text-gray-900">{{ $paymentData['selected_qris'] }}</p>
                        </div>
                        @endif
                    @endif
                </div>

                <!-- Donor Information -->
                <div class="mb-6">
                    <h5 class="text-lg font-semibold text-gray-800 mb-4">Informasi Donatur</h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h6 class="text-sm font-medium text-gray-500">Nama</h6>
                            <p class="mt-1 text-gray-900">{{ $transaction->donor_name }}</p>
                        </div>
                        <div>
                            <h6 class="text-sm font-medium text-gray-500">Email</h6>
                            <p class="mt-1 text-gray-900">{{ $transaction->donor_email }}</p>
                        </div>
                        @if($transaction->donor_phone)
                        <div>
                            <h6 class="text-sm font-medium text-gray-500">Telepon</h6>
                            <p class="mt-1 text-gray-900">{{ $transaction->donor_phone }}</p>
                        </div>
                        @endif
                        <div>
                            <h6 class="text-sm font-medium text-gray-500">Status</h6>
                            <p class="mt-1 text-gray-900">
                                @if($transaction->anonymous)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        <i class="fas fa-user-secret mr-1"></i> Anonim
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-user mr-1"></i> Terlihat
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Campaign Information -->
                @if($transaction->campaign)
                <div class="mb-6">
                    <h5 class="text-lg font-semibold text-gray-800 mb-4">Informasi Kampanye</h5>
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <p class="font-medium text-gray-800">{{ $transaction->campaign->title }}</p>
                        <p class="text-gray-600 text-sm mt-1">{{ Str::limit($transaction->campaign->description, 150) }}</p>
                        <a href="{{ route('admin.campaigns.show', $transaction->campaign->id) }}" class="mt-2 inline-flex items-center text-blue-600 hover:text-blue-800 text-sm">
                            <i class="fas fa-external-link-alt mr-1"></i> Lihat Kampanye
                        </a>
                    </div>
                </div>
                @endif

                <!-- Bank Information (for manual transfers) -->
                @if(in_array($transaction->payment_method, ['bank_transfer', 'manual_transfer']))
                <div class="mb-6">
                    <h5 class="text-lg font-semibold text-gray-800 mb-4">Informasi Transfer</h5>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <h6 class="text-sm font-medium text-gray-500">Nama Akun</h6>
                            <p class="mt-1 text-gray-900">{{ $transaction->bank_account_name }}</p>
                        </div>
                        <div>
                            <h6 class="text-sm font-medium text-gray-500">Nomor Rekening</h6>
                            <p class="mt-1 text-gray-900 font-mono">{{ $transaction->bank_account_number }}</p>
                        </div>
                        <div>
                            <h6 class="text-sm font-medium text-gray-500">Nama Bank</h6>
                            <p class="mt-1 text-gray-900">{{ $transaction->bank_name }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Proof of Transfer (if exists) -->
                @if($transaction->proof_of_transfer_path)
                <div class="mb-6">
                    <h5 class="text-lg font-semibold text-gray-800 mb-4">Bukti Transfer</h5>
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div class="flex justify-center">
                            <img src="{{ $transaction->proof_of_transfer_path }}"
                                 alt="Bukti Transfer"
                                 class="max-w-full max-h-96 rounded-lg border shadow-sm"
                                 onerror="this.onerror=null; this.src='https://placehold.co/600x400?text=Gambar+Tidak+Dapat+Dimuat';">
                        </div>
                        <div class="mt-4 text-center">
                            <a href="{{ $transaction->proof_of_transfer_path }}"
                               target="_blank"
                               class="inline-flex items-center px-4 py-2 border border-blue-300 text-sm font-medium rounded-md shadow-sm text-blue-700 bg-blue-50 hover:bg-blue-100">
                                <i class="fas fa-external-link-alt mr-2"></i> Lihat Gambar Penuh
                            </a>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Proof and Actions Card -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="py-4 px-6 bg-gradient-to-r from-gray-700 to-gray-800 text-white rounded-t-lg">
                <h5 class="m-0 font-bold text-lg text-white">Verifikasi</h5>
            </div>
            <div class="p-6">

                <!-- Action Buttons -->
                <div class="space-y-3">
                    <a href="{{ route('admin.donations.invoice', $transaction->order_id) }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-blue-300 text-sm font-medium rounded-md shadow-sm text-blue-700 bg-blue-50 hover:bg-blue-100">
                        <i class="fas fa-file-invoice mr-2"></i> Lihat Invoice
                    </a>

                    @if($transaction->status === 'PENDING_VERIFICATION')
                    <div class="space-y-3">
                        <form action="{{ route('admin.donations.updateStatus', $transaction->order_id) }}" method="POST" class="inline-block w-full">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="VERIFIED">
                            <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700" onclick="return confirm('Yakin ingin memverifikasi pembayaran ini?')">
                                <i class="fas fa-check mr-2"></i> Verifikasi Donasi
                            </button>
                        </form>

                        <form action="{{ route('admin.donations.updateStatus', $transaction->order_id) }}" method="POST" class="inline-block w-full">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="CANCELLED">
                            <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700" onclick="return confirm('Yakin ingin menolak pembayaran ini?')">
                                <i class="fas fa-times mr-2"></i> Tolak Donasi
                            </button>
                        </form>
                    </div>
                    @else
                    <div class="space-y-3">
                        <p class="text-sm text-gray-600 mb-3">Status saat ini: <strong>{{ $transaction->status_label }}</strong></p>

                        <!-- Status change dropdown -->
                        <form action="{{ route('admin.donations.updateStatus', $transaction->order_id) }}" method="POST" class="space-y-3">
                            @csrf
                            @method('PUT')
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Ubah Status</label>
                                <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="AWAITING_TRANSFER" {{ $transaction->status === 'AWAITING_TRANSFER' ? 'selected' : '' }}>Pending</option>
                                    <option value="PENDING_VERIFICATION" {{ $transaction->status === 'PENDING_VERIFICATION' ? 'selected' : '' }}>Waiting</option>
                                    <option value="VERIFIED" {{ $transaction->status === 'VERIFIED' ? 'selected' : '' }}>Paid</option>
                                    <option value="CANCELLED" {{ $transaction->status === 'CANCELLED' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>
                            <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                                <i class="fas fa-sync mr-2"></i> Update Status
                            </button>
                        </form>
                    </div>
                    @endif

                    <form action="{{ route('admin.donations.destroy', $transaction->order_id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50" onclick="return confirm('Yakin ingin menghapus transaksi ini? Data yang dihapus tidak dapat dikembalikan.')">
                            <i class="fas fa-trash mr-2 text-red-600"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Tambahkan event listener untuk form update status
    document.addEventListener('DOMContentLoaded', function() {
        const updateStatusForm = document.querySelector('form[action*="updateStatus"]');
        if (updateStatusForm) {
            updateStatusForm.addEventListener('submit', function(e) {
                // Tidak perlu mencegah default submit karena ini adalah form biasa
                // Tapi kita bisa tambahkan loading indicator jika perlu
                const submitButton = updateStatusForm.querySelector('button[type="submit"]');
                if (submitButton) {
                    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...';
                    submitButton.disabled = true;

                    // Kembalikan setelah submit
                    setTimeout(() => {
                        submitButton.disabled = false;
                    }, 3000);
                }
            });
        }

        // Pastikan CSRF token tetap valid
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            // Update CSRF token di setiap form jika diperlukan
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                let hiddenInput = form.querySelector('input[name="_token"]');
                if (!hiddenInput) {
                    hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = '_token';
                    hiddenInput.value = csrfToken.getAttribute('content');
                    form.appendChild(hiddenInput);
                } else {
                    hiddenInput.value = csrfToken.getAttribute('content');
                }
            });
        }
    });
</script>
@endpush