@extends('layouts.topnav-landing')

@section('title', 'Notezque V4 - Solusi Produktivitas Akademik')

@section('content')
    <div class="relative z-10">
        <!-- Bagian 1: Hero Section -->
        <section
            class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-16 md:pt-8 md:pb-8 flex flex-col md:flex-row items-center justify-between gap-12">

            <!-- Teks Hero -->
            <div class="md:w-3/5 text-center md:text-left animate-on-scroll fade-in-left">
                @php
                    $heroTitle = \App\Models\KontenStatis::where('key', 'hero_title')->first();
                    $heroDesc = \App\Models\KontenStatis::where('key', 'hero_description')->first();
                @endphp
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-50 to-cyan-50 border border-blue-200/50 rounded-full mb-6">
                    <span class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></span>
                    <p class="text-sm font-semibold bg-gradient-to-r from-blue-600 to-blue-700 bg-clip-text text-transparent tracking-wide">
                        Notezque - Asisten Akademik Anda
                    </p>
                </div>
                
                <h1 class="text-5xl sm:text-6xl lg:text-7xl font-extrabold leading-[1.1] mb-6">
                    @if($heroTitle)
                        {!! nl2br(e($heroTitle->value)) !!}
                    @else
                        Kelola <span class="bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent">Tugas Kuliah</span><br>
                        <span class="text-slate-800">Tanpa Stres</span>
                    @endif
                </h1>
                
                <p class="text-xl text-slate-600 max-w-2xl mx-auto md:mx-0 mb-10 leading-relaxed">
                    {{ $heroDesc ? $heroDesc->value : 'Platform digital terpusat yang dirancang khusus untuk mahasiswa agar aktivitas akademik menjadi terorganisir, fleksibel, dan terfokus.' }}
                </p>

                <div class="flex flex-col sm:flex-row justify-center md:justify-start gap-4 mb-12">
                    <!-- ✅ tombol ke halaman register -->
                    <a href="{{ url('/register') }}"
                        class="group px-8 py-4 text-lg font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-700 rounded-2xl shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-300 flex items-center justify-center gap-2">
                        Mulai Sekarang (Gratis)
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>

                    <!-- tombol lihat fitur -->
                    <a href="#fitur"
                        class="group px-8 py-4 text-lg font-semibold text-slate-700 bg-white border-2 border-slate-200 rounded-2xl hover:border-blue-300 hover:bg-blue-50/50 transition-all duration-300 flex items-center justify-center gap-2">
                        Lihat Fitur
                        <svg class="w-5 h-5 group-hover:translate-y-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </a>
                </div>
                
                <!-- Stats Mini -->
                <div class="flex justify-center md:justify-start gap-8 text-sm">
                    <div class="text-center md:text-left">
                        <p class="text-2xl font-bold text-slate-800">1000+</p>
                        <p class="text-slate-500">Pengguna Aktif</p>
                    </div>
                    <div class="text-center md:text-left">
                        <p class="text-2xl font-bold text-slate-800">50K+</p>
                        <p class="text-slate-500">Tugas Diselesaikan</p>
                    </div>
                    <div class="text-center md:text-left">
                        <p class="text-2xl font-bold text-slate-800">4.9★</p>
                        <p class="text-slate-500">Rating Pengguna</p>
                    </div>
                </div>
            </div>

            <!-- Ilustrasi Hero -->
            <div class="md:w-2/5 mt-12 md:mt-0 flex justify-center relative animate-on-scroll fade-in-right">
                <div class="relative w-full max-w-md">
                    <!-- Main Card -->
                    <div class="w-full bg-gradient-to-br from-white via-blue-50/30 to-cyan-50/30 backdrop-blur-sm rounded-3xl p-8 border border-white/60 shadow-2xl relative overflow-hidden group hover:shadow-blue-200/50 transition-all duration-500">
                        <!-- Animated Background Gradient -->
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-400/10 via-cyan-400/10 to-sky-400/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        
                        <!-- Content -->
                        <div class="relative z-10">
                            <!-- Icon Badge -->
                            <div class="inline-flex p-4 bg-gradient-to-br from-blue-500 to-blue-700 rounded-2xl shadow-lg mb-6">
                                <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            
                            <h3 class="text-2xl font-bold text-slate-800 mb-3">Fokus di Perkuliahan</h3>
                            <p class="text-slate-600 mb-6 leading-relaxed">Dapatkan pengingat deadline yang akurat dan kelola semua tugas dalam satu platform.</p>
                            
                            <!-- Mini Stats -->
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 border border-slate-200/50">
                                    <p class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent">95%</p>
                                    <p class="text-xs text-slate-500 mt-1">Task Completion</p>
                                </div>
                                <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 border border-slate-200/50">
                                    <p class="text-2xl font-bold bg-gradient-to-r from-blue-700 to-sky-600 bg-clip-text text-transparent">24/7</p>
                                    <p class="text-xs text-slate-500 mt-1">Akses Penuh</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Floating Elements -->
                    <div class="absolute -top-6 -right-6 w-32 h-32 bg-gradient-to-br from-blue-400 to-cyan-500 rounded-3xl opacity-20 blur-2xl animate-pulse"></div>
                    <div class="absolute -bottom-6 -left-6 w-32 h-32 bg-gradient-to-br from-cyan-400 to-sky-500 rounded-3xl opacity-20 blur-2xl animate-pulse" style="animation-delay: 1s;"></div>
                    
                    <!-- Small Floating Cards -->
                    <div class="absolute -top-4 -left-4 bg-white rounded-xl shadow-lg p-3 border border-slate-200/50 hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <div class="absolute -bottom-4 -right-4 bg-white rounded-xl shadow-lg p-3 border border-slate-200/50 hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </section>

        <!-- Bagian 2: Fitur Utama -->
        <section id="fitur" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-6">
            <div class="animate-on-scroll fade-in-up text-center mb-16">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-50 to-cyan-50 border border-blue-200/50 rounded-full mb-6">
                    <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                    <span class="text-sm font-semibold text-blue-600">Fitur Unggulan</span>
                </div>
                
                <h2 class="text-4xl md:text-5xl font-extrabold mb-6">
                    <span class="bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent">Fitur Inti</span> <span class="text-slate-800">Notezque</span>
                </h2>
                <p class="text-xl text-slate-600 max-w-3xl mx-auto leading-relaxed">
                    Alat lengkap yang dirancang khusus untuk meningkatkan produktivitas akademik Anda ke level tertinggi.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                <!-- Kartu 1: Kelola Tugas -->
                <div class="group relative animate-on-scroll fade-in-up card-stagger-1">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-cyan-400 rounded-3xl blur-xl opacity-0 group-hover:opacity-30 transition-opacity duration-500"></div>
                    <div class="relative p-8 rounded-3xl bg-white/80 backdrop-blur-sm shadow-xl hover:shadow-2xl transition-all duration-500 border border-blue-200/50 hover:border-blue-300 hover:-translate-y-2">
                        <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-2xl flex items-center justify-center mb-6 shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M10 16h.01"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-3 text-slate-800">Kelola Tugas</h3>
                        <p class="text-slate-600 mb-6 leading-relaxed">
                            Fitur CRUD lengkap untuk mencatat, mengatur deadline, dan memantau progress tugas kuliah Anda di satu tempat.
                        </p>
                        <a href="#" class="inline-flex items-center gap-2 text-sm font-semibold text-blue-600 hover:text-blue-700 transition group-hover:gap-3">
                            Pelajari Lebih Lanjut
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Kartu 2: Kalender Aktivitas -->
                <div class="group relative animate-on-scroll fade-in-up card-stagger-2">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-cyan-400 rounded-3xl blur-xl opacity-0 group-hover:opacity-30 transition-opacity duration-500"></div>
                    <div class="relative p-8 rounded-3xl bg-white/80 backdrop-blur-sm shadow-xl hover:shadow-2xl transition-all duration-500 border border-blue-200/50 hover:border-blue-300 hover:-translate-y-2">
                        <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-2xl flex items-center justify-center mb-6 shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-3 text-slate-800">Kalender Aktivitas</h3>
                        <p class="text-slate-600 mb-6 leading-relaxed">
                            Kalender interaktif untuk menambahkan, mengedit, dan melihat kegiatan harian, mingguan, atau bulanan Anda secara intuitif.
                        </p>
                        <a href="#" class="inline-flex items-center gap-2 text-sm font-semibold text-blue-600 hover:text-blue-700 transition group-hover:gap-3">
                            Pelajari Lebih Lanjut
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Kartu 3: Manajemen File -->
                <div class="group relative animate-on-scroll fade-in-up card-stagger-3">
                    <div class="absolute inset-0 bg-gradient-to-r from-sky-400 to-blue-500 rounded-3xl blur-xl opacity-0 group-hover:opacity-30 transition-opacity duration-500"></div>
                    <div class="relative p-8 rounded-3xl bg-white/80 backdrop-blur-sm shadow-xl hover:shadow-2xl transition-all duration-500 border border-sky-200/50 hover:border-sky-300 hover:-translate-y-2">
                        <div class="w-14 h-14 bg-gradient-to-br from-sky-500 to-blue-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-3 text-slate-800">Manajemen File</h3>
                        <p class="text-slate-600 mb-6 leading-relaxed">
                            Menyimpan link/file tugas dan materi berdasarkan kategori untuk arsip sumber belajar yang rapi dan mudah diakses.
                        </p>
                        <a href="#" class="inline-flex items-center gap-2 text-sm font-semibold text-sky-600 hover:text-sky-700 transition group-hover:gap-3">
                            Pelajari Lebih Lanjut
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Bagian 3: Alur Kerja -->
        <section id="alur" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-6 bg-gradient-to-b from-slate-50/50 to-white rounded-[3rem]">
            <div class="animate-on-scroll fade-in-up text-center mb-20">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-50 to-sky-50 border border-blue-200/50 rounded-full mb-6">
                    <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm font-semibold text-blue-600">Proses Sederhana</span>
                </div>
                
                <h2 class="text-4xl md:text-5xl font-extrabold mb-6">
                    <span class="bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent">Alur Kerja</span> <span class="text-slate-800">Kami</span>
                </h2>
                <p class="text-xl text-slate-600 max-w-3xl mx-auto leading-relaxed">
                    Tiga langkah sederhana untuk mengubah cara Anda mengatur tugas dan aktivitas akademik.
                </p>
            </div>

            <div class="flex flex-col md:flex-row items-center justify-between">
                <!-- Ilustrasi Alur Kerja (Sesuai image_ee6241.jpg - Lebih Sederhana) -->
                <div class="md:w-1/2 flex justify-center mb-12 md:mb-0 relative animate-on-scroll fade-in-left">
                    <!-- Placeholder Ilustrasi Workflow dengan Pastel Circle dan Line Art -->
                    <div class="w-full max-w-md h-72 rounded-2xl flex items-center justify-center relative">
                        <svg class="w-full h-full" viewBox="0 0 300 200" xmlns="http://www.w3.org/2000/svg">
                            <!-- Background Circles -->
                            <circle cx="250" cy="50" r="30" fill="#FFC0CB" opacity="0.3" />
                            <circle cx="50" cy="150" r="40" fill="#8C9EFF" opacity="0.3" />

                            <!-- Main Line Flow -->
                            <path d="M 70 50 C 150 0, 250 0, 230 50" fill="none" stroke="#A8A8E6" stroke-width="2"
                                stroke-dasharray="5,5" />
                            <path d="M 230 50 C 250 100, 150 100, 70 150" fill="none" stroke="#A8A8E6" stroke-width="2"
                                stroke-dasharray="5,5" />

                            <!-- Step Icons -->
                            <circle cx="70" cy="50" r="15" fill="#8C9EFF" />
                            <text x="70" y="55" text-anchor="middle" font-size="12" fill="white" font-weight="bold">1</text>

                            <circle cx="230" cy="50" r="15" fill="#FFC0CB" />
                            <text x="230" y="55" text-anchor="middle" font-size="12" fill="white"
                                font-weight="bold">2</text>

                            <circle cx="70" cy="150" r="15" fill="#A8A8E6" />
                            <text x="70" y="155" text-anchor="middle" font-size="12" fill="white"
                                font-weight="bold">3</text>

                            <!-- Line Art Elements (Illustrative objects) -->
                            <rect x="235" y="130" width="50" height="40" rx="5" fill="#FFFFFF" stroke="#8C9EFF"
                                stroke-width="1" />
                            <path d="M 235 150 L 285 150" stroke="#8C9EFF" stroke-width="1" />
                            <path d="M 250 130 V 170" stroke="#8C9EFF" stroke-width="1" />
                        </svg>
                    </div>
                </div>

                <!-- Daftar Alur Kerja -->
                <div class="md:w-1/2 space-y-8 md:pl-16 animate-on-scroll fade-in-right">
                    <!-- Step 1: Login & Setup -->
                    <div class="flex items-start transition-all duration-300 hover:translate-x-2">
                        <div
                            class="w-10 h-10 flex items-center justify-center text-lg font-bold rounded-full bg-v4-primary text-white flex-shrink-0 mr-4 shadow-md">
                            01</div>
                        <div>
                            <h3 class="text-xl font-bold text-v4-text mb-1">Daftar & Personalisasi</h3>
                            <p class="text-gray-600">Buat akun Anda, login, dan atur preferensi notifikasi serta kategori
                                mata kuliah Anda.</p>
                        </div>
                    </div>

                    <!-- Step 2: Input & Schedule -->
                    <div class="flex items-start transition-all duration-300 hover:translate-x-2">
                        <div
                            class="w-10 h-10 flex items-center justify-center text-lg font-bold rounded-full bg-v4-pink text-white flex-shrink-0 mr-4 shadow-md">
                            02</div>
                        <div>
                            <h3 class="text-xl font-bold text-v4-text mb-1">Input Tugas & Jadwal</h3>
                            <p class="text-gray-600">Masukkan semua tugas dan kegiatan ke Task Manager dan Kalender
                                Aktivitas dengan deadline yang jelas.</p>
                        </div>
                    </div>

                    <!-- Step 3: Monitor & Collaborate -->
                    <div class="flex items-start transition-all duration-300 hover:translate-x-2">
                        <div
                            class="w-10 h-10 flex items-center justify-center text-lg font-bold rounded-full bg-v4-secondary text-white flex-shrink-0 mr-4 shadow-md">
                            03</div>
                        <div>
                            <h3 class="text-xl font-bold text-v4-text mb-1">Monitor & Kolaborasi</h3>
                            <p class="text-gray-600">Pantau progres di Dashboard, dapatkan pengingat, dan undang teman untuk
                                tugas kelompok.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Bagian 4: Call to Action -->
        <section class="mt-16 mb-24 animate-on-scroll fade-in-up">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="relative overflow-hidden rounded-[3rem] bg-gradient-to-br from-blue-600 via-blue-700 to-cyan-600 p-1">
                    <div class="relative bg-gradient-to-br from-blue-50 via-cyan-50 to-sky-50 rounded-[2.85rem] px-8 py-20 md:px-16 md:py-24 text-center overflow-hidden">
                        <!-- Decorative Elements -->
                        <div class="absolute top-0 right-0 w-64 h-64 bg-blue-400 rounded-full blur-3xl opacity-20 -translate-y-1/2 translate-x-1/2"></div>
                        <div class="absolute bottom-0 left-0 w-64 h-64 bg-cyan-400 rounded-full blur-3xl opacity-20 translate-y-1/2 -translate-x-1/2"></div>
                        
                        <div class="relative z-10">
                            <h2 class="text-4xl md:text-5xl lg:text-6xl font-extrabold mb-6">
                                Siap Mengubah <br><span class="bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent">Gaya Belajar</span> Anda?
                            </h2>
                            <p class="text-xl text-slate-700 mb-12 max-w-3xl mx-auto leading-relaxed">
                                Notezque adalah solusi <span class="font-semibold">terintegrasi, fleksibel, dan efisien</span> yang dirancang untuk kebutuhan akademis mahasiswa modern.
                            </p>

                            <div class="flex flex-col sm:flex-row justify-center gap-4">
                                <!-- ✅ tombol CTA ke register -->
                                <a href="{{ url('/register') }}"
                                   class="group px-10 py-5 text-xl font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-700 rounded-2xl shadow-2xl hover:shadow-blue-500/50 hover:scale-105 transition-all duration-300 flex items-center justify-center gap-3">
                                    Mulai Sekarang (Gratis)
                                    <svg class="w-6 h-6 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                    </svg>
                                </a>
                                
                                <a href="#fitur"
                                   class="px-10 py-5 text-xl font-semibold text-slate-700 bg-white rounded-2xl shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300">
                                    Lihat Demo
                                </a>
                            </div>
                            
                            <!-- Trust Badges -->
                            <div class="flex justify-center items-center gap-8 mt-12 flex-wrap">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-sm font-medium text-slate-600">100% Gratis</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                    </svg>
                                    <span class="text-sm font-medium text-slate-600">Tanpa Kartu Kredit</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-cyan-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-sm font-medium text-slate-600">Setup Instant</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
