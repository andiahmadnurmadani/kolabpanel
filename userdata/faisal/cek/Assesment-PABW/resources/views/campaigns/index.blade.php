<x-app title="Daftar Kampanye Donasi - DonasiKita">
    <div class="relative bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-900 overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <svg class="h-full w-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                <path d="M0 100 C 20 0 50 0 100 100 Z" fill="white" />
                <circle cx="90" cy="10" r="40" fill="white" opacity="0.5" />
            </svg>
        </div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">
            <span class="inline-block py-1 px-3 rounded-full bg-white/20 text-white text-sm font-semibold mb-6 border border-white/30 backdrop-blur-sm">
                Mari Berbagi Kebaikan
            </span>
            <h1 class="text-4xl md:text-6xl font-bold text-white tracking-tight mb-6 drop-shadow-md">
                Wujudkan Harapan, <br class="hidden md:block" />
                <span class="text-blue-200">Satu Donasi Sekaligus</span>
            </h1>
            <p class="text-lg text-blue-100 max-w-2xl mx-auto mb-10 font-medium">
                Platform terpercaya untuk menyalurkan bantuan Anda kepada mereka yang paling membutuhkan secara transparan.
            </p>
        </div>
    </div>

    <div class="relative -mt-8 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 z-20">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-3">
            <form method="GET" action="{{ route('campaigns.all') }}" id="searchFilterForm" class="flex flex-col md:flex-row gap-3">
                
                <div class="flex-1 relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-search text-slate-400 group-focus-within:text-blue-600 transition-colors"></i>
                    </div>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           class="block w-full pl-11 pr-4 py-3 bg-slate-50 border-0 rounded-xl text-slate-900 placeholder:text-slate-400 focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all font-medium" 
                           placeholder="Cari kampanye (mis: Bencana, Pendidikan)...">
                </div>

                <div class="hidden md:block w-px bg-slate-200 my-2"></div>

                <div class="md:w-1/4 relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-filter text-slate-400"></i>
                    </div>
                    <select name="kategori" 
                            onchange="document.getElementById('searchFilterForm').submit();"
                            class="block w-full pl-11 pr-10 py-3 bg-slate-50 border-0 rounded-xl text-slate-700 font-medium focus:ring-2 focus:ring-blue-500 focus:bg-white cursor-pointer appearance-none transition-all">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoriOptions as $kategori)
                            <option value="{{ $kategori }}" {{ request('kategori') == $kategori ? 'selected' : '' }}>
                                {{ $kategori }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                        <i class="fas fa-chevron-down text-slate-400 text-xs"></i>
                    </div>
                </div>

                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-xl font-semibold transition-all shadow-lg shadow-blue-600/30 hover:shadow-blue-600/50 flex items-center justify-center gap-2">
                    <span>Cari</span>
                </button>

                @if(request('kategori') || request('search'))
                    <a href="{{ route('campaigns.all') }}" 
                       class="bg-red-50 hover:bg-red-100 text-red-600 px-4 py-3 rounded-xl transition-colors flex items-center justify-center border border-red-200"
                       title="Reset Filter">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </form>
        </div>
    </div>

    <section class="py-16 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($campaigns as $campaign)
                    @php
                        // --- FIX LOGIKA MATEMATIKA ---
                        
                        // 1. Hitung Persentase (Max 100%)
                        $target = $campaign->target_amount > 0 ? $campaign->target_amount : 1;
                        $percentage = min(100, ($campaign->current_amount / $target) * 100);
                        
                        // 2. Setup Tanggal
                        $endDate = \Carbon\Carbon::parse($campaign->end_date);
                        $now = \Carbon\Carbon::now();
                        
                        // 3. Cek Kadaluarsa
                        $isExpired = $endDate->isPast();
                        
                        // 4. Hitung Sisa Hari (Bulatkan ke atas agar tidak ada desimal)
                        // floatDiffInDays mengambil presisi, ceil membulatkannya ke atas.
                        // Contoh: 29.1 hari -> 30 Hari.
                        $daysLeft = $isExpired ? 0 : ceil($now->floatDiffInDays($endDate));
                    @endphp

                    <div class="group bg-white rounded-2xl border border-slate-100 overflow-hidden hover:shadow-2xl hover:shadow-blue-900/5 transition-all duration-300 flex flex-col h-full">
                        <div class="relative h-56 overflow-hidden">
                            <div class="absolute top-4 left-4 z-10">
                                <span class="bg-white/95 backdrop-blur-sm text-slate-800 text-xs font-bold px-3 py-1.5 rounded-full shadow-sm border border-slate-100 flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                    {{ $campaign->kategori ?? 'Umum' }}
                                </span>
                            </div>
                            
                            <a href="{{ route('donations.details', ['slug' => $campaign->slug]) }}" class="block h-full w-full">
                                <img src="{{ $campaign->image ?? 'https://placehold.co/600x400?text=DonasiKita' }}" 
                                     alt="{{ $campaign->title }}"
                                     class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700 ease-out">
                            </a>
                        </div>

                        <div class="p-6 flex-1 flex flex-col">
                            <h3 class="text-lg font-bold text-slate-900 mb-3 line-clamp-2 leading-snug group-hover:text-blue-600 transition-colors">
                                <a href="{{ route('donations.details', ['slug' => $campaign->slug]) }}">
                                    {{ $campaign->title }}
                                </a>
                            </h3>
                            
                            <p class="text-slate-500 text-sm mb-6 line-clamp-2 flex-1">
                                {{ \Illuminate\Support\Str::limit($campaign->description, 90) }}
                            </p>

                            <div class="space-y-4 mt-auto">
                                <div class="relative pt-1">
                                    <div class="flex mb-2 items-center justify-between">
                                        <div>
                                            <span class="text-xs font-semibold inline-block text-blue-600">
                                                Terkumpul
                                            </span>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-xs font-bold inline-block text-slate-700">
                                                {{ number_format($percentage, 0) }}%
                                            </span>
                                        </div>
                                    </div>
                                    <div class="overflow-hidden h-2 mb-2 text-xs flex rounded-full bg-slate-100">
                                        <div style="width:{{ $percentage }}%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-500 transition-all duration-1000 ease-out rounded-full"></div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between border-t border-slate-50 pt-4">
                                    <div class="flex flex-col">
                                        <span class="text-xs text-slate-400 font-medium">Dana Terkumpul</span>
                                        <span class="text-sm font-bold text-slate-800">Rp {{ number_format($campaign->current_amount, 0, ',', '.') }}</span>
                                    </div>
                                    
                                    <div class="text-right flex flex-col items-end">
                                        <span class="text-xs text-slate-400 font-medium">Sisa Waktu</span>
                                        @if($isExpired)
                                            <span class="text-xs font-bold text-red-500 bg-red-50 px-2 py-0.5 rounded">Selesai</span>
                                        @else
                                            <span class="text-sm font-bold text-slate-800 flex items-center gap-1">
                                                <i class="far fa-clock text-slate-400 text-xs"></i>
                                                {{ number_format($daysLeft, 0) }} Hari
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <a href="{{ route('donations.details', ['slug' => $campaign->slug]) }}" 
                                   class="block w-full text-center py-2.5 rounded-xl border-2 border-blue-600 text-blue-600 font-bold text-sm hover:bg-blue-600 hover:text-white transition-all duration-300">
                                    Donasi Sekarang
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full flex justify-center py-20">
                        <div class="text-center max-w-lg">
                            <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i class="fas fa-search text-3xl text-slate-400"></i>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 mb-2">Belum ada kampanye ditemukan</h3>
                            <p class="text-slate-500 mb-8">Coba ubah kata kunci pencarian atau kategori filter Anda untuk menemukan hasil yang lain.</p>
                            <a href="{{ route('campaigns.all') }}" class="text-blue-600 font-semibold hover:text-blue-700 hover:underline">
                                Bersihkan Filter
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            @if($campaigns->hasPages())
                <div class="mt-16">
                    {{ $campaigns->links() }}
                </div>
            @endif
        </div>
    </section>
</x-app>