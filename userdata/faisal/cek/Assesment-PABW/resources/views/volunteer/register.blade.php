<x-app title="Daftar Relawan - {{ $campaign->judul }}">
    {{-- CDN SweetAlert2 (Wajib ada untuk popup) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="min-h-screen bg-slate-50 py-12">
        <div class="container mx-auto px-6 max-w-3xl">

            {{-- Tombol Kembali --}}
            <a href="{{ route('volunteer.campaigns.index') }}" class="inline-flex items-center text-slate-500 hover:text-indigo-600 mb-6 transition-colors font-medium">
                <i class="fas fa-arrow-left mr-2"></i> Batalkan & Kembali
            </a>

            {{-- Tampilkan Error Global jika ada --}}
            @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-xl shadow-sm">
                <p class="font-bold flex items-center gap-2"><i class="fas fa-exclamation-circle"></i> Ada kesalahan pada input Anda:</p>
                <ul class="list-disc ml-5 text-sm mt-2">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-slate-100 relative">

                {{-- Header Kampanye --}}
                <div class="bg-gradient-to-r from-indigo-600 to-blue-600 p-8 text-white relative overflow-hidden">
                    <div class="absolute top-0 right-0 opacity-10 transform translate-x-4 -translate-y-4">
                        <i class="fas fa-hand-holding-heart text-[10rem]"></i>
                    </div>
                    <div class="relative z-10">
                        <h2 class="text-3xl font-bold mb-2 tracking-tight">Formulir Pendaftaran Relawan</h2>
                        <p class="opacity-90 text-indigo-100">Anda mendaftar untuk program:</p>
                        <p class="font-bold text-yellow-300 text-xl mt-1 border-b-2 border-yellow-300/30 inline-block pb-1">{{ $campaign->judul }}</p>
                    </div>
                </div>

                {{-- Campaign Image --}}
                @if($campaign->image)
                <div class="relative -mt-12 mx-8">
                    <div class="bg-white rounded-xl shadow-lg p-2">
                        <img src="{{ $campaign->image }}"
                             alt="{{ $campaign->judul }}"
                             class="w-full h-48 object-cover rounded-lg"
                             onerror="this.onerror=null; this.src='https://placehold.co/600x200?text=No+Image';">
                    </div>
                </div>
                @endif

                {{-- Form Body --}}
                <div class="p-8 md:p-10">
                    <form id="registrationForm" action="{{ route('volunteer.register.store', $campaign->slug) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        {{-- Info User Otomatis --}}
                        <div class="bg-blue-50/80 p-5 rounded-2xl border border-blue-100 flex items-start gap-4 shadow-sm">
                            <div class="bg-blue-100 p-2 rounded-full text-blue-600">
                                <i class="fas fa-user text-xl"></i>
                            </div>
                            <div>
                                <p class="text-xs text-blue-500 font-bold uppercase tracking-wider mb-1">Data Akun</p>
                                <p class="text-base text-slate-800 font-bold">{{ Auth::user()->name }}</p>
                                <p class="text-sm text-slate-600">{{ Auth::user()->email }}</p>
                            </div>
                        </div>

                        {{-- Posisi --}}
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Posisi yang Dilamar <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <select name="posisi_dilamar" class="appearance-none w-full px-4 py-3.5 rounded-xl border {{ $errors->has('posisi_dilamar') ? 'border-red-500' : 'border-slate-300' }} focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 bg-white transition-all outline-none cursor-pointer text-slate-700" required>
                                    <option value="" disabled selected>Pilih posisi...</option>
                                    <option value="Lapangan" {{ old('posisi_dilamar') == 'Lapangan' ? 'selected' : '' }}>Tim Lapangan (Fisik)</option>
                                    <option value="Medis" {{ old('posisi_dilamar') == 'Medis' ? 'selected' : '' }}>Tim Medis / P3K</option>
                                    <option value="Logistik" {{ old('posisi_dilamar') == 'Logistik' ? 'selected' : '' }}>Logistik & Dapur Umum</option>
                                    <option value="Dokumentasi" {{ old('posisi_dilamar') == 'Dokumentasi' ? 'selected' : '' }}>Dokumentasi & Media</option>
                                    <option value="Pendidikan" {{ old('posisi_dilamar') == 'Pendidikan' ? 'selected' : '' }}>Pengajar / Edukator</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-slate-500">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                            @error('posisi_dilamar')
                            <p class="text-xs text-red-500 mt-1 font-bold"><i class="fas fa-times-circle"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Alamat --}}
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Alamat Domisili Saat Ini <span class="text-red-500">*</span></label>
                            <input type="text" name="alamat"
                                value="{{ old('alamat') }}"
                                class="w-full px-4 py-3.5 rounded-xl border {{ $errors->has('alamat') ? 'border-red-500' : 'border-slate-300' }} focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none"
                                placeholder="Jalan, Kelurahan, Kota..." required>
                            @error('alamat')
                            <p class="text-xs text-red-500 mt-1 font-bold"><i class="fas fa-times-circle"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Alasan Bergabung --}}
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Mengapa Anda ingin bergabung? <span class="text-red-500">*</span></label>
                            <textarea name="alasan_bergabung" rows="5"
                                class="w-full px-4 py-3.5 rounded-xl border {{ $errors->has('alasan_bergabung') ? 'border-red-500' : 'border-slate-300' }} focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none"
                                placeholder="Ceritakan motivasi dan pengalaman relevan Anda..." required>{{ old('alasan_bergabung') }}</textarea>
                            <div class="flex justify-between mt-1">
                                <p class="text-xs text-slate-400">Minimal 20 karakter.</p>
                                @error('alasan_bergabung')
                                <p class="text-xs text-red-500 font-bold"><i class="fas fa-times-circle"></i> {{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Upload CV (LOGIKA JS DITERAPKAN DI SINI) --}}
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Upload CV / Resume <span class="text-red-500">*</span></label>
                            
                            {{-- Area Upload yang Dimodifikasi --}}
                            <div id="uploadBox" class="relative border-2 border-dashed {{ $errors->has('cv') ? 'border-red-500 bg-red-50' : 'border-slate-300 bg-slate-50' }} rounded-xl p-8 text-center hover:bg-slate-100 hover:border-indigo-400 transition-all duration-300 cursor-pointer group">
                                
                                {{-- Input File Tersembunyi tapi menutupi area --}}
                                <input type="file" id="cvInput" name="cv" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" accept=".pdf" required onchange="handleFileUpload(this)">
                                
                                {{-- Default State --}}
                                <div id="uploadDefault" class="pointer-events-none transition-opacity duration-300">
                                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-3 shadow-sm group-hover:scale-110 transition-transform">
                                        <i class="fas fa-cloud-upload-alt text-3xl text-indigo-500"></i>
                                    </div>
                                    <p class="text-sm text-slate-700 font-bold mb-1">Klik untuk upload file PDF</p>
                                    <p class="text-xs text-slate-400">Maksimal ukuran file 2MB</p>
                                </div>

                                {{-- Success State (Hidden by Default) --}}
                                <div id="uploadSuccess" class="hidden pointer-events-none transition-opacity duration-300">
                                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3 shadow-sm animate-bounce">
                                        <i class="fas fa-file-pdf text-3xl text-green-600"></i>
                                    </div>
                                    <p class="text-sm text-green-700 font-bold mb-1">File Terpilih:</p>
                                    <p id="fileName" class="text-sm text-slate-800 font-medium truncate max-w-[200px] mx-auto">nama_file.pdf</p>
                                    <p class="text-xs text-indigo-500 mt-2 font-bold hover:underline">Klik untuk ganti file</p>
                                </div>

                            </div>
                            @error('cv')
                            <p class="text-xs text-red-500 mt-1 font-bold"><i class="fas fa-times-circle"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Submit --}}
                        <div class="pt-6">
                            <button type="submit" id="submitBtn" class="w-full py-4 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-500/30 transform hover:-translate-y-1 transition-all flex items-center justify-center gap-2">
                                <span>Kirim Pendaftaran</span>
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

   {{-- SCRIPT JAVASCRIPT KHUSUS HALAMAN INI --}}
<script>
    // 1. Logic Upload File (WAJIB ADA: Supaya tampilan upload berubah saat file dipilih)
    function handleFileUpload(input) {
        const box = document.getElementById('uploadBox');
        const defaultView = document.getElementById('uploadDefault');
        const successView = document.getElementById('uploadSuccess');
        const fileName = document.getElementById('fileName');

        if (input.files && input.files[0]) {
            fileName.textContent = input.files[0].name;
            box.classList.remove('border-slate-300', 'bg-slate-50');
            box.classList.add('border-indigo-400', 'bg-indigo-50');
            defaultView.classList.add('hidden');
            successView.classList.remove('hidden');
        } else {
            box.classList.add('border-slate-300', 'bg-slate-50');
            box.classList.remove('border-indigo-400', 'bg-indigo-50');
            defaultView.classList.remove('hidden');
            successView.classList.add('hidden');
        }
    }

    // 2. Loading State (WAJIB ADA: Supaya user tidak klik 2x saat submit)
    const form = document.getElementById('registrationForm');
    const btn = document.getElementById('submitBtn');

    if(form && btn) {
        form.addEventListener('submit', function() {
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Sedang Mengirim...';
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            btn.disabled = true;
        });
    }
</script>
</x-app>