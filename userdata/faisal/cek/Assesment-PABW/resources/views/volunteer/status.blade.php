<x-app title="Keputusan Lamaran - {{ $campaign->title ?? $campaign->judul }}">
    
    {{-- Main Container --}}
    <div class="min-h-screen bg-[#F8FAFC] py-12 flex items-center justify-center px-4 font-sans">
        <div class="max-w-xl w-full">

            {{-- Navigation Back --}}
            <a href="{{ route('home') }}" class="inline-flex items-center text-xs font-bold text-slate-400 hover:text-blue-600 mb-6 transition-colors group tracking-wide uppercase">
                <i class="fas fa-chevron-left mr-2 group-hover:-translate-x-1 transition-transform"></i>
                Kembali ke Beranda
            </a>

            {{-- OFFICIAL CARD --}}
            <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 overflow-hidden border border-white ring-1 ring-slate-100 relative">
                
                {{-- 1. HEADER (Gradient Modern) --}}
                <div class="relative bg-gradient-to-br from-blue-600 to-indigo-700 h-32 overflow-hidden">
                    <div class="absolute top-0 right-0 w-48 h-48 bg-white/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
                    <div class="absolute bottom-0 left-0 w-32 h-32 bg-indigo-400/20 rounded-full blur-2xl translate-y-1/2 -translate-x-1/2"></div>
                    
                    {{-- Watermark --}}
                    <div class="absolute top-6 left-6 flex items-center gap-2 opacity-90">
                        <div class="w-8 h-8 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center border border-white/10">
                            <i class="fas fa-hand-holding-heart text-white text-sm"></i>
                        </div>
                        <span class="text-white font-bold tracking-wide text-sm font-sans">DonasiKita</span>
                    </div>
                </div>

                {{-- 2. STATUS ICON (Floating Overlay) --}}
                <div class="absolute top-16 left-1/2 transform -translate-x-1/2">
                    <div class="w-28 h-28 rounded-full bg-white p-3 shadow-2xl ring-4 ring-white">
                        <div class="w-full h-full rounded-full flex items-center justify-center border-4 
                            {{ $status == 'approved' ? 'border-green-100 bg-green-50 text-green-500' : 
                               ($status == 'rejected' ? 'border-red-100 bg-red-50 text-red-500' : 'border-blue-100 bg-blue-50 text-blue-500') }}">
                            
                            @if($status == 'approved')
                                <i class="fas fa-check text-5xl animate-bounce-subtle"></i>
                            @elseif($status == 'rejected')
                                <i class="fas fa-times text-5xl"></i>
                            @else
                                <i class="fas fa-hourglass-half text-4xl animate-pulse"></i>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- 3. CONTENT BODY --}}
                <div class="pt-20 pb-10 px-8 text-center">
                    
                    {{-- Status Text --}}
                    <div class="mb-8">
                        @if($status == 'approved')
                            <span class="inline-block py-1 px-3 rounded-full bg-green-100 text-green-700 text-[10px] font-bold uppercase tracking-widest mb-4 border border-green-200">
                                Official Letter
                            </span>
                            <h1 class="text-3xl font-extrabold text-slate-800 mb-2 tracking-tight">Selamat Bergabung!</h1>
                            <p class="text-slate-500 leading-relaxed text-sm max-w-sm mx-auto">
                                Halo <strong class="text-slate-800">{{ auth()->user()->name }}</strong>, profil Anda sangat sesuai dengan misi kami. Selamat datang di tim perubahan.
                            </p>
                        @elseif($status == 'rejected')
                            <span class="inline-block py-1 px-3 rounded-full bg-red-100 text-red-700 text-[10px] font-bold uppercase tracking-widest mb-4 border border-red-200">
                                Notification
                            </span>
                            <h1 class="text-3xl font-extrabold text-slate-800 mb-2 tracking-tight">Mohon Maaf</h1>
                            <p class="text-slate-500 leading-relaxed text-sm max-w-sm mx-auto">
                                Halo <strong class="text-slate-800">{{ auth()->user()->name }}</strong>, terima kasih atas antusiasme Anda. Saat ini kami belum dapat melanjutkan proses Anda.
                            </p>
                        @else
                            <span class="inline-block py-1 px-3 rounded-full bg-blue-100 text-blue-700 text-[10px] font-bold uppercase tracking-widest mb-4 border border-blue-200">
                                In Progress
                            </span>
                            <h1 class="text-3xl font-extrabold text-slate-800 mb-2 tracking-tight">Menunggu Review</h1>
                            <p class="text-slate-500 leading-relaxed text-sm max-w-sm mx-auto">
                                Aplikasi Anda sedang dalam tahap peninjauan oleh tim seleksi kami. Harap menunggu notifikasi selanjutnya.
                            </p>
                        @endif
                    </div>

                    {{-- Detail Grid --}}
                    <div class="bg-slate-50 rounded-2xl p-5 border border-slate-100 mb-8 text-left">
                        <div class="flex items-center gap-4 mb-4 border-b border-slate-200 border-dashed pb-4">
                            <div class="w-12 h-12 rounded-xl bg-white border border-slate-200 p-1 shadow-sm">
                                <img src="{{ $campaign->image ? $campaign->image : 'https://placehold.co/48x48?text=No+Image' }}"
                                     class="w-full h-full object-cover rounded-lg"
                                     onerror="this.onerror=null; this.src='https://placehold.co/48x48?text=No+Image';">
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs text-slate-400 font-bold uppercase mb-0.5">Program Kampanye</p>
                                <p class="text-sm font-bold text-slate-800 truncate">{{ $campaign->judul ?? $campaign->title }}</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-slate-400 font-bold uppercase mb-1">Posisi</p>
                                <p class="text-sm font-semibold text-slate-700 flex items-center gap-1.5">
                                    <i class="fas fa-user-tag text-blue-500 text-xs"></i> 
                                    {{ $application->posisi ?? $application->posisi_dilamar ?? 'Relawan Umum' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 font-bold uppercase mb-1">Tanggal Mulai</p>
                                <p class="text-sm font-semibold text-slate-700 flex items-center gap-1.5">
                                    <i class="far fa-calendar text-blue-500 text-xs"></i>
                                    {{ \Carbon\Carbon::parse($campaign->tanggal_mulai)->translatedFormat('d M Y') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    @if($status == 'approved')
                        <div class="space-y-3">
                            <a href="#" class="block w-full py-3.5 rounded-xl bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-bold text-sm shadow-lg shadow-green-200 transition-all transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                                <i class="fab fa-whatsapp text-lg"></i> Gabung Grup WhatsApp
                            </a>
                            <p class="text-[10px] text-slate-400 mt-2">
                                *Undangan grup berlaku 2x24 jam dari sekarang.
                            </p>
                        </div>
                    @elseif($status == 'rejected')
                        <div class="bg-red-50 rounded-xl p-4 mb-6 border-l-4 border-red-400 text-left">
                            <p class="text-xs text-slate-600 italic">
                                "Kami menyimpan data Anda di database talenta kami. Jika ada program mendatang yang sesuai kualifikasi, Anda akan menjadi prioritas kami."
                            </p>
                        </div>
                        <a href="{{ route('volunteer.campaigns.index') }}" class="block w-full py-3.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-bold text-sm transition-colors">
                            Cari Peluang Lain
                        </a>
                    @endif

                </div>

                {{-- Footer ID --}}
                <div class="bg-slate-50 py-3 border-t border-slate-100 text-center">
                    <p class="text-[10px] text-slate-400 font-mono">
                        REF-ID: APP-{{ str_pad($application->id, 5, '0', STR_PAD_LEFT) }} • {{ date('d/m/Y') }}
                    </p>
                </div>
            </div>

        </div>
    </div>

    {{-- Animation CSS --}}
    <style>
        @keyframes bounce-subtle {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }
        .animate-bounce-subtle {
            animation: bounce-subtle 2s infinite ease-in-out;
        }
    </style>
</x-app>