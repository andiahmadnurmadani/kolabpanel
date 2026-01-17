@extends('admin.layouts.master')

@section('title', 'Verifikasi Donasi - API')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800 mb-2"><i class="fas fa-hand-holding-usd mr-3 text-blue-600"></i>Verifikasi Donasi API</h2>
        <p class="text-gray-600">Kelola dan verifikasi bukti transfer donasi dari para donatur</p>
    </div>
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

<div class="bg-white shadow rounded-lg mb-6 overflow-hidden">
    <div class="py-4 px-6 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-t-lg">
        <h5 class="m-0 font-bold text-lg text-white">Daftar Transaksi Menunggu Verifikasi</h5>
    </div>
    <div class="p-6">
        <div id="loading" class="text-center py-8 hidden">
            <i class="fas fa-spinner fa-spin text-3xl text-blue-600"></i>
            <p class="mt-2 text-gray-600">Memuat data...</p>
        </div>
        
        <div id="transactions-container">
            <!-- Data akan dimuat secara dinamis -->
        </div>
    </div>
</div>

<!-- Modal Detail Transaksi -->
<div id="transaction-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center pb-3 border-b">
                <h3 class="text-lg font-bold text-gray-800">Detail Transaksi</h3>
                <button id="close-modal" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="mt-4" id="modal-content">
                <!-- Konten modal akan dimuat secara dinamis -->
            </div>
            
            <div class="mt-6 flex justify-end space-x-3">
                <button id="reject-btn" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                    <i class="fas fa-times mr-1"></i> Tolak
                </button>
                <button id="verify-btn" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    <i class="fas fa-check mr-1"></i> Verifikasi
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

<script>
    // Fungsi untuk memuat data transaksi
    async function loadPendingVerifications() {
        const loadingElement = document.getElementById('loading');
        const container = document.getElementById('transactions-container');
        
        loadingElement.classList.remove('hidden');
        container.innerHTML = '';
        
        try {
            const response = await fetch(`{{ route('admin.api.donations.pending') }}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const result = await response.json();

            if (result.success) {
                if (result.data.length === 0) {
                    container.innerHTML = `
                        <div class="text-center py-12">
                            <i class="fas fa-check-circle text-5xl text-green-500 mb-4"></i>
                            <h3 class="text-xl font-semibold text-gray-800 mb-2">Tidak Ada Transaksi Menunggu Verifikasi</h3>
                            <p class="text-gray-600">Semua transaksi telah diverifikasi</p>
                        </div>
                    `;
                } else {
                    let html = '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">';
                    result.data.forEach(transaction => {
                        html += `
                            <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-bold text-gray-800">#${transaction.order_id}</h4>
                                        <p class="text-lg font-bold text-green-600">Rp ${formatRupiah(transaction.amount)}</p>
                                        <p class="text-sm text-gray-600">${transaction.donor_name}</p>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock mr-1"></i> Waiting
                                    </span>
                                </div>
                                ${transaction.campaign ? `<p class="mt-2 text-sm text-gray-600 truncate">${transaction.campaign.title}</p>` : ''}
                                <div class="mt-3 flex justify-end">
                                    <button onclick="showTransactionDetail('${transaction.order_id}')" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        <i class="fas fa-eye mr-1"></i> Lihat Detail
                                    </button>
                                </div>
                            </div>
                        `;
                    });
                    html += '</div>';
                    container.innerHTML = html;
                }
            } else {
                container.innerHTML = `
                    <div class="text-center py-8">
                        <i class="fas fa-exclamation-triangle text-5xl text-red-500 mb-4"></i>
                        <p class="text-red-600">Gagal memuat data: ${result.message || 'Terjadi kesalahan'}</p>
                    </div>
                `;
            }
        } catch (error) {
            console.error('Error:', error);
            container.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-exclamation-triangle text-5xl text-red-500 mb-4"></i>
                    <p class="text-red-600">Terjadi kesalahan saat memuat data</p>
                </div>
            `;
        } finally {
            loadingElement.classList.add('hidden');
        }
    }

    // Fungsi untuk menampilkan detail transaksi
    async function showTransactionDetail(orderId) {
        const modal = document.getElementById('transaction-modal');
        const modalContent = document.getElementById('modal-content');

        // Tampilkan loading
        modalContent.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-spinner fa-spin text-3xl text-blue-600"></i>
                <p class="mt-2 text-gray-600">Memuat detail transaksi...</p>
            </div>
        `;

        modal.classList.remove('hidden');

        try {
            const response = await fetch(`{{ route('admin.api.donations.get', ':orderId') }}`.replace(':orderId', orderId), {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (result.success) {
                const transaction = result.data;

                let proofSection = '';
                if (transaction.proof_url) {
                    proofSection = `
                        <div class="mb-6">
                            <h4 class="font-semibold text-gray-800 mb-3">Bukti Transfer</h4>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                                <a href="${transaction.proof_url}" target="_blank">
                                    <img src="${transaction.proof_url}"
                                         alt="Bukti Transfer"
                                         class="max-w-full h-auto rounded-lg mx-auto cursor-pointer hover:opacity-90 transition-opacity"
                                         style="max-height: 300px;">
                                </a>
                                <p class="mt-2 text-sm text-gray-600">Klik gambar untuk membuka dalam tab baru</p>
                            </div>
                        </div>
                    `;
                } else {
                    proofSection = `
                        <div class="mb-6">
                            <h4 class="font-semibold text-gray-800 mb-3">Bukti Transfer</h4>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
                                <i class="fas fa-file-image text-5xl text-gray-300 mb-3"></i>
                                <p class="text-gray-500">Belum ada bukti transfer</p>
                            </div>
                        </div>
                    `;
                }

                modalContent.innerHTML = `
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-3">Informasi Transaksi</h4>
                            <div class="space-y-2">
                                <p><span class="font-medium">Order ID:</span> ${transaction.order_id}</p>
                                <p><span class="font-medium">Jumlah Donasi:</span> Rp ${formatRupiah(transaction.amount)}</p>
                                <p><span class="font-medium">Status:</span>
                                    <span class="px-2 py-1 rounded-full text-xs font-medium
                                        ${transaction.status === 'VERIFIED' ? 'bg-green-100 text-green-800' :
                                          (transaction.status === 'PENDING_VERIFICATION' ? 'bg-yellow-100 text-yellow-800' :
                                          (transaction.status === 'AWAITING_TRANSFER' ? 'bg-blue-100 text-blue-800' :
                                          'bg-red-100 text-red-800'))}">
                                        ${transaction.status_label}
                                    </span>
                                </p>
                                <p><span class="font-medium">Tanggal:</span> ${new Date(transaction.created_at).toLocaleString('id-ID')}</p>
                                <p><span class="font-medium">Metode Pembayaran:</span> ${transaction.payment_method}</p>
                            </div>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-800 mb-3">Informasi Donatur</h4>
                            <div class="space-y-2">
                                <p><span class="font-medium">Nama:</span> ${transaction.donor_name}</p>
                                <p><span class="font-medium">Email:</span> ${transaction.donor_email}</p>
                                ${transaction.donor_phone ? `<p><span class="font-medium">Telepon:</span> ${transaction.donor_phone}</p>` : ''}
                            </div>

                            ${transaction.campaign ? `
                                <h4 class="font-semibold text-gray-800 mt-4 mb-3">Kampanye</h4>
                                <div class="bg-blue-50 p-3 rounded-lg">
                                    <p class="font-medium">${transaction.campaign.title}</p>
                                    <p class="text-sm text-gray-600 mt-1">${transaction.campaign.description.substring(0, 100)}...</p>
                                </div>
                            ` : ''}
                        </div>
                    </div>

                    ${proofSection}
                `;

                // Tambahkan event listener untuk tombol verifikasi dan tolak
                document.getElementById('verify-btn').onclick = () => updateStatus(orderId, 'VERIFIED');
                document.getElementById('reject-btn').onclick = () => updateStatus(orderId, 'CANCELLED');

                // Tambahkan tombol hapus bukti transfer jika ada bukti
                if (transaction.proof_url) {
                    const deleteProofBtn = document.createElement('button');
                    deleteProofBtn.id = 'delete-proof-btn';
                    deleteProofBtn.className = 'px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 mr-3';
                    deleteProofBtn.innerHTML = '<i class="fas fa-trash mr-1"></i> Hapus Bukti Transfer';
                    deleteProofBtn.onclick = () => deleteProof(orderId);

                    document.querySelector('.mt-6.flex.justify-end').prepend(deleteProofBtn);
                }
            } else {
                modalContent.innerHTML = `
                    <div class="text-center py-8">
                        <i class="fas fa-exclamation-triangle text-5xl text-red-500 mb-4"></i>
                        <p class="text-red-600">Gagal memuat detail: ${result.message || 'Terjadi kesalahan'}</p>
                    </div>
                `;
            }
        } catch (error) {
            console.error('Error:', error);
            modalContent.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-exclamation-triangle text-5xl text-red-500 mb-4"></i>
                    <p class="text-red-600">Terjadi kesalahan saat memuat detail transaksi</p>
                </div>
            `;
        }
    }

    // Fungsi untuk memperbarui status transaksi
    async function updateStatus(orderId, status) {
        if (!confirm(`Yakin ingin ${status === 'VERIFIED' ? 'memverifikasi' : 'menolak'} pembayaran ini?`)) {
            return;
        }

        try {
            const response = await fetch(`{{ route('admin.api.donations.verify', ':orderId') }}`.replace(':orderId', orderId), {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status: status })
            });

            const result = await response.json();

            if (result.success) {
                alert(result.message);
                document.getElementById('transaction-modal').classList.add('hidden');
                loadPendingVerifications(); // Refresh daftar
            } else {
                alert('Gagal memperbarui status: ' + (result.message || 'Terjadi kesalahan'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memperbarui status');
        }
    }

    // Fungsi untuk menghapus bukti transfer
    async function deleteProof(orderId) {
        if (!confirm('Yakin ingin menghapus bukti transfer ini? Tindakan ini tidak dapat dibatalkan.')) {
            return;
        }

        try {
            const response = await fetch(`{{ route('admin.api.donations.proof.delete', ':orderId') }}`.replace(':orderId', orderId), {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const result = await response.json();

            if (result.success) {
                alert(result.message);
                document.getElementById('transaction-modal').classList.add('hidden');
                loadPendingVerifications(); // Refresh daftar
            } else {
                alert('Gagal menghapus bukti transfer: ' + (result.message || 'Terjadi kesalahan'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghapus bukti transfer');
        }
    }

    // Fungsi format rupiah
    function formatRupiah(angka) {
        return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // Event listener untuk tombol tutup modal
    document.getElementById('close-modal').onclick = function() {
        document.getElementById('transaction-modal').classList.add('hidden');
    };

    // Event listener untuk klik di luar modal
    window.onclick = function(event) {
        const modal = document.getElementById('transaction-modal');
        if (event.target == modal) {
            modal.classList.add('hidden');
        }
    };

    // Load data saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        loadPendingVerifications();
    });
</script>