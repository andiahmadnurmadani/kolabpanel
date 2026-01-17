<x-app title="DonasiKita - Wujudkan Harapan Bersama">

    {{-- Script Animasi Scroll (AOS) --}}
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    {{-- 1. HERO SECTION (Clean & Light Blue Theme) --}}
    <section
        class="relative min-h-[90vh] flex items-center pt-20 overflow-hidden bg-gradient-to-b from-blue-50 via-white to-white">
        {{-- Background Shapes (Hiasan Biru Muda) --}}
        <div
            class="absolute top-0 right-0 w-[600px] h-[600px] bg-blue-100 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob">
        </div>
        <div
            class="absolute top-0 left-0 w-[400px] h-[400px] bg-cyan-100 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob animation-delay-2000">
        </div>

        <div class="container max-w-7xl mx-auto px-6 relative z-10 flex flex-col md:flex-row items-center gap-12">
            {{-- Text Content --}}
            <div class="w-full md:w-1/2 space-y-6" data-aos="fade-right">
                <div
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-blue-100 shadow-sm text-blue-600 text-sm font-semibold">
                    <span class="relative flex h-3 w-3">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-500"></span>
                    </span>
                    Platform Kebaikan #1 di Indonesia
                </div>

                <h1 class="text-5xl md:text-6xl lg:text-7xl font-extrabold text-gray-900 leading-tight">
                    Sebar Kebaikan,<br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-cyan-500">Tuai
                        Senyuman.</span>
                </h1>

                <p class="text-lg text-gray-600 leading-relaxed max-w-lg">
                    Platform donasi transparan yang menghubungkan ribuan #OrangBaik dengan mereka yang membutuhkan
                    uluran tanganmu hari ini.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 pt-4">
                    <a href="#donasi"
                        class="px-8 py-4 rounded-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-lg shadow-lg hover:shadow-blue-500/30 transition-all transform hover:-translate-y-1 text-center">
                        Mulai Donasi
                    </a>
                    <a href="#cara-kerja"
                        class="px-8 py-4 rounded-full bg-white hover:bg-gray-50 text-blue-600 border border-blue-100 font-bold text-lg shadow-sm hover:shadow-md transition-all text-center flex items-center justify-center gap-2">
                        <i class="fas fa-play-circle"></i> Cara Kerja
                    </a>
                </div>

                {{-- Mini Social Proof --}}
                <div class="flex items-center gap-4 pt-6">
                    <div class="flex -space-x-3">
                        <img class="w-10 h-10 rounded-full border-2 border-white" src="https://i.pravatar.cc/100?img=1"
                            alt="User">
                        <img class="w-10 h-10 rounded-full border-2 border-white" src="https://i.pravatar.cc/100?img=2"
                            alt="User">
                        <img class="w-10 h-10 rounded-full border-2 border-white" src="https://i.pravatar.cc/100?img=3"
                            alt="User">
                        <div
                            class="w-10 h-10 rounded-full border-2 border-white bg-blue-50 flex items-center justify-center text-xs font-bold text-blue-600">
                            +2K</div>
                    </div>
                    <p class="text-sm text-gray-500"><strong>2.000+</strong> Donatur telah bergabung</p>
                </div>
            </div>

            {{-- Hero Image / Illustration --}}
            <div class="w-full md:w-1/2 relative" data-aos="fade-left">
                <div class="relative rounded-[3rem] overflow-hidden shadow-2xl border-4 border-white">
                    <img src="https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?q=80&w=2070&auto=format&fit=crop"
                        class="w-full h-auto object-cover transform hover:scale-105 transition duration-700"
                        alt="Hero">

                    {{-- Floating Card --}}
                    <div
                        class="absolute bottom-6 left-6 right-6 bg-white/90 backdrop-blur-md p-6 rounded-2xl shadow-lg border border-white/50">
                        <div class="flex justify-between items-end">
                            <div>
                                <p class="text-gray-500 text-sm mb-1">Total Donasi Tersalurkan</p>
                                <p class="text-3xl font-bold text-blue-600">Rp 12.5 Milyar+</p>
                            </div>
                            <div
                                class="h-10 w-10 bg-green-100 rounded-full flex items-center justify-center text-green-600">
                                <i class="fas fa-arrow-up transform rotate-45"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 2. STATISTIK (Clean White) --}}
    <section class="py-12 bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center divide-x divide-gray-100">
                <div data-aos="fade-up" data-aos-delay="0">
                    <p class="text-3xl md:text-4xl font-bold text-blue-600">150+</p>
                    <p class="text-gray-500 text-sm mt-1">Mitra Yayasan</p>
                </div>
                <div data-aos="fade-up" data-aos-delay="100">
                    <p class="text-3xl md:text-4xl font-bold text-blue-600">12K+</p>
                    <p class="text-gray-500 text-sm mt-1">Donasi Masuk</p>
                </div>
                <div data-aos="fade-up" data-aos-delay="200">
                    <p class="text-3xl md:text-4xl font-bold text-blue-600">100%</p>
                    <p class="text-gray-500 text-sm mt-1">Transparan</p>
                </div>
                <div data-aos="fade-up" data-aos-delay="300">
                    <p class="text-3xl md:text-4xl font-bold text-blue-600">24/7</p>
                    <p class="text-gray-500 text-sm mt-1">Layanan Support</p>
                </div>
            </div>
        </div>
    </section>

    {{-- 3. KATEGORI (Icon Bulat) --}}
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-10">
                <h3 class="text-2xl font-bold text-gray-800">Peduli Apa Hari Ini?</h3>
            </div>
            <div class="flex flex-wrap justify-center gap-8">
                {{-- Item Kategori --}}
                <a href="#" class="group flex flex-col items-center gap-3 w-24">
                    <div
                        class="w-16 h-16 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-2xl group-hover:bg-blue-600 group-hover:text-white transition-all duration-300 shadow-sm">
                        <i class="fas fa-stethoscope"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-600 group-hover:text-blue-600">Medis</span>
                </a>
                <a href="#" class="group flex flex-col items-center gap-3 w-24">
                    <div
                        class="w-16 h-16 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-2xl group-hover:bg-blue-600 group-hover:text-white transition-all duration-300 shadow-sm">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-600 group-hover:text-blue-600">Pendidikan</span>
                </a>
                <a href="#" class="group flex flex-col items-center gap-3 w-24">
                    <div
                        class="w-16 h-16 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-2xl group-hover:bg-blue-600 group-hover:text-white transition-all duration-300 shadow-sm">
                        <i class="fas fa-house-damage"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-600 group-hover:text-blue-600">Bencana</span>
                </a>
                <a href="#" class="group flex flex-col items-center gap-3 w-24">
                    <div
                        class="w-16 h-16 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-2xl group-hover:bg-blue-600 group-hover:text-white transition-all duration-300 shadow-sm">
                        <i class="fas fa-mosque"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-600 group-hover:text-blue-600">Rumah Ibadah</span>
                </a>
                <a href="#" class="group flex flex-col items-center gap-3 w-24">
                    <div
                        class="w-16 h-16 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-2xl group-hover:bg-blue-600 group-hover:text-white transition-all duration-300 shadow-sm">
                        <i class="fas fa-baby"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-600 group-hover:text-blue-600">Balita</span>
                </a>
            </div>
        </div>
    </section>

    {{-- 4. SPOTLIGHT / MENDESAK (Big Card) --}}
    <section class="py-16 bg-blue-50/50">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex items-center gap-2 mb-8">
                <div class="w-1 h-8 bg-red-500 rounded-full"></div>
                <h2 class="text-2xl font-bold text-gray-800">Kampanye Mendesak</h2>
            </div>

            @if($urgentCampaigns->count() > 0)
                @foreach($urgentCampaigns as $urgentCampaign)
                    <div
                        class="bg-white rounded-3xl shadow-xl overflow-hidden flex flex-col md:flex-row border border-blue-100 mb-6 last:mb-0">
                        <div class="md:w-1/2 h-64 md:h-auto relative">
                            @if($urgentCampaign->image && !filter_var($urgentCampaign->image, FILTER_VALIDATE_URL))
                                <img src="{{ asset('storage/' . ltrim($urgentCampaign->image, '/')) }}"
                                    class="w-full h-full object-cover" alt="{{ $urgentCampaign->title }}">
                            @else
                                <img src="{{ $urgentCampaign->image ?? 'https://placehold.co/600x400?text=Campaign+Image' }}"
                                    class="w-full h-full object-cover" alt="{{ $urgentCampaign->title }}">
                            @endif
                            <span
                                class="absolute top-4 left-4 bg-red-600 text-white text-xs font-bold px-3 py-1 rounded-full animate-pulse">
                                <i class="fas fa-clock mr-1"></i> Sisa <span class="days-remaining" data-end-date="{{ \Carbon\Carbon::parse($urgentCampaign->end_date)->format('Y-m-d') }}">{{ \Carbon\Carbon::parse($urgentCampaign->end_date)->diffInDays() > 0 ? \Carbon\Carbon::parse($urgentCampaign->end_date)->diffInDays() : 0 }}</span> Hari
                            </span>
                        </div>
                        <div class="md:w-1/2 p-8 md:p-12 flex flex-col justify-center">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="px-3 py-1 bg-blue-100 text-blue-600 text-xs font-bold rounded-full">{{ $urgentCampaign->kategori ?? 'Umum' }}</span>
                                <span class="text-gray-400 text-xs"><i class="fas fa-map-marker-alt"></i> Indonesia</span>
                            </div>
                            <h3 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">{{ $urgentCampaign->title }}</h3>
                            <p class="text-gray-600 mb-6 line-clamp-3">
                                {{ \Illuminate\Support\Str::limit($urgentCampaign->description, 150) }}
                            </p>

                            {{-- Progress --}}
                            <div class="mb-6">
                                <div class="flex justify-between text-sm font-semibold mb-2">
                                    <span class="text-blue-600">Rp {{ number_format($urgentCampaign->current_amount, 0, ',', '.') }}</span>
                                    <span class="text-gray-500">dari Rp {{ number_format($urgentCampaign->target_amount, 0, ',', '.') }}</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                                    <div class="bg-blue-600 h-3 rounded-full" style="width: {{ min(100, ($urgentCampaign->current_amount / $urgentCampaign->target_amount) * 100) }}%"></div>
                                </div>
                            </div>

                            <div class="flex gap-4">
                                <a href="{{ route('donations.details', ['slug' => $urgentCampaign->slug]) }}"
                                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl transition shadow-lg shadow-blue-200 text-center">
                                    Donasi Sekarang
                                </a>
                                <a href="{{ route('donations.details', ['slug' => $urgentCampaign->slug]) }}"
                                    class="px-4 py-3 border border-gray-200 rounded-xl hover:bg-gray-50 text-gray-600 transition">
                                    <i class="fas fa-share-alt"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    @if(!$loop->last)
                        <div class="border-t border-gray-100 my-6"></div>
                    @endif
                @endforeach
            @else
                <div class="bg-white rounded-3xl shadow-xl p-12 text-center border border-blue-100">
                    <i class="fas fa-exclamation-triangle text-5xl text-yellow-400 mb-4"></i>
                    <h3 class="text-2xl font-bold text-gray-700 mb-2">Tidak Ada Kampanye Mendesak</h3>
                    <p class="text-gray-500 mb-6">Saat ini tidak ada kampanye yang tergolong mendesak. Silakan cek kembali nanti.</p>
                    <a href="{{ route('campaigns.all') }}"
                        class="inline-block px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all">
                        Lihat Semua Kampanye
                    </a>
                </div>
            @endif
            </div>
        </div>
    </section>

    {{-- 5. DAFTAR DONASI (Card Baru & Rapih) --}}
    <section id="donasi" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800">Pilihan Kebaikan</h2>
                <p class="text-gray-500 mt-2">Pilih kampanye yang ingin kamu dukung hari ini.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @if (isset($campaigns) && !empty($campaigns))
                    @foreach ($campaigns->take(4) as $campaign)
                        {{-- CARD DESAIN BARU --}}
                        <div
                            class="group bg-white rounded-3xl border border-gray-100 shadow-[0_4px_20px_rgb(0,0,0,0.03)] hover:shadow-[0_4px_25px_rgb(0,0,0,0.1)] overflow-hidden transition-all duration-300 hover:-translate-y-1 h-full flex flex-col">

                            {{-- Image Area --}}
                            <div class="relative h-48 overflow-hidden">
                                @if ($campaign->image && !filter_var($campaign->image, FILTER_VALIDATE_URL))
                                    <img src="{{ asset('storage/' . ltrim($campaign->image, '/')) }}"
                                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                                        alt="{{ $campaign->title }}">
                                @else
                                    <img src="{{ $campaign->image ?? 'https://placehold.co/600x400?text=Campaign+Image' }}"
                                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                                        alt="{{ $campaign->title }}">
                                @endif

                                {{-- Badge --}}
                                <div
                                    class="absolute top-3 right-3 bg-white/90 backdrop-blur px-3 py-1 rounded-full text-xs font-bold text-blue-600 shadow-sm">
                                    {{ $campaign->status }}
                                </div>
                            </div>

                            {{-- Content Area --}}
                            <div class="p-6 flex flex-col flex-grow">
                                {{-- Category (Now dynamic based on campaign data) --}}
                                <div class="text-xs font-semibold text-gray-400 mb-2 uppercase tracking-wide">
                                    {{ $campaign->kategori ?? 'Umum' }}
                                </div>

                                <h3
                                    class="font-bold text-lg text-gray-900 mb-3 line-clamp-2 hover:text-blue-600 transition-colors">
                                    <a href="{{ route('donations.details', ['slug' => $campaign->slug]) }}">
                                        {{ $campaign->title }}
                                    </a>
                                </h3>

                                {{-- Progress Bar --}}
                                <div class="mt-auto">
                                    <div class="w-full bg-blue-50 rounded-full h-2 mb-2 overflow-hidden">
                                        <div class="bg-blue-500 h-2 rounded-full"
                                            style="width: {{ $campaign->target_amount > 0 ? min(100, ($campaign->current_amount / $campaign->target_amount) * 100) : 0 }}%">
                                        </div>
                                    </div>
                                    <div class="flex justify-between items-center text-sm">
                                        <div>
                                            <p class="text-gray-400 text-xs">Terkumpul</p>
                                            <p class="font-bold text-blue-600">Rp
                                                {{ number_format($campaign->current_amount, 0, ',', '.') }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-gray-400 text-xs">Sisa Hari</p>
                                            <p class="font-bold text-gray-700">
                                                <span class="days-remaining" data-end-date="{{ \Carbon\Carbon::parse($campaign->end_date)->format('Y-m-d') }}">
                                                    {{ \Carbon\Carbon::parse($campaign->end_date)->diffInDays() > 0 ? \Carbon\Carbon::parse($campaign->end_date)->diffInDays() : 0 }}
                                                </span>
                                                <span class="text-xs font-normal">Hari</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Footer Button --}}
                            <div class="p-4 border-t border-gray-50">
                                <a href="{{ route('donations.details', ['slug' => $campaign->slug]) }}"
                                    class="block w-full text-center py-2 rounded-xl text-blue-600 font-bold border border-blue-100 hover:bg-blue-600 hover:text-white transition-all duration-300">
                                    Donasi Sekarang
                                </a>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div
                        class="col-span-full text-center py-12 bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                        <p class="text-gray-500">Belum ada kampanye aktif.</p>
                    </div>
                @endif
            </div>

            <div class="text-center mt-12">
                <a href="{{ route('campaigns.all') }}"
                    class="inline-flex items-center px-6 py-3 rounded-full bg-gray-100 text-gray-600 font-semibold hover:bg-gray-200 transition-colors">
                    Lihat Semua <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </section>

    {{-- 6. CARA KERJA (Step by Step) --}}
    <section id="cara-kerja" class="py-20 bg-blue-50">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-800">Semudah Ini Berbagi</h2>
                <p class="text-gray-500 mt-2">Kebaikan tidak perlu rumit. Cukup 3 langkah mudah.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative">
                {{-- Garis Penghubung (Desktop) --}}
                <div
                    class="hidden md:block absolute top-10 left-[16%] right-[16%] h-0.5 bg-blue-200 border-t-2 border-dashed border-blue-300 -z-0">
                </div>

                {{-- Step 1 --}}
                <div class="relative z-10 flex flex-col items-center text-center">
                    <div
                        class="w-20 h-20 bg-white rounded-full shadow-lg flex items-center justify-center text-3xl text-blue-600 mb-6 border-4 border-blue-50">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h4 class="text-xl font-bold text-gray-800 mb-2">1. Pilih Campaign</h4>
                    <p class="text-gray-500 text-sm px-4">Pilih cerita yang menggerakkan hatimu dari daftar kampanye.
                    </p>
                </div>

                {{-- Step 2 --}}
                <div class="relative z-10 flex flex-col items-center text-center">
                    <div
                        class="w-20 h-20 bg-white rounded-full shadow-lg flex items-center justify-center text-3xl text-blue-600 mb-6 border-4 border-blue-50">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <h4 class="text-xl font-bold text-gray-800 mb-2">2. Transfer Donasi</h4>
                    <p class="text-gray-500 text-sm px-4">Transfer via Bank, E-Wallet, atau QRIS dengan aman.</p>
                </div>

                {{-- Step 3 --}}
                <div class="relative z-10 flex flex-col items-center text-center">
                    <div
                        class="w-20 h-20 bg-white rounded-full shadow-lg flex items-center justify-center text-3xl text-blue-600 mb-6 border-4 border-blue-50">
                        <i class="fas fa-smile"></i>
                    </div>
                    <h4 class="text-xl font-bold text-gray-800 mb-2">3. Terima Laporan</h4>
                    <p class="text-gray-500 text-sm px-4">Dapatkan update penggunaan dana secara transparan via email.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- 7. TESTIMONI (Social Proof) --}}
    <section class="py-20 bg-white">
        <div class="max-w-6xl mx-auto px-6">
            <h2 class="text-3xl font-bold text-gray-800 text-center mb-12">Kata #OrangBaik</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-gray-50 p-8 rounded-3xl relative">
                    <i class="fas fa-quote-left text-4xl text-blue-100 absolute top-6 left-6"></i>
                    <p class="text-gray-600 relative z-10 italic mb-6">"Platformnya sangat transparan. Saya jadi tidak
                        ragu untuk menyisihkan rezeki setiap bulan di sini."</p>
                    <div class="flex items-center gap-4">
                        <img src="https://i.pravatar.cc/150?img=60" class="w-12 h-12 rounded-full" alt="User">
                        <div>
                            <p class="font-bold text-gray-900">Rina Suryani</p>
                            <p class="text-xs text-gray-500">Donatur Rutin</p>
                        </div>
                    </div>
                </div>

                <div class="bg-blue-600 p-8 rounded-3xl relative text-white shadow-xl transform md:-translate-y-4">
                    <i class="fas fa-quote-left text-4xl text-blue-400 absolute top-6 left-6"></i>
                    <p class="relative z-10 italic mb-6">"Proses donasinya cepat banget, cuma hitungan detik. Semoga
                        DonasiKita terus amanah menyalurkan bantuan."</p>
                    <div class="flex items-center gap-4">
                        <img src="https://i.pravatar.cc/150?img=12"
                            class="w-12 h-12 rounded-full border-2 border-white" alt="User">
                        <div>
                            <p class="font-bold">Dimas Anggara</p>
                            <p class="text-xs text-blue-200">Karyawan Swasta</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 p-8 rounded-3xl relative">
                    <i class="fas fa-quote-left text-4xl text-blue-100 absolute top-6 left-6"></i>
                    <p class="text-gray-600 relative z-10 italic mb-6">"Senang bisa melihat laporan update secara
                        berkala. Jadi tau uangnya beneran dipake beli sembako."</p>
                    <div class="flex items-center gap-4">
                        <img src="https://i.pravatar.cc/150?img=44" class="w-12 h-12 rounded-full" alt="User">
                        <div>
                            <p class="font-bold text-gray-900">Siti Aminah</p>
                            <p class="text-xs text-gray-500">Ibu Rumah Tangga</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 8. VOLUNTEER CTA (Light Blue Version) --}}
    <section class="py-20 relative overflow-hidden">
        {{-- Background Image with Light Overlay --}}
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1559027615-cd4628902d4a?q=80&w=2074&auto=format&fit=crop"
                class="w-full h-full object-cover" alt="Volunteer">
            <div class="absolute inset-0 bg-blue-900/80 mix-blend-multiply"></div>
        </div>

        <div class="container max-w-5xl mx-auto px-6 relative z-10 text-center text-white">
            <span
                class="inline-block py-1 px-3 rounded-full bg-white/20 border border-white/30 text-xs font-bold uppercase tracking-wider mb-4">
                Komunitas Relawan
            </span>
            <h2 class="text-4xl md:text-5xl font-extrabold mb-6">Punya Waktu Luang?</h2>
            <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
                Bergabunglah menjadi relawan dan rasakan pengalaman turun langsung ke lapangan. Tenagamu sangat berarti
                bagi mereka.
            </p>
            <a href="{{ route('volunteer.campaigns.index') }}"
                class="px-8 py-4 bg-white text-blue-900 font-bold rounded-full shadow-lg hover:bg-blue-50 transition transform hover:scale-105">
                Daftar Relawan
                </a>
        </div>
    </section>

    {{-- 9. FAQ (Clean Accordion) --}}
    <section class="py-20 bg-white">
        <div class="max-w-3xl mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800">Pertanyaan Umum</h2>
            </div>

            <div class="space-y-4">
                <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden">
                    <details class="group p-6 cursor-pointer">
                        <summary class="flex justify-between items-center font-bold text-gray-800 list-none">
                            Apakah donasi saya aman?
                            <span class="transition group-open:rotate-180 text-blue-600"><i
                                    class="fas fa-chevron-down"></i></span>
                        </summary>
                        <p class="text-gray-600 mt-4 leading-relaxed pl-4 border-l-2 border-blue-500">
                            Sangat aman. Kami menggunakan payment gateway terverifikasi dan setiap transaksi tercatat
                            dalam sistem yang transparan.
                        </p>
                    </details>
                </div>
                <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden">
                    <details class="group p-6 cursor-pointer">
                        <summary class="flex justify-between items-center font-bold text-gray-800 list-none">
                            Apa saja metode pembayarannya?
                            <span class="transition group-open:rotate-180 text-blue-600"><i
                                    class="fas fa-chevron-down"></i></span>
                        </summary>
                        <p class="text-gray-600 mt-4 leading-relaxed pl-4 border-l-2 border-blue-500">
                            Kami menerima Transfer Bank (BCA, Mandiri, BRI, BNI), E-Wallet (GoPay, OVO, Dana), dan QRIS.
                        </p>
                    </details>
                </div>
            </div>
        </div>
    </section>

    {{-- CSS Kustom --}}
    <style>
        .animate-blob {
            animation: blob 7s infinite;
        }

        .animation-delay-2000 {
            animation-delay: 2s;
        }

        @keyframes blob {
            0% {
                transform: translate(0px, 0px) scale(1);
            }

            33% {
                transform: translate(30px, -50px) scale(1.1);
            }

            66% {
                transform: translate(-20px, 20px) scale(0.9);
            }

            100% {
                transform: translate(0px, 0px) scale(1);
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init({
                once: true,
                duration: 800
            });
        });
    </script>

</x-app>
