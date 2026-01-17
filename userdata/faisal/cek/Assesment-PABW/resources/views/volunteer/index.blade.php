<x-app title="DonGiv - Bergabung sebagai Relawan">

    <style>
        .pattern-grid {
            background-image: radial-gradient(#cbd5e1 1.5px, transparent 1.5px);
            background-size: 24px 24px;
        }

        .text-glow {
            text-shadow: 0 0 20px rgba(59, 130, 246, 0.15);
        }
    </style>

    {{-- HERO SECTION: SPLIT LAYOUT (Compact & Professional) --}}
    <section class="relative pt-28 pb-20 overflow-hidden bg-white">
        {{-- Background Pattern Halus --}}
        <div class="absolute inset-0 pattern-grid opacity-[0.3] mask-image-gradient"></div>

        {{-- Blob background agar tidak terlalu sepi --}}
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-blue-50/60 rounded-full blur-3xl -translate-y-1/4 translate-x-1/4 -z-10"></div>

        <div class="container mx-auto px-6 relative z-10">
            <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-20">

                {{-- KOLOM KIRI: Teks & Call to Action (Lebih Fokus) --}}
                <div class="w-full lg:w-1/2 text-left">
                    {{-- Social Proof Badge --}}
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white border border-gray-200 shadow-sm mb-6 animate-fade-in-up w-fit">
                        <div class="flex -space-x-2">
                            <img class="w-5 h-5 rounded-full border border-white" src="https://i.pravatar.cc/100?img=12" alt="Volunteer">
                            <img class="w-5 h-5 rounded-full border border-white" src="https://i.pravatar.cc/100?img=33" alt="Volunteer">
                            <img class="w-5 h-5 rounded-full border border-white" src="https://i.pravatar.cc/100?img=5" alt="Volunteer">
                        </div>
                        <span class="text-[10px] md:text-xs font-semibold text-gray-600 pl-1">Gabung 1,200+ relawan</span>
                    </div>

                    <h1 class="text-4xl lg:text-6xl font-extrabold text-gray-900 mb-6 tracking-tight leading-[1.1]">
                        Ubah Niat Baik <br>
                        Menjadi <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-blue-500 text-glow">Dampak Nyata.</span>
                    </h1>

                    <p class="text-lg text-gray-500 mb-8 leading-relaxed font-light max-w-lg">
                        Jangan biarkan kepedulian hanya berhenti di pikiran. DonGiv menyediakan ekosistem terstruktur agar waktumu menghasilkan perubahan yang terukur.
                    </p>

                    <div class="flex flex-wrap gap-4">
                        {{-- Kode Baru: Arahkan ke halaman list kampanye --}}
                        <a href="{{ route('volunteer.campaigns.index') }}" class="px-7 py-3.5 bg-gray-900 text-white font-bold rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex items-center gap-3 group">
                            Daftar Sekarang <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                        </a>
                        <a href="#roles" class="px-7 py-3.5 bg-white text-gray-700 font-bold rounded-xl border border-gray-200 hover:border-gray-300 hover:bg-gray-50 transition-all duration-300">
                            Pelajari Posisi
                        </a>
                    </div>

                    {{-- Mini Stat (Optional, untuk mengisi ruang bawah kiri) --}}
                    <div class="mt-10 flex gap-8 border-t border-gray-100 pt-6">
                        <div>
                            <p class="text-2xl font-bold text-gray-900">150+</p>
                            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Misi Selesai</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900">34</p>
                            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Provinsi</p>
                        </div>
                    </div>
                </div>

                {{-- KOLOM KANAN: Gambar (Visual Interest) --}}
                <div class="w-full lg:w-1/2 relative">
                    {{-- Main Image Wrapper with Shape --}}
                    <div class="relative rounded-3xl overflow-hidden shadow-2xl border border-gray-100 group">
                        <div class="absolute inset-0 bg-primary/10 group-hover:bg-transparent transition-colors duration-500 z-10 pointer-events-none"></div>
                        <img src="https://images.unsplash.com/photo-1559027615-cd4628902d4a?q=80&w=1200&auto=format&fit=crop"
                            class="w-full h-[400px] lg:h-[500px] object-cover transform transition-transform duration-700 group-hover:scale-105"
                            alt="Volunteer Action">
                    </div>

                    {{-- Floating Decoration (Agar tidak terlalu kotak) --}}
                    <div class="absolute -bottom-6 -left-6 bg-white p-4 rounded-xl shadow-lg border border-gray-100 flex items-center gap-3 animate-bounce-slow max-w-[200px]">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center text-green-600 shrink-0">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold uppercase">Status</p>
                            <p class="text-sm font-bold text-gray-900">Verified Program</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- BENEFITS SECTION (Tetap sama karena sudah oke, hanya penyesuaian spacing) --}}
    <section class="py-20 bg-white border-b border-gray-100">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="p-6">
                    <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center text-primary text-xl mb-4"><i class="fas fa-chart-line"></i></div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Impact Analytics</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Lihat seberapa besar kontribusi jam kerjamu terhadap keberhasilan proyek secara real-time.</p>
                </div>
                <div class="p-6">
                    <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center text-primary text-xl mb-4"><i class="fas fa-user-tie"></i></div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Professional Network</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Akses eksklusif ke komunitas mentor, praktisi NGO, dan sesama relawan berprestasi.</p>
                </div>
                <div class="p-6">
                    <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center text-primary text-xl mb-4"><i class="fas fa-medal"></i></div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Certified Experience</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Sertifikat digital resmi yang valid untuk melengkapi portofolio karir atau studi lanjutmu.</p>
                </div>
            </div>
        </div>
    </section>

    <script src="//unpkg.com/alpinejs" defer></script>

    <section id="roles" class="py-24 bg-slate-50" x-data="{ activeTab: 'all' }">
        <div class="container mx-auto px-6 max-w-7xl">

            {{-- HEADER --}}
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-4xl md:text-5xl font-extrabold text-slate-900 tracking-tight mb-6">
                    Temukan Misi <span class="text-indigo-600">Kebaikanmu</span>
                </h2>
                <p class="text-lg text-slate-600 leading-relaxed">
                    Bergabung dengan ribuan relawan DonGiv lainnya. Pilih peran yang menantang keahlianmu.
                </p>
            </div>

            {{-- FILTER TABS --}}
            <div class="flex justify-center mb-12">
                <div class="inline-flex bg-white p-1.5 rounded-full shadow-lg shadow-slate-200/50 border border-slate-100 flex-wrap justify-center gap-2 md:gap-0">
                    <button @click="activeTab = 'all'"
                        :class="activeTab === 'all' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-500 hover:text-indigo-600 hover:bg-indigo-50'"
                        class="px-6 py-2.5 rounded-full text-sm font-bold transition-all duration-300">
                        Semua Posisi
                    </button>
                    <button @click="activeTab = 'onsite'"
                        :class="activeTab === 'onsite' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-500 hover:text-indigo-600 hover:bg-indigo-50'"
                        class="px-6 py-2.5 rounded-full text-sm font-bold transition-all duration-300">
                        Lapangan (On-Site)
                    </button>
                    <button @click="activeTab = 'remote'"
                        :class="activeTab === 'remote' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-500 hover:text-indigo-600 hover:bg-indigo-50'"
                        class="px-6 py-2.5 rounded-full text-sm font-bold transition-all duration-300">
                        Remote (WFH)
                    </button>
                </div>
            </div>

            {{-- GRID CARD DINAMIS (REPLACE BAGIAN INI) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">

                @forelse($campaigns as $campaign)
                {{-- Logika Filter --}}
                @php
                $isRemote = stripos($campaign->lokasi, 'Remote') !== false || stripos($campaign->lokasi, 'Online') !== false || stripos($campaign->lokasi, 'WFH') !== false;
                $filterCategory = $isRemote ? 'remote' : 'onsite';
                @endphp

                <div x-show="activeTab === 'all' || activeTab === '{{ $filterCategory }}'"
                    x-transition.opacity.duration.500ms
                    class="group bg-white rounded-3xl p-8 border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 relative flex flex-col h-full">

                    {{-- Badge Urgent --}}
                    @if(($campaign->kuota_total - $campaign->kuota_terisi) < 5)
                        <div class="absolute top-8 right-8">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-50 border border-red-100 rounded-full text-[10px] font-bold text-red-600 uppercase tracking-wider">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span> Urgent
                        </span>
                </div>
                @else
                <div class="absolute top-8 right-8">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-50 border border-blue-100 rounded-full text-[10px] font-bold text-blue-600 uppercase tracking-wider">
                        {{ $campaign->kategori }}
                    </span>
                </div>
                @endif

                {{-- Campaign Image --}}
                @if($campaign->image)
                    <div class="w-full h-40 rounded-xl overflow-hidden mb-4">
                        <img src="{{ $campaign->image }}"
                             alt="{{ $campaign->judul }}"
                             class="w-full h-full object-cover"
                             onerror="this.onerror=null; this.src='https://placehold.co/400x200?text=No+Image';">
                    </div>
                @else
                    <div class="w-full h-40 rounded-xl bg-gray-100 mb-4 flex items-center justify-center">
                        <i class="fas fa-image text-gray-400 text-2xl"></i>
                    </div>
                @endif

                {{-- Icon --}}
                <div class="w-14 h-14 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 text-2xl mb-5 group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-300">
                    @if($campaign->kategori == 'Pendidikan') <i class="fas fa-chalkboard-teacher"></i>
                    @elseif($campaign->kategori == 'Kesehatan') <i class="fas fa-heartbeat"></i>
                    @elseif($campaign->kategori == 'Bencana') <i class="fas fa-house-damage"></i>
                    @elseif($campaign->kategori == 'Lingkungan') <i class="fas fa-leaf"></i>
                    @else <i class="fas fa-hand-holding-heart"></i>
                    @endif
                </div>

                {{-- Judul --}}
                <h3 class="text-2xl font-bold text-slate-900 mb-3 line-clamp-1" title="{{ $campaign->judul }}">
                    {{ $campaign->judul }}
                </h3>
                <p class="text-slate-500 mb-6 leading-relaxed line-clamp-2 flex-grow">
                    {{ Str::limit($campaign->deskripsi, 80) }}
                </p>

                {{-- Progress Bar --}}
                <div class="mb-6 w-full bg-slate-100 rounded-full h-1.5">
                    <div class="bg-indigo-500 h-1.5 rounded-full" style="width: {{ $campaign->progress }}%"></div>
                </div>

                {{-- Footer --}}
                <div class="space-y-4 pt-6 border-t border-slate-50 mt-auto">
                    <div class="flex items-center gap-3 text-sm text-slate-600">
                        <div class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center text-slate-400">
                            @if($isRemote) <i class="fas fa-laptop-house"></i> @else <i class="fas fa-map-marker-alt"></i> @endif
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-400 font-bold uppercase">Lokasi</p>
                            <p class="font-bold text-slate-800">{{ $campaign->lokasi }}</p>
                        </div>
                    </div>

                    {{-- Tombol --}}
                    <a href="{{ route('volunteer.campaigns.show', $campaign->slug) }}" class="block w-full py-3 px-4 bg-white border-2 border-slate-100 text-slate-900 font-bold rounded-xl text-center hover:border-indigo-600 hover:text-indigo-600 hover:bg-indigo-50 transition-all">
                        Daftar Sekarang <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>

            @empty
            {{-- Kosong --}}
            <div class="col-span-1 md:col-span-2 lg:col-span-3 text-center py-12">
                <div class="inline-flex w-16 h-16 bg-slate-100 rounded-full items-center justify-center mb-4">
                    <i class="fas fa-folder-open text-slate-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-700">Belum ada kampanye aktif</h3>
            </div>
            @endforelse

        </div>

        {{-- NEW SECTION: Tombol Lihat Semua --}}
        <div class="flex flex-col items-center justify-center pt-8 border-t border-slate-200/60">
            <p class="text-slate-500 mb-4 text-sm font-medium">Masih belum menemukan peran yang cocok?</p>
            <a href="#" class="group inline-flex items-center gap-3 px-8 py-4 bg-slate-900 text-white rounded-full font-bold shadow-lg hover:bg-slate-800 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300">
                Lihat Semua Divisi & Peran
                <div class="w-6 h-6 bg-white/20 rounded-full flex items-center justify-center group-hover:bg-white/30 transition-all">
                    <i class="fas fa-arrow-right text-xs"></i>
                </div>
            </a>
        </div>

        </div>
    </section>

    <section class="py-24 bg-slate-50 relative">
        <div class="container mx-auto px-6 max-w-7xl">

            {{-- Header Section: Centered & Clean --}}
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-indigo-600 font-bold tracking-wider text-sm uppercase mb-3 block">Wall of Love</span>
                <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 mb-6">
                    Cerita Nyata Relawan
                </h2>
                <p class="text-slate-500 text-lg leading-relaxed">
                    Ribuan jam telah didonasikan. Inilah dampak yang dirasakan langsung oleh mereka yang turun tangan.
                </p>
            </div>

            {{-- MASONRY LAYOUT: Ini kuncinya untuk efek "Kolom Memanjang" --}}
            {{-- columns-1 (mobile) -> columns-2 (tablet) -> columns-3 (desktop) --}}
            <div class="columns-1 md:columns-2 lg:columns-3 gap-6 space-y-6">

                {{-- Card 1: Pendek --}}
                <div class="break-inside-avoid bg-white p-6 rounded-2xl border border-slate-100 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                    <div class="flex items-center gap-3 mb-4">
                        <img src="https://i.pravatar.cc/150?img=32" class="w-10 h-10 rounded-full bg-slate-100">
                        <div>
                            <h4 class="font-bold text-slate-900 text-sm">Sarah Amalia</h4>
                            <p class="text-xs text-slate-500">Relawan Pengajar • 2024</p>
                        </div>
                    </div>
                    <p class="text-slate-600 text-sm leading-relaxed mb-4">
                        "Awalnya ragu karena jadwal kuliah padat. Tapi sistem shift di DonGiv sangat fleksibel. Melihat anak-anak bisa membaca adalah bayaran termahal."
                    </p>
                    <div class="pt-4 border-t border-slate-50 flex items-center gap-2 text-xs font-semibold text-indigo-600">
                        <i class="fas fa-clock"></i> Total 45 Jam Kontribusi
                    </div>
                </div>

                {{-- Card 2: Panjang (Highlight) --}}
                <div class="break-inside-avoid bg-white p-8 rounded-2xl border-t-4 border-indigo-500 shadow-md hover:shadow-xl transition-all duration-300">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <img src="https://i.pravatar.cc/150?img=11" class="w-12 h-12 rounded-full bg-slate-100 border-2 border-white shadow-sm">
                            <div>
                                <h4 class="font-bold text-slate-900">Dimas Pratama</h4>
                                <div class="flex items-center gap-1">
                                    <span class="text-xs text-slate-500">Logistik Bencana</span>
                                    <i class="fas fa-check-circle text-blue-500 text-[10px]" title="Verified"></i>
                                </div>
                            </div>
                        </div>
                        <span class="text-4xl text-indigo-100 font-serif">"</span>
                    </div>

                    <h5 class="text-lg font-bold text-slate-800 mb-3">Pengalaman yang mengubah hidup.</h5>
                    <p class="text-slate-600 leading-relaxed mb-6">
                        "Manajemen krisis di lapangan benar-benar menguji mental. Skill leadership saya tumbuh pesat di sini. Saya belajar bahwa menjadi relawan bukan soal 'memberi', tapi soal seberapa banyak kita 'menerima' pelajaran hidup dari mereka yang kuat."
                    </p>

                    <div class="flex gap-2">
                        <span class="px-2 py-1 bg-slate-100 text-slate-600 text-[10px] font-bold uppercase rounded">Leadership</span>
                        <span class="px-2 py-1 bg-slate-100 text-slate-600 text-[10px] font-bold uppercase rounded">Crisis Mgmt</span>
                    </div>
                </div>

                {{-- Card 3: Medium --}}
                <div class="break-inside-avoid bg-white p-6 rounded-2xl border border-slate-100 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                    <div class="flex items-center gap-3 mb-4">
                        <img src="https://i.pravatar.cc/150?img=5" class="w-10 h-10 rounded-full bg-slate-100">
                        <div>
                            <h4 class="font-bold text-slate-900 text-sm">Rina Kartika</h4>
                            <p class="text-xs text-slate-500">Fundraiser • 2023</p>
                        </div>
                    </div>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        "Platform DonGiv sangat transparan. Kita bisa lihat impact donasi secara real-time, itu yang bikin saya percaya diri mengajak mitra korporat."
                    </p>
                </div>

                {{-- Card 4: Panjang (Story) --}}
                <div class="break-inside-avoid bg-white p-6 rounded-2xl border border-slate-100 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                    <div class="flex items-center gap-3 mb-4">
                        <img src="https://i.pravatar.cc/150?img=60" class="w-10 h-10 rounded-full bg-slate-100">
                        <div>
                            <h4 class="font-bold text-slate-900 text-sm">Budi Santoso</h4>
                            <p class="text-xs text-slate-500">Tech Support • 2024</p>
                        </div>
                    </div>
                    <p class="text-slate-600 text-sm leading-relaxed mb-4">
                        "Sebagai programmer introver, saya pikir sulit berkontribusi. Ternyata DonGiv butuh maintenance website. Coding untuk kebaikan rasanya beda banget!"
                    </p>
                    <div class="w-full bg-slate-100 rounded-lg p-3 text-center">
                        <p class="text-xs text-slate-500 font-medium">Project Terlibat</p>
                        <p class="text-sm font-bold text-indigo-600">Website Revamp 2.0</p>
                    </div>
                </div>

                {{-- Card 5: Pendek --}}
                <div class="break-inside-avoid bg-white p-6 rounded-2xl border border-slate-100 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                    <div class="flex items-center gap-3 mb-4">
                        <img src="https://i.pravatar.cc/150?img=45" class="w-10 h-10 rounded-full bg-slate-100">
                        <div>
                            <h4 class="font-bold text-slate-900 text-sm">Anita Wijaya</h4>
                            <p class="text-xs text-slate-500">Event Staff • 2024</p>
                        </div>
                    </div>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        "Capek? Pasti. Tapi melihat senyum para lansia saat acara charity kemarin? Worth every second."
                    </p>
                </div>

                {{-- Card 6: Impact --}}
                <div class="break-inside-avoid bg-gradient-to-br from-indigo-600 to-blue-600 p-6 rounded-2xl shadow-lg text-white text-center flex flex-col items-center justify-center min-h-[200px]">
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center mb-4 backdrop-blur-sm">
                        <i class="fas fa-heart text-xl"></i>
                    </div>
                    <h4 class="font-bold text-xl mb-2">Gabung Sekarang</h4>
                    <p class="text-indigo-100 text-sm mb-6">Jadilah bagian dari cerita perubahan selanjutnya.</p>
                   <a href="{{ route('volunteer.campaigns.index') }}" class="px-6 py-2 bg-white text-indigo-600 text-sm font-bold rounded-full hover:bg-indigo-50 transition-colors">
                    Daftar Relawan
                    </a>
                    </a>
                </div>

            </div>
        </div>
    </section>


    {{-- Sisa section (How it Works & CTA) bisa tetap menggunakan kode sebelumnya yang sudah Anda sukai --}}
    <section class="py-24 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900">Alur Pendaftaran</h2>
            </div>
            <div class="relative">
                <div class="hidden md:block absolute top-8 left-[10%] right-[10%] h-1 bg-gray-100 rounded-full"></div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    @foreach(['Daftar Akun', 'Pilih Program', 'Briefing/Seleksi', 'Mulai Aksi'] as $index => $step)
                    <div class="relative flex flex-col items-center text-center group">
                        <div class="w-16 h-16 rounded-2xl bg-white border-2 border-gray-100 shadow-sm flex items-center justify-center text-xl font-bold text-gray-400 mb-6 relative z-10 group-hover:border-primary group-hover:text-primary group-hover:scale-110 transition-all duration-300">
                            {{ $index + 1 }}
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $step }}</h3>
                        <p class="text-sm text-gray-500 px-4">Langkah mudah untuk memulai dampak besarmu.</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- SECTION BARU 2: FAQ ACCORDION (MENGHILANGKAN KERAGUAN) --}}
    {{-- Menggunakan Alpine.js untuk buka-tutup --}}
    <section class="py-24 bg-slate-50 border-t border-slate-200/60">
        <div class="container mx-auto px-6 max-w-4xl">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-extrabold text-slate-900 mb-4">Sering Ditanyakan</h2>
                <p class="text-slate-500">Jawaban cepat untuk pertanyaan umum calon relawan.</p>
            </div>

            <div class="space-y-4">
                {{-- FAQ Item 1 --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full flex justify-between items-center p-6 text-left focus:outline-none">
                        <span class="font-bold text-slate-900 text-lg">Apakah saya perlu pengalaman sebelumnya?</span>
                        <span :class="open ? 'rotate-180 bg-indigo-100 text-indigo-600' : 'bg-slate-100 text-slate-500'" class="w-8 h-8 rounded-full flex items-center justify-center transition-transform duration-300">
                            <i class="fas fa-chevron-down text-sm"></i>
                        </span>
                    </button>
                    <div x-show="open" x-collapse class="px-6 pb-6 text-slate-600 leading-relaxed border-t border-slate-50 pt-4">
                        Tidak wajib untuk semua posisi! Posisi seperti "Relawan Pengajar" atau "Logistik" terbuka untuk pemula. Kami menyediakan sesi briefing dan pelatihan singkat sebelum terjun ke lapangan. Namun, posisi spesifik seperti "Medis" memerlukan sertifikasi.
                    </div>
                </div>

                {{-- FAQ Item 2 --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full flex justify-between items-center p-6 text-left focus:outline-none">
                        <span class="font-bold text-slate-900 text-lg">Apakah ada sertifikat setelah kegiatan?</span>
                        <span :class="open ? 'rotate-180 bg-indigo-100 text-indigo-600' : 'bg-slate-100 text-slate-500'" class="w-8 h-8 rounded-full flex items-center justify-center transition-transform duration-300">
                            <i class="fas fa-chevron-down text-sm"></i>
                        </span>
                    </button>
                    <div x-show="open" x-collapse class="px-6 pb-6 text-slate-600 leading-relaxed border-t border-slate-50 pt-4">
                        Ya, tentu saja. Setiap relawan yang menyelesaikan misinya dengan baik akan mendapatkan E-Sertifikat resmi dari DonGiv yang mencantumkan detail peran dan jumlah jam kontribusi. Sangat berguna untuk portofolio atau SKPI kampus.
                    </div>
                </div>

                {{-- FAQ Item 3 --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full flex justify-between items-center p-6 text-left focus:outline-none">
                        <span class="font-bold text-slate-900 text-lg">Berapa lama durasi satu program?</span>
                        <span :class="open ? 'rotate-180 bg-indigo-100 text-indigo-600' : 'bg-slate-100 text-slate-500'" class="w-8 h-8 rounded-full flex items-center justify-center transition-transform duration-300">
                            <i class="fas fa-chevron-down text-sm"></i>
                        </span>
                    </button>
                    <div x-show="open" x-collapse class="px-6 pb-6 text-slate-600 leading-relaxed border-t border-slate-50 pt-4">
                        Durasi bervariasi tergantung proyek. Ada yang bersifat *one-time event* (1 hari), ada juga program dampingan rutin selama 1-3 bulan. Anda bisa memfilter durasi ini saat memilih peran di dashboard relawan nanti.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-24 bg-white">
        <div class="container mx-auto px-6 max-w-7xl">

            {{-- Main Container: Indigo Theme (REVISI PADDING: Lebih Ramping) --}}
            {{-- Mengubah md:p-16 menjadi md:p-12 agar tidak terlalu tinggi --}}
            <div class="bg-indigo-600 rounded-[2.5rem] p-8 md:p-12 relative overflow-hidden shadow-xl shadow-indigo-200">

                {{-- Background Decoration --}}
                <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#ffffff 2px, transparent 2px); background-size: 32px 32px;"></div>
                <div class="absolute -top-24 -right-24 w-96 h-96 bg-white opacity-10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-12">

                    {{-- KIRI: Copywriting --}}
                    <div class="w-full md:w-1/2 text-center md:text-left">
                        <h2 class="text-3xl md:text-5xl font-extrabold text-white mb-6 tracking-tight leading-tight">
                            Resmikan Niat Baikmu.
                        </h2>
                        <p class="text-indigo-100 text-lg mb-8 leading-relaxed max-w-md mx-auto md:mx-0">
                            Dapatkan identitas resmi sebagai relawan, akses pelatihan eksklusif, dan perluas jaringan kebaikanmu.
                        </p>

                        <div class="flex flex-col sm:flex-row items-center gap-4 justify-center md:justify-start">
                            <a href="{{ route('volunteer.campaigns.index') }}" class="px-8 py-4 bg-white text-indigo-700 font-bold rounded-xl shadow-lg hover:shadow-white/25 hover:bg-slate-50 hover:-translate-y-1 transition-all duration-300 flex items-center gap-2 group">
                                Gabung Sekarang <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                            </a>
                        </div>
                    </div>

                    {{-- KANAN: ID Card Visual (REVISI TINGGI & LOGO) --}}
                    {{-- Mengubah h-[420px] menjadi h-[380px] agar container kanan juga lebih pendek --}}
                    <div class="w-full md:w-1/2 flex justify-center md:justify-end relative h-[380px] items-center">

                        {{-- Tali Lanyard --}}
                        <div class="absolute top-0 right-[25%] md:right-[22%] w-2 h-32 bg-indigo-800/40 z-0"></div>

                        {{-- Container Kartu --}}
                        <div class="relative z-10 animate-float" style="animation: float 6s ease-in-out infinite;">

                            {{-- Clip Besi --}}
                            <div class="mx-auto w-16 h-6 bg-slate-300 rounded-t-lg border-x-2 border-t-2 border-slate-400 relative z-20">
                                <div class="w-12 h-1 bg-slate-400 mx-auto mt-2 rounded-full"></div>
                            </div>

                            {{-- Fisik Kartu --}}
                            <div class="w-[300px] bg-white rounded-xl shadow-2xl overflow-hidden relative border-t-8 border-indigo-500 transform rotate-2 hover:rotate-0 transition-transform duration-500">

                                {{-- Lubang Gantungan --}}
                                <div class="absolute top-2 left-1/2 -translate-x-1/2 w-8 h-1.5 bg-slate-200 rounded-full z-20 shadow-inner"></div>

                                {{-- Header Kartu (AREA REVISI LOGO) --}}
                                {{-- Mengubah pt-10 menjadi pt-8 agar header lebih ringkas --}}
                                <div class="bg-slate-50 p-6 pb-4 border-b border-slate-100 text-center pt-8">

                                    {{-- Mengubah h-12 menjadi h-8 agar logo lebih kecil dan proporsional --}}
                                    <img src="{{ asset('images/dongiv-logo.png') }}" alt="DonGiv Logo" class="h-8 w-auto mx-auto mb-2 object-contain">

                                    <p class="text-[10px] text-slate-400 uppercase tracking-widest font-semibold">Volunteer Pass</p>
                                </div>

                                {{-- Body Kartu --}}
                                <div class="p-6 text-center">
                                    {{-- Foto Profil --}}
                                    <div class="w-24 h-24 mx-auto bg-slate-200 rounded-full p-1 border-2 border-indigo-100 mb-4 shadow-sm">
                                        <img src="https://i.pravatar.cc/150?img=12" class="w-full h-full rounded-full object-cover">
                                    </div>

                                    <h3 class="text-xl font-bold text-slate-900 leading-tight">Nama Kamu</h3>
                                    <p class="text-xs text-slate-500 font-medium mb-4">Relawan Pengajar</p>

                                    {{-- Info Grid --}}
                                    <div class="grid grid-cols-2 gap-2 text-left bg-indigo-50/50 p-3 rounded-lg border border-indigo-50 mb-4">
                                        <div>
                                            <p class="text-[9px] text-slate-400 uppercase font-bold">ID Number</p>
                                            <p class="text-xs font-mono font-bold text-indigo-700">DG-8821</p>
                                        </div>
                                        <div>
                                            <p class="text-[9px] text-slate-400 uppercase font-bold">Valid Thru</p>
                                            <p class="text-xs font-mono font-bold text-slate-700">12/25</p>
                                        </div>
                                    </div>

                                    {{-- Barcode --}}
                                    <div class="flex justify-between items-center opacity-60">
                                        <div class="h-6 w-32 bg-repeat-x" style="background-image: linear-gradient(90deg, #333 0, #333 1px, transparent 1px, transparent 3px);"></div>
                                        <i class="fas fa-qrcode text-2xl text-slate-800"></i>
                                    </div>
                                </div>

                                {{-- Hologram Strip (Footer) --}}
                                <div class="h-2 w-full bg-gradient-to-r from-indigo-400 via-purple-400 to-indigo-400 opacity-50"></div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CSS untuk animasi float (pastikan ini ada di style Anda) --}}
    <style>
        @keyframes float {

            0%,
            100% {
                transform: translateY(0px) rotate(2deg);
            }

            50% {
                transform: translateY(-12px) rotate(0deg);
            }
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
    </style>

</x-app>