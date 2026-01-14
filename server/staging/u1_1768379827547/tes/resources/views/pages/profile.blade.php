@extends('layouts.main-nav')
@section('title', 'Profil Saya')
@section('subtitle','Kelola informasi profil Anda')

@section('content')
<div class="min-h-screen">
    <main class="flex-grow bg-white rounded-tl-3xl p-6 md:p-10">
        <div class="max-w-5xl mx-auto">

            <!-- Profile Card -->
            <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-2xl p-8 mb-6 border border-blue-100">
                <div class="flex flex-col md:flex-row items-center md:items-start gap-6">

                    <!-- FOTO PROFIL + MODAL -->
                    <div x-data="{ open: false }" class="relative">

                        <!-- Foto -->
                        <div 
                            class="relative group cursor-pointer"
                            @click="open = true"
                        >
                            <img 
                                src="{{ $user->profile_photo ? asset('storage/' . $user->profile_photo) : 'https://placehold.co/120x120/6366F1/FFFFFF?text=' . strtoupper(substr($user->name, 0, 1)) }}" 
                                alt="Profile Picture" 
                                class="w-32 h-32 rounded-2xl border-4 border-white shadow-xl object-cover"
                            >

                            <!-- Hover icon -->
                            <div
                                class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 
                                       flex items-center justify-center rounded-2xl transition"
                            >
                                <i data-lucide="camera" class="text-white w-6 h-6"></i>
                            </div>
                        </div>

                        <!-- Modal -->
                        <div 
                            x-show="open"
                            x-transition
                            class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50"
                        >
                            <div class="bg-white rounded-2xl p-6 w-80 shadow-xl">

                                <h2 class="text-lg font-bold text-slate-800 mb-4 text-center">
                                    Ubah Foto Profil
                                </h2>

                                <!-- Upload -->
                                <label 
                                    for="uploadPhoto"
                                    class="block w-full text-center bg-blue-600 text-white py-2 rounded-lg font-medium hover:bg-blue-700 transition cursor-pointer mb-3"
                                >
                                    Pilih Foto Baru
                                </label>

                                <!-- Hapus foto -->
                                @if ($user->profile_photo)
                                <form action="{{ route('profile.deletePhoto') }}" method="POST" class="mb-3">
                                    @csrf
                                    <button
                                        type="submit"
                                        class="w-full bg-red-50 text-red-600 py-2 rounded-lg font-medium hover:bg-red-100 transition"
                                    >
                                        Hapus Foto
                                    </button>
                                </form>
                                @endif

                                <!-- Batal -->
                                <button
                                    @click="open = false"
                                    class="w-full bg-slate-100 text-slate-700 py-2 rounded-lg font-medium hover:bg-slate-200 transition"
                                >
                                    Batal
                                </button>
                            </div>
                        </div>
                        <form 
                            action="{{ route('profile.updatePhoto') }}" 
                            method="POST" 
                            enctype="multipart/form-data" 
                            class="hidden"
                        >
                            @csrf
                            <input type="file" id="uploadPhoto" name="profile_photo" accept="image/*"
                                onchange="this.form.submit()">
                        </form>

                    </div>

                    <!-- USER INFO -->
                    <div class="flex-1 text-center md:text-left">
                        <h2 class="text-3xl font-bold text-slate-800 mb-2">{{ $user->name }}</h2>
                        <p class="text-slate-600 mb-4 flex items-center justify-center md:justify-start gap-2">
                            <i data-lucide="mail" class="w-4 h-4"></i>
                            {{ $user->email }}
                        </p>
                        
                        <div class="flex flex-wrap gap-2 justify-center md:justify-start">
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium">Mahasiswa</span>
                            <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm font-medium">Active Member</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="p-5 rounded-xl bg-white border border-slate-200 hover:shadow-md transition">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="calendar" class="w-5 h-5 text-blue-600"></i>
                        </div>
                        <p class="text-xs text-slate-500 font-medium uppercase">Bergabung</p>
                    </div>
                    <p class="text-lg font-bold text-slate-800">{{ $user->created_at->format('d M Y') }}</p>
                </div>
                
                <div class="p-5 rounded-xl bg-white border border-slate-200 hover:shadow-md transition">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="file-text" class="w-5 h-5 text-purple-600"></i>
                        </div>
                        <p class="text-xs text-slate-500 font-medium uppercase">Catatan</p>
                    </div>
                    <p class="text-2xl font-bold text-slate-800">{{ $user->notes()->count() }}</p>
                </div>
                
                <div class="p-5 rounded-xl bg-white border border-slate-200 hover:shadow-md transition">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="check-square" class="w-5 h-5 text-green-600"></i>
                        </div>
                        <p class="text-xs text-slate-500 font-medium uppercase">Tugas</p>
                    </div>
                    <p class="text-2xl font-bold text-slate-800">{{ $user->tasks()->count() }}</p>
                </div>
                
                <div class="p-5 rounded-xl bg-white border border-slate-200 hover:shadow-md transition">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="calendar-days" class="w-5 h-5 text-orange-600"></i>
                        </div>
                        <p class="text-xs text-slate-500 font-medium uppercase">Acara</p>
                    </div>
                    <p class="text-2xl font-bold text-slate-800">{{ $user->activities()->count() }}</p>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-xl border border-slate-200 p-6">
                <h3 class="text-lg font-bold text-slate-800 mb-4">Pengaturan Akun</h3>
                <div class="flex flex-wrap gap-3">
                    <a href="/change-password" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition flex items-center gap-2">
                        <i data-lucide="lock" class="w-4 h-4"></i>
                        <span>Ganti Kata Sandi</span>
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-5 py-2.5 bg-red-50 text-red-600 rounded-lg font-medium hover:bg-red-100 transition flex items-center gap-2">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                            <span>Keluar</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    if (typeof lucide !== 'undefined') lucide.createIcons();
});
</script>

@endsection
