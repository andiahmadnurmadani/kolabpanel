<x-app title="Pilih Kampanye Kebaikan - DonGiv">

    {{-- CSS Tambahan untuk Hide Scrollbar & Transition --}}
    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .fade-enter {
            opacity: 0;
            transform: scale(0.95);
        }

        .fade-enter-active {
            opacity: 1;
            transform: scale(1);
            transition: opacity 300ms, transform 300ms;
        }

        .fade-exit {
            opacity: 1;
            transform: scale(1);
        }

        .fade-exit-active {
            opacity: 0;
            transform: scale(0.95);
            transition: opacity 300ms, transform 300ms;
        }
    </style>

    <section class="py-12 bg-slate-50 min-h-screen relative">
        <div class="container mx-auto px-6 max-w-7xl">

            {{-- HEADER SECTION --}}
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="text-indigo-600 font-bold tracking-wider text-sm uppercase mb-3 block">Mulai Aksi Nyata</span>
                <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 mb-4">
                    Kampanye yang Membutuhkanmu
                </h1>
                <p class="text-slate-500 text-lg">
                    Pilih isu yang dekat dengan hatimu. Satu klik darimu bisa mengubah hidup seseorang hari ini.
                </p>
            </div>

            {{-- SEARCH & FILTER BAR --}}
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 mb-10 flex flex-col md:flex-row gap-4 items-center justify-between sticky top-4 z-30">
                {{-- Search --}}
                <div class="relative w-full md:w-96">
                    <input type="text" id="searchInput" onkeyup="filterCampaigns()" placeholder="Cari judul kampanye..."
                        class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
                    <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                </div>

                {{-- Filter Categories (JS Based) --}}
                <div class="flex gap-2 overflow-x-auto pb-2 md:pb-0 w-full md:w-auto no-scrollbar" id="categoryFilters">
                    <button onclick="filterCategory('all', this)" class="filter-btn active px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-bold shadow-md whitespace-nowrap transition-all">
                        Semua
                    </button>
                    <button onclick="filterCategory('Pendidikan', this)" class="filter-btn px-4 py-2 bg-slate-50 text-slate-600 hover:bg-slate-100 rounded-lg text-sm font-medium whitespace-nowrap border border-slate-200 transition-all">
                        Pendidikan
                    </button>
                    <button onclick="filterCategory('Bencana', this)" class="filter-btn px-4 py-2 bg-slate-50 text-slate-600 hover:bg-slate-100 rounded-lg text-sm font-medium whitespace-nowrap border border-slate-200 transition-all">
                        Bencana
                    </button>
                    <button onclick="filterCategory('Kesehatan', this)" class="filter-btn px-4 py-2 bg-slate-50 text-slate-600 hover:bg-slate-100 rounded-lg text-sm font-medium whitespace-nowrap border border-slate-200 transition-all">
                        Kesehatan
                    </button>
                    <button onclick="filterCategory('Lingkungan', this)" class="filter-btn px-4 py-2 bg-slate-50 text-slate-600 hover:bg-slate-100 rounded-lg text-sm font-medium whitespace-nowrap border border-slate-200 transition-all">
                        Lingkungan
                    </button>
                    <button onclick="filterCategory('Sosial', this)" class="filter-btn px-4 py-2 bg-slate-50 text-slate-600 hover:bg-slate-100 rounded-lg text-sm font-medium whitespace-nowrap border border-slate-200 transition-all">
                        Sosial
                    </button>
                </div>
            </div>

            {{-- CAMPAIGN GRID --}}
            <div id="campaignGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($campaigns as $campaign)
                @php
                $badgeColor = match($campaign->kategori) {
                'Bencana' => 'bg-red-100 text-red-600',
                'Lingkungan' => 'bg-green-100 text-green-600',
                'Kesehatan' => 'bg-pink-100 text-pink-600',
                'Pendidikan' => 'bg-blue-100 text-blue-600',
                'Sosial' => 'bg-orange-100 text-orange-600',
                default => 'bg-gray-100 text-gray-600',
                };
                $formattedDate = \Carbon\Carbon::parse($campaign->tanggal_mulai)->format('d M Y');
                @endphp

                {{-- Item Kampanye --}}
                <div class="campaign-item group bg-white rounded-3xl overflow-hidden border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col h-full"
                    data-category="{{ $campaign->kategori }}"
                    data-title="{{ $campaign->judul }}">

                    <div class="relative h-48 overflow-hidden">
                        <div class="absolute top-4 left-4 z-10">
                            <span class="px-3 py-1 {{ $badgeColor }} backdrop-blur-sm text-[10px] font-bold uppercase tracking-wider rounded-lg shadow-sm">
                                {{ $campaign->kategori }}
                            </span>
                        </div>
                        <img src="{{ $campaign->image }}"
                            alt="{{ $campaign->judul }}"
                            class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700"
                            onerror="this.onerror=null; this.src='https://placehold.co/400x300?text=No+Image';">
                    </div>

                    <div class="p-6 flex flex-col flex-grow">
                        <div class="flex items-center gap-2 mb-3 text-slate-400 text-xs font-semibold">
                            <i class="fas fa-map-marker-alt text-indigo-500"></i> {{ Str::limit($campaign->lokasi, 20) }}
                            <span class="mx-1">•</span>
                            <i class="far fa-calendar-alt text-indigo-500"></i> {{ $formattedDate }}
                            <span class="mx-1">•</span>
                            <i class="fas fa-clock text-indigo-500"></i>
                            @php
                                $endDate = \Carbon\Carbon::parse($campaign->tanggal_selesai);
                                $now = \Carbon\Carbon::now();
                                $diffInDays = $endDate->diffInDays($now, false);
                            @endphp
                            @if($diffInDays > 0)
                                {{ $diffInDays }} hari tersisa
                            @elseif($diffInDays == 0)
                                Hari ini berakhir
                            @else
                                Sudah berakhir
                            @endif
                        </div>

                        <h3 class="text-xl font-bold text-slate-900 mb-3 group-hover:text-indigo-600 transition-colors line-clamp-2">
                            {{ $campaign->judul }}
                        </h3>

                        <p class="text-slate-500 text-sm leading-relaxed mb-6 line-clamp-3 flex-grow">
                            {{ Str::limit($campaign->deskripsi, 100) }}
                        </p>

                        <div class="mb-6">
                            <div class="flex justify-between text-xs font-bold mb-1">
                                <span class="text-slate-700">Kuota Terisi</span>
                                <span class="{{ $campaign->progress >= 100 ? 'text-red-600' : 'text-indigo-600' }}">
                                    {{ $campaign->kuota_terisi }} / {{ $campaign->kuota_total }}
                                </span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2">
                                <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $campaign->progress }}%"></div>
                            </div>
                        </div>
                    <button type="button" 
                        onclick="openCampaignModal(this)"
                        data-slug="{{ $campaign->slug }}"
                        data-title="{{ htmlspecialchars($campaign->judul, ENT_QUOTES) }}"
                        data-desc="{{ htmlspecialchars($campaign->deskripsi, ENT_QUOTES) }}"
                        data-image="{{ $campaign->image }}"
                        data-location="{{ htmlspecialchars($campaign->lokasi, ENT_QUOTES) }}"
                        data-date="{{ $formattedDate }}"
                        data-category="{{ $campaign->kategori }}"
                        data-skills="{{ $campaign->skills ?? 'Relawan Umum,Logistik,Dokumentasi' }}" 
                        data-organizer="DonGiv"
                        data-progress="{{ $campaign->progress }}"
                        data-quota-current="{{ $campaign->kuota_terisi }}"
                        data-quota-total="{{ $campaign->kuota_total }}"
                        data-register-url="{{ route('volunteer.register.create', $campaign->slug) }}"
                        class="w-full py-3 bg-white border-2 border-slate-100 text-slate-900 font-bold rounded-xl text-center hover:border-indigo-600 hover:text-indigo-600 hover:bg-indigo-50 transition-all duration-300">
                        Lihat Detail
                    </button>
                    </div>
                </div>
                @empty
                <div class="col-span-1 md:col-span-3 text-center py-12">
                    <p class="text-slate-400">Belum ada kampanye tersedia.</p>
                </div>
                @endforelse
            </div>

            {{-- State Kosong (Hidden by Default, shown by JS if filter returns 0) --}}
            <div id="noResults" class="hidden text-center py-20">
                <div class="inline-block p-4 rounded-full bg-slate-100 mb-4">
                    <i class="fas fa-search text-4xl text-slate-400"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-700">Tidak ditemukan.</h3>
                <p class="text-slate-500">Coba ganti kata kunci atau kategori lain.</p>
            </div>

            {{-- Pagination --}}
            <div class="mt-12">
                {{ $campaigns->links() }}
            </div>
        </div>
    </section>

    <div id="campaignModal" class="fixed inset-0 z-[60] hidden" role="dialog" aria-modal="true">
    {{-- Backdrop Gelap --}}
    <div class="fixed inset-0 bg-gray-900/75 transition-opacity opacity-0" id="modalBackdrop"></div>

    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            
            {{-- MODAL PANEL --}}
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-4xl opacity-0 scale-95 border border-slate-200" id="modalPanel">
                
                {{-- HEADER: Tombol Close yang Jelas --}}
                <div class="absolute top-4 right-4 z-20">
                    <button type="button" onclick="closeCampaignModal()" class="bg-white/80 hover:bg-white text-slate-400 hover:text-red-500 rounded-full p-2 transition-all shadow-sm border border-slate-100">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- GAMBAR UTAMA (Full Width tapi pendek agar rapi) --}}
                <div class="relative h-56 w-full bg-slate-100">
                    <img id="modalImage" src="" alt="Campaign Cover" class="h-full w-full object-cover" onerror="this.onerror=null; this.src='https://placehold.co/400x300?text=No+Image';">
                    <div class="absolute inset-0 bg-gradient-to-t from-white via-transparent to-transparent opacity-30"></div>
                </div>

                {{-- KONTEN BODY (Grid Layout) --}}
                <div class="px-6 py-6 sm:px-8">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        
                        {{-- KOLOM KIRI (Detail Utama - Lebar 2 Kolom) --}}
                        <div class="lg:col-span-2 space-y-6">
                            
                            {{-- Judul & Meta --}}
                            <div>
                                <span id="modalCategory" class="inline-block px-3 py-1 mb-3 text-xs font-bold tracking-wider uppercase bg-indigo-50 text-indigo-600 rounded-md">
                                    Kategori
                                </span>
                                <h3 class="text-2xl font-bold text-slate-900 leading-tight mb-2" id="modalTitle">
                                    Judul Kampanye
                                </h3>
                                <div class="flex flex-wrap gap-4 text-sm text-slate-500 font-medium items-center">
                                    <span class="flex items-center gap-1.5">
                                        <i class="fas fa-building text-slate-400"></i> <span id="modalOrganizer">DonGiv</span>
                                    </span>
                                    <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                                    <span class="flex items-center gap-1.5">
                                        <i class="fas fa-map-marker-alt text-slate-400"></i> <span id="modalLocation">Lokasi</span>
                                    </span>
                                    <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                                    <span class="flex items-center gap-1.5">
                                        <i class="far fa-calendar-alt text-slate-400"></i> <span id="modalDate">Tanggal</span>
                                    </span>
                                </div>
                            </div>

                            <hr class="border-slate-100">

                            {{-- Deskripsi --}}
                            <div>
                                <h4 class="font-bold text-slate-800 mb-2">Tentang Aksi Ini</h4>
                                <div class="prose prose-sm text-slate-600 leading-relaxed text-justify max-w-none" id="modalDesc">
                                    Deskripsi...
                                </div>
                            </div>

                            {{-- Skills / Posisi --}}
                            <div class="bg-slate-50 rounded-xl p-5 border border-slate-100">
                                <h4 class="text-sm font-bold text-slate-700 mb-3 flex items-center gap-2">
                                    <i class="fas fa-users text-indigo-500"></i> Posisi yang Dibutuhkan
                                </h4>
                                <div id="modalSkills" class="flex flex-wrap gap-2">
                                    {{-- Badge Skill akan masuk sini via JS --}}
                                </div>
                            </div>
                        </div>

                        {{-- KOLOM KANAN (Sidebar Aksi - Lebar 1 Kolom) --}}
                        <div class="lg:col-span-1">
                            <div class="bg-white rounded-xl border border-slate-200 shadow-lg shadow-slate-200/50 p-6 sticky top-0">
                                <div class="mb-6">
                                    <div class="flex justify-between items-end mb-2">
                                        <span class="text-sm font-bold text-slate-500">Target Relawan</span>
                                        <span class="text-xs font-bold px-2 py-0.5 bg-green-100 text-green-700 rounded-full" id="modalProgressText">0%</span>
                                    </div>
                                    
                                    {{-- Progress Bar --}}
                                    <div class="w-full bg-slate-100 rounded-full h-3 mb-2 overflow-hidden">
                                        <div id="modalProgressBar" class="bg-indigo-600 h-3 rounded-full transition-all duration-500" style="width: 0%"></div>
                                    </div>

                                    <div class="flex justify-between text-sm">
                                        <div class="font-bold text-slate-900"><span id="modalQuotaCurrent">0</span> <span class="font-normal text-slate-500">Terisi</span></div>
                                        <div class="font-bold text-slate-900"><span id="modalQuotaTotal">0</span> <span class="font-normal text-slate-500">Kuota</span></div>
                                    </div>
                                </div>

                                {{-- Tombol Aksi --}}
                                <div class="space-y-3">
                                    <a href="#" id="modalRegisterBtn" class="flex items-center justify-center w-full py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md shadow-indigo-200 transition-all transform hover:-translate-y-0.5">
                                        Daftar 
                                    </a>
                                    <button onclick="closeCampaignModal()" class="w-full py-3.5 bg-white border border-slate-200 text-slate-600 font-bold rounded-xl hover:bg-slate-50 transition-colors">
                                        Kembali
                                    </button>
                                </div>
                                
                                <div class="mt-4 pt-4 border-t border-slate-100 text-center">
                                    <p class="text-[10px] text-slate-400">
                                        Pastikan data diri Anda sudah lengkap sebelum mendaftar.
                                    </p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    // --- 1. FILTER SYSTEM (Aman) ---
    // Kita gunakan window. agar bisa diakses global, tapi nama fungsi dibuat unik
    window.filterCategory = function(category, btnElement) {
        // Reset styles
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('bg-indigo-600', 'text-white', 'shadow-md');
            btn.classList.add('bg-slate-50', 'text-slate-600', 'border', 'border-slate-200');
        });

        // Activate clicked button
        btnElement.classList.remove('bg-slate-50', 'text-slate-600', 'border', 'border-slate-200');
        btnElement.classList.add('bg-indigo-600', 'text-white', 'shadow-md');

        // Filter Logic
        const items = document.querySelectorAll('.campaign-item');
        let visibleCount = 0;

        items.forEach(item => {
            const itemCategory = item.getAttribute('data-category');
            if (category === 'all' || itemCategory === category) {
                item.classList.remove('hidden');
                visibleCount++;
            } else {
                item.classList.add('hidden');
            }
        });

        const noResults = document.getElementById('noResults');
        if(noResults) {
            visibleCount === 0 ? noResults.classList.remove('hidden') : noResults.classList.add('hidden');
        }
    }

    window.filterCampaigns = function() {
        const input = document.getElementById('searchInput');
        if(!input) return;
        
        const filter = input.value.toLowerCase();
        const items = document.querySelectorAll('.campaign-item');
        let visibleCount = 0;

        items.forEach(item => {
            const title = item.getAttribute('data-title').toLowerCase();
            if (title.includes(filter)) {
                item.classList.remove('hidden');
                visibleCount++;
            } else {
                item.classList.add('hidden');
            }
        });

        const noResults = document.getElementById('noResults');
        if(noResults) {
            visibleCount === 0 ? noResults.classList.remove('hidden') : noResults.classList.add('hidden');
        }
    }

    // --- OPEN MODAL LOGIC ---
    window.openCampaignModal = function(element) {
        const modal = document.getElementById('campaignModal');
        const backdrop = document.getElementById('modalBackdrop');
        const panel = document.getElementById('modalPanel');

        if (!modal) return;

        // Helper untuk Decode HTML Entities
        const decodeHtml = (html) => {
            const txt = document.createElement("textarea");
            txt.innerHTML = html;
            return txt.value;
        };

        // 1. Populate Text Data
        document.getElementById('modalTitle').innerText = decodeHtml(element.getAttribute('data-title'));
        document.getElementById('modalDesc').innerText = decodeHtml(element.getAttribute('data-desc'));
        document.getElementById('modalLocation').innerText = decodeHtml(element.getAttribute('data-location'));
        document.getElementById('modalDate').innerText = element.getAttribute('data-date');
        document.getElementById('modalCategory').innerText = element.getAttribute('data-category');
        document.getElementById('modalOrganizer').innerText = element.getAttribute('data-organizer') || 'DonasiKita';

        // 2. Populate Image
        const imgUrl = element.getAttribute('data-image');
        const imgElement = document.getElementById('modalImage');
        imgElement.onerror = function() { this.onerror=null; this.src='https://placehold.co/400x300?text=No+Image'; };
        imgElement.src = imgUrl;

        // 3. Logic Progress Bar & Kuota
        const current = element.getAttribute('data-quota-current');
        const total = element.getAttribute('data-quota-total');
        const progress = element.getAttribute('data-progress');

        document.getElementById('modalQuotaCurrent').innerText = current;
        document.getElementById('modalQuotaTotal').innerText = total;
        document.getElementById('modalProgressText').innerText = progress + '%';
        
        const progressBar = document.getElementById('modalProgressBar');
        if(progressBar) progressBar.style.width = progress + '%';

        // 4. Logic Skills (Badge yang Rapi)
        const skillsContainer = document.getElementById('modalSkills');
        const skillsString = element.getAttribute('data-skills'); 
        
        skillsContainer.innerHTML = ''; // Clear lama
        
        if (skillsString && skillsString.trim() !== '') {
            const skillsArray = skillsString.split(',');
            skillsArray.forEach(skill => {
                const cleanSkill = skill.trim();
                if(cleanSkill) {
                    // Badge Style: Biru muda bersih
                    const badge = document.createElement('span');
                    badge.className = "px-3 py-1.5 bg-white border border-slate-200 text-slate-600 text-xs font-semibold rounded-lg shadow-sm";
                    badge.innerText = cleanSkill;
                    skillsContainer.appendChild(badge);
                }
            });
        } else {
            skillsContainer.innerHTML = '<span class="text-sm text-slate-400 italic">Tidak ada spesifikasi khusus.</span>';
        }

        // 5. Button Logic (Validasi Penuh)
        const registerBtn = document.getElementById('modalRegisterBtn');
        const registerUrl = element.getAttribute('data-register-url');
        const progressVal = parseFloat(progress);

        if (registerBtn) {
            registerBtn.href = registerUrl;
            
            if (progressVal >= 100) {
                // Style Disabled
                registerBtn.className = "flex items-center justify-center w-full py-3.5 bg-slate-100 text-slate-400 font-bold rounded-xl border border-slate-200 cursor-not-allowed pointer-events-none";
                registerBtn.innerText = 'Kuota Penuh';
                registerBtn.removeAttribute('href');
            } else {
                // Style Active
                registerBtn.className = "flex items-center justify-center w-full py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md shadow-indigo-200 transition-all transform hover:-translate-y-0.5";
                registerBtn.innerText = 'Daftar Sekarang';
            }
        }

        // Animasi Masuk
        modal.classList.remove('hidden');
        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            panel.classList.remove('opacity-0', 'scale-95');
            panel.classList.add('opacity-100', 'scale-100');
        }, 10);
    }

    // --- CLOSE MODAL LOGIC ---
    window.closeCampaignModal = function() {
        const modal = document.getElementById('campaignModal');
        const backdrop = document.getElementById('modalBackdrop');
        const panel = document.getElementById('modalPanel');

        if (!modal) return;

        backdrop.classList.add('opacity-0');
        panel.classList.remove('opacity-100', 'scale-100');
        panel.classList.add('opacity-0', 'scale-95');

        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    // Event Listener Backdrop
    document.addEventListener('DOMContentLoaded', function() {
        const backdrop = document.getElementById('modalBackdrop');
        if(backdrop) {
            backdrop.addEventListener('click', window.closeCampaignModal);
        }
    });
</script>
</x-app>