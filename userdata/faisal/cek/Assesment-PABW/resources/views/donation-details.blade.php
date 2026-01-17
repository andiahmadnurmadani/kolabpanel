<x-app title="{{ $campaign->title ?? 'Detail Donasi' }} - DonasiKita">
    
    @php
        // LOGIC PHP: Perhitungan Persentase & Hari
        $currentAmount = $campaign->current_amount ?? 0;
        $targetAmount = $campaign->target_amount ?? 1; 
        $percentage = min(100, ($currentAmount / $targetAmount) * 100);
        
        $endDate = isset($campaign->end_date) ? \Carbon\Carbon::parse($campaign->end_date) : null;
        $isExpired = $endDate ? $endDate->isPast() : false;
        
        // Menggunakan ceil() agar hari dibulatkan ke atas (misal 29.1 hari jadi 30 hari)
        $daysLeft = ($endDate && !$isExpired) ? ceil(\Carbon\Carbon::now()->floatDiffInDays($endDate)) : 0;
        
        $donaturCount = $donaturCount ?? 0; // Pastikan variabel ini ada dari controller
    @endphp

    <div class="relative h-[400px] lg:h-[500px] overflow-hidden group">
        <div class="absolute inset-0">
            <img src="{{ $campaign->image ?? 'https://placehold.co/1200x500/1e40af/FFFFFF?text=DonasiKita+Campaign' }}"
                 alt="{{ $campaign->title ?? 'Campaign' }}"
                 class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                 onerror="this.onerror=null; this.src='https://placehold.co/1200x500/1e40af/FFFFFF?text=Image+Not+Found';">
        </div>
        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/60 to-transparent"></div>

        <div class="absolute bottom-0 left-0 w-full p-6 md:p-12 z-20">
            <div class="max-w-7xl mx-auto">
                <nav class="flex mb-4 text-sm font-medium text-blue-200" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-2">
                        <li><a href="{{ route('home') }}" class="hover:text-white transition-colors">Beranda</a></li>
                        <li><span class="text-slate-400">/</span></li>
                        <li><a href="{{ route('campaigns.all') }}" class="hover:text-white transition-colors">Donasi</a></li>
                        <li><span class="text-slate-400">/</span></li>
                        <li class="text-white truncate max-w-[150px] md:max-w-xs">{{ $campaign->title ?? 'Detail' }}</li>
                    </ol>
                </nav>

                <h1 class="text-3xl md:text-5xl font-bold text-white mb-4 leading-tight max-w-4xl drop-shadow-lg">
                    {{ $campaign->title ?? 'Mari Bantu Sesama' }}
                </h1>
                
                <div class="flex items-center gap-4 text-white/90">
                    <span class="flex items-center gap-2 bg-white/10 backdrop-blur-md px-3 py-1.5 rounded-full border border-white/20 text-sm">
                        <i class="fas fa-tag text-blue-300"></i>
                        {{ $campaign->kategori ?? 'Sosial' }}
                    </span>
                    <span class="flex items-center gap-2 text-sm">
                        <i class="fas fa-user-circle text-blue-300"></i>
                        Oleh: <span class="font-semibold">{{ $campaign->user->name ?? 'DonasiKita Official' }}</span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 -mt-8 relative z-30">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12">
            
            <div class="lg:col-span-2 space-y-10">
                
                <div class="lg:hidden bg-white rounded-2xl shadow-xl p-6 border border-slate-100">
                    <div class="mb-6">
                        <div class="flex justify-between items-end mb-2">
                            <span class="text-2xl font-bold text-slate-800">Rp {{ number_format($currentAmount, 0, ',', '.') }}</span>
                        </div>
                        <div class="text-sm text-slate-500 mb-3">
                            terkumpul dari target <span class="font-semibold text-slate-700">Rp {{ number_format($targetAmount, 0, ',', '.') }}</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-3 mb-3">
                            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-3 rounded-full relative" style="width: {{ $percentage }}%"></div>
                        </div>
                        <div class="flex justify-between text-xs font-semibold">
                            <span class="text-blue-600">{{ number_format($percentage, 0) }}% Terlampaui</span>
                            <span class="text-slate-600">{{ number_format($daysLeft, 0) }} Hari Lagi</span>
                        </div>
                    </div>
                    @if(!$isExpired)
                    <a href="{{ $campaign ? route('donation.checkout', ['campaign' => $campaign->id]) : route('donation.checkout') }}" 
                       class="block w-full text-center py-3 rounded-xl bg-blue-600 text-white font-bold shadow-lg">
                        Donasi Sekarang
                    </a>
                    @endif
                </div>

                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="p-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="p-3 bg-blue-50 rounded-xl text-blue-600">
                                <i class="fas fa-align-left text-xl"></i>
                            </div>
                            <h2 class="text-2xl font-bold text-slate-800">Cerita Penggalangan Dana</h2>
                        </div>
                        
                        <div class="prose prose-lg prose-blue max-w-none text-slate-600 leading-relaxed">
                            @if($campaign)
                                {!! nl2br(e($campaign->description)) !!}
                            @else
                                <p>Program ini bertujuan untuk mengatasi kebutuhan mendesak akan bantuan kemanusiaan di komunitas yang kurang terlayani. Melalui donasi Anda yang murah hati, kami akan menyediakan persediaan penting, pendidikan, dan layanan kesehatan bagi keluarga yang membutuhkan.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2">
                        <i class="fas fa-hand-sparkles text-amber-500"></i> Area Dampak
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm text-center hover:shadow-md transition-all group">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3 text-blue-600 group-hover:scale-110 transition-transform">
                                <i class="fas fa-tint"></i>
                            </div>
                            <div class="font-bold text-slate-800 text-sm">Air Bersih</div>
                            <p class="text-xs text-slate-500 mt-1">Akses air layak minum</p>
                        </div>
                        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm text-center hover:shadow-md transition-all group">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3 text-green-600 group-hover:scale-110 transition-transform">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <div class="font-bold text-slate-800 text-sm">Pendidikan</div>
                            <p class="text-xs text-slate-500 mt-1">Dukungan belajar anak</p>
                        </div>
                        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm text-center hover:shadow-md transition-all group">
                            <div class="w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-3 text-amber-600 group-hover:scale-110 transition-transform">
                                <i class="fas fa-utensils"></i>
                            </div>
                            <div class="font-bold text-slate-800 text-sm">Pangan</div>
                            <p class="text-xs text-slate-500 mt-1">Kebutuhan pokok</p>
                        </div>
                        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm text-center hover:shadow-md transition-all group">
                            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3 text-red-600 group-hover:scale-110 transition-transform">
                                <i class="fas fa-medkit"></i>
                            </div>
                            <div class="font-bold text-slate-800 text-sm">Kesehatan</div>
                            <p class="text-xs text-slate-500 mt-1">Bantuan medis darurat</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
                    <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2">
                        <i class="fas fa-clipboard-list text-indigo-500"></i> Detail Program
                    </h3>
                    <div class="space-y-6">
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 mt-1">
                                <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                                    <i class="fas fa-bullseye"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800">Tujuan Kami</h4>
                                <p class="text-slate-600 text-sm leading-relaxed mt-1">Misi kami adalah menciptakan dampak berkelanjutan dengan mendukung komunitas lokal, membangun infrastruktur, dan memberdayakan individu dengan sumber daya yang mereka butuhkan untuk berkembang.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 mt-1">
                                <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center text-amber-600">
                                    <i class="fas fa-hand-holding-heart"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800">Penyaluran Dana</h4>
                                <p class="text-slate-600 text-sm leading-relaxed mt-1">
                                    100% donasi Anda (setelah biaya operasional platform) akan langsung digunakan untuk implementasi program.
                                    @if(isset($withdrawals) && $withdrawals->count() > 0)
                                        Sudah disalurkan: <span class="font-bold text-green-600">Rp {{ number_format($totalDistributed ?? 0, 0, ',', '.') }}</span>
                                        dari total <span class="font-bold">Rp {{ number_format($totalDonated ?? 0, 0, ',', '.') }}</span>
                                    @else
                                        DonasiKita memastikan transparansi dan akuntabilitas dalam semua upaya kami.
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
                    <h3 class="text-xl font-bold text-slate-800 mb-8 flex items-center gap-2">
                        <i class="fas fa-history text-blue-500"></i> Kabar Terbaru
                    </h3>

                    <!-- Fund Distribution Updates -->
                    <div class="mb-8">
                        <h4 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <i class="fas fa-money-bill-wave text-green-500"></i> Penyaluran Dana
                        </h4>

                        <!-- Fund Distribution Summary -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div class="bg-blue-50 p-4 rounded-xl border border-blue-100">
                                <div class="text-sm text-blue-600 font-semibold">Total Donasi</div>
                                <div class="text-lg font-bold text-slate-800">Rp {{ number_format($totalDonated ?? 0, 0, ',', '.') }}</div>
                            </div>
                            <div class="bg-green-50 p-4 rounded-xl border border-green-100">
                                <div class="text-sm text-green-600 font-semibold">Tersalurkan</div>
                                <div class="text-lg font-bold text-slate-800">Rp {{ number_format($totalDistributed ?? 0, 0, ',', '.') }}</div>
                            </div>
                            <div class="bg-amber-50 p-4 rounded-xl border border-amber-100">
                                <div class="text-sm text-amber-600 font-semibold">Tersisa</div>
                                <div class="text-lg font-bold text-slate-800">Rp {{ number_format($remainingFunds ?? 0, 0, ',', '.') }}</div>
                            </div>
                        </div>

                        <!-- Distribution Timeline -->
                        <div class="relative border-l-2 border-slate-100 ml-3 space-y-6 pl-8 pb-2">
                            @if(isset($withdrawals) && $withdrawals->count() > 0)
                                @foreach($withdrawals as $withdrawal)
                                <div class="relative group">
                                    <div class="absolute -left-[41px] bg-green-100 border-4 border-white w-6 h-6 rounded-full flex items-center justify-center group-hover:bg-green-600 transition-colors">
                                        <div class="w-2 h-2 bg-green-600 rounded-full group-hover:bg-white"></div>
                                    </div>
                                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-2">
                                            <h4 class="font-bold text-slate-800">Penyaluran Dana: {{ $withdrawal->bank_name ?? 'Transfer Bank' }}</h4>
                                            <span class="text-xs font-semibold text-slate-500 bg-white px-2 py-1 rounded border border-slate-200">
                                                {{ $withdrawal->transferred_at ? $withdrawal->transferred_at->format('d M Y') : $withdrawal->created_at->format('d M Y') }}
                                            </span>
                                        </div>
                                        <div class="text-sm text-green-600 font-bold mb-1">Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}</div>
                                        <p class="text-slate-600 text-sm">
                                            @if($withdrawal->admin_note)
                                                {{ $withdrawal->admin_note }}
                                            @else
                                                Dana sebesar Rp {{ number_format($withdrawal->amount, 0, ',', '.') }} telah disalurkan untuk kebutuhan {{ $campaign->title ?? 'program ini' }}.
                                            @endif
                                        </p>
                                        @if($withdrawal->status !== 'completed')
                                            <span class="inline-block mt-2 px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">
                                                Status: {{ ucfirst($withdrawal->status) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="bg-blue-50 border border-blue-100 rounded-xl p-6 text-center">
                                    <i class="fas fa-info-circle text-blue-500 text-2xl mb-3"></i>
                                    <p class="text-blue-700 font-medium">Belum ada penyaluran dana</p>
                                    <p class="text-blue-600 text-sm mt-1">Dana yang terkumpul akan disalurkan sesuai kebutuhan program</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <h3 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-comments text-blue-500"></i> Doa & Dukungan
                    </h3>
                    <div class="grid grid-cols-1 gap-4">
                        <div class="bg-gradient-to-r from-blue-50 to-cyan-50 p-6 rounded-2xl border border-blue-100">
                            <div class="flex gap-4">
                                <div class="w-12 h-12 rounded-full bg-blue-200 flex-shrink-0 flex items-center justify-center text-blue-700">
                                    <i class="fas fa-comment-dots"></i>
                                </div>
                                <div>
                                    <p class="text-slate-700 italic leading-relaxed">"Setiap donasi kecil membuat perbedaan besar. Terima kasih semuanya atas kemurahan hati Anda! Dukungan Anda telah memberi kami harapan."</p>
                                    <p class="text-sm font-bold text-slate-800 mt-2">— Maria Rodriguez, Tokoh Masyarakat</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-6 rounded-2xl border border-green-100">
                            <div class="flex gap-4">
                                <div class="w-12 h-12 rounded-full bg-green-200 flex-shrink-0 flex items-center justify-center text-green-700">
                                    <i class="fas fa-heart"></i>
                                </div>
                                <div>
                                    <p class="text-slate-700 italic leading-relaxed">"Kebaikan kalian memulihkan iman saya pada kemanusiaan. Tuhan memberkati semua donatur yang telah memungkinkan ini."</p>
                                    <p class="text-sm font-bold text-slate-800 mt-2">— James Wilson, Penerima Manfaat</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="hidden lg:block lg:col-span-1">
                <div class="sticky top-24 space-y-6">
                    
                    <div class="bg-white rounded-3xl shadow-xl shadow-blue-900/5 p-8 border border-slate-100 overflow-hidden relative">
                        <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-blue-500 to-indigo-600"></div>
                        
                        <div class="mb-8">
                            <div class="flex justify-between items-end mb-2">
                                <span class="text-3xl font-bold text-slate-800">Rp {{ number_format($currentAmount, 0, ',', '.') }}</span>
                            </div>
                            <div class="text-sm text-slate-500 mb-4">
                                terkumpul dari target <span class="font-semibold text-slate-700">Rp {{ number_format($targetAmount, 0, ',', '.') }}</span>
                            </div>
                            
                            <div class="w-full bg-slate-100 rounded-full h-3 mb-4">
                                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-3 rounded-full transition-all duration-1000 ease-out relative"
                                     style="width: {{ $percentage }}%">
                                </div>
                            </div>
                            
                            <div class="flex justify-between text-sm font-semibold">
                                <span class="text-blue-600">{{ number_format($percentage, 0) }}% Terlampaui</span>
                                @if($isExpired)
                                    <span class="text-red-500">Selesai</span>
                                @else
                                    <span class="text-slate-600">{{ number_format($daysLeft, 0) }} Hari Lagi</span>
                                @endif
                            </div>

                            <div class="mt-6 flex items-center justify-center gap-2 p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <i class="fas fa-users text-blue-500"></i>
                                <span class="text-slate-700 font-medium">{{ $donaturCount }} Orang telah berdonasi</span>
                            </div>
                        </div>

                        @if($isExpired)
                             <button disabled class="w-full py-4 rounded-xl bg-slate-200 text-slate-500 font-bold text-lg cursor-not-allowed mb-4">
                                Donasi Ditutup
                            </button>
                        @else
                            <a href="{{ $campaign ? route('donation.checkout', ['campaign' => $campaign->id]) : route('donation.checkout') }}" 
                               class="block w-full text-center py-4 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-700 hover:to-indigo-800 text-white font-bold text-lg shadow-lg shadow-blue-600/30 transform transition hover:-translate-y-1 mb-4">
                                Donasi Sekarang
                            </a>
                        @endif

                        <div class="text-center">
                            <button class="text-slate-500 hover:text-blue-600 text-sm font-semibold flex items-center justify-center gap-2 w-full transition-colors">
                                <i class="fas fa-share-alt"></i> Bagikan Kampanye
                            </button>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
                        <h4 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <img src="https://placehold.co/30x30/1e40af/ffffff?text=D" class="rounded-full w-6 h-6" alt="Logo">
                            Tentang DonasiKita
                        </h4>
                        <p class="text-slate-600 text-sm mb-4 leading-relaxed">
                            DonasiKita berkomitmen untuk menciptakan perubahan positif melalui pemberian amal yang transparan dan efektif.
                        </p>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3 text-sm text-slate-600">
                                <i class="fas fa-check-circle text-green-500 mt-1"></i>
                                <span>Terverifikasi & Aman</span>
                            </li>
                            <li class="flex items-start gap-3 text-sm text-slate-600">
                                <i class="fas fa-file-contract text-blue-500 mt-1"></i>
                                <span>Laporan Transparan</span>
                            </li>
                        </ul>
                        
                        @if($campaign->yayasan)
                        <div class="mt-4 pt-4 border-t border-slate-100">
                            <h4 class="font-bold text-slate-800 mb-3 flex items-center gap-2">
                                <img src="https://placehold.co/30x30/1e40af/ffffff?text=Y" class="rounded-full w-6 h-6" alt="Foundation">
                                Yayasan: {{ $campaign->yayasan }}
                            </h4>
                            <p class="text-slate-600 text-sm mb-3 leading-relaxed">
                                Kampanye ini dikelola oleh {{ $campaign->yayasan }}. Dana yang terkumpul akan disalurkan secara transparan untuk kebutuhan program.
                            </p>

                            <!-- Foundation Fund Distribution Summary -->
                            <div class="grid grid-cols-2 gap-3 mb-3">
                                <div class="bg-green-50 p-3 rounded-lg border border-green-100">
                                    <div class="text-xs text-green-600">Tersalurkan</div>
                                    <div class="text-sm font-bold text-slate-800">Rp {{ number_format($totalDistributed ?? 0, 0, ',', '.') }}</div>
                                </div>
                                <div class="bg-blue-50 p-3 rounded-lg border border-blue-100">
                                    <div class="text-xs text-blue-600">Tersisa</div>
                                    <div class="text-sm font-bold text-slate-800">Rp {{ number_format($remainingFunds ?? 0, 0, ',', '.') }}</div>
                                </div>
                            </div>

                            @if(isset($withdrawals) && $withdrawals->count() > 0)
                                <div class="text-xs text-slate-500">
                                    <i class="fas fa-check-circle text-green-500"></i>
                                    {{ $withdrawals->count() }} kali penyaluran telah dilakukan
                                </div>
                            @else
                                <div class="text-xs text-slate-500">
                                    <i class="fas fa-clock text-amber-500"></i>
                                    Menunggu penyaluran dana
                                </div>
                            @endif
                        </div>
                        @else
                        <div class="mt-4 pt-4 border-t border-slate-100 text-xs text-slate-500 space-y-1 text-center">
                             <div class="flex items-center justify-center gap-2">
                                <i class="fas fa-envelope text-blue-400"></i> contact@donasikita.org
                             </div>
                             <div class="flex items-center justify-center gap-2">
                                <i class="fas fa-phone text-blue-400"></i> +62 123 456 789
                             </div>
                        </div>
                        @endif
                    </div>
                    
                </div>
            </div>

        </div>
    </div>
</x-app>