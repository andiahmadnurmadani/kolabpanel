@extends('layouts.topnav-landing')

@section('title', 'Daftar Akun | Notezque')

@section('content')
<div class="flex justify-center items-center min-h-screen py-12 px-4">
  <div class="w-full max-w-md">
    <!-- Logo/Brand -->
    <div class="text-center mb-10">
      <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-cyan-600 to-blue-600 rounded-2xl mb-4 shadow-lg">
        <i data-lucide="user-plus" class="w-8 h-8 text-white"></i>
      </div>
      <h1 class="text-3xl font-bold text-slate-800 mb-2">Mulai Bersama Notezque</h1>
      <p class="text-slate-600">Buat akun untuk memulai perjalanan belajar Anda</p>
    </div>

    <!-- Register Card -->
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-8 md:p-10">
      <form action="{{ route('register.post') }}" method="POST" class="space-y-6">
        @csrf
        
        @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg text-sm">
          <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        @endif

        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Lengkap</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i data-lucide="user" class="w-5 h-5 text-slate-400"></i>
            </div>
            <input type="text" name="name" required value="{{ old('name') }}"
              class="w-full pl-10 pr-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-shadow" 
              placeholder="Masukkan nama lengkap">
          </div>
        </div>

        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-2">Email</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i data-lucide="mail" class="w-5 h-5 text-slate-400"></i>
            </div>
            <input type="email" name="email" required value="{{ old('email') }}"
              class="w-full pl-10 pr-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-shadow" 
              placeholder="nama@email.com">
          </div>
        </div>

        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-2">Kata Sandi</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i data-lucide="lock" class="w-5 h-5 text-slate-400"></i>
            </div>
            <input type="password" name="password" required 
              class="w-full pl-10 pr-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-shadow" 
              placeholder="Minimal 6 karakter">
          </div>
        </div>

        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-2">Konfirmasi Kata Sandi</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i data-lucide="lock" class="w-5 h-5 text-slate-400"></i>
            </div>
            <input type="password" name="password_confirmation" required 
              class="w-full pl-10 pr-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-shadow" 
              placeholder="Ulangi kata sandi">
          </div>
        </div>

        <div class="flex items-start pt-2">
          <input type="checkbox" required class="w-4 h-4 mt-0.5 text-cyan-600 border-slate-300 rounded focus:ring-cyan-500">
          <label class="ml-3 text-sm text-slate-600">
            Saya setuju dengan <a href="#" class="text-cyan-600 hover:text-cyan-700 hover:underline font-medium">Syarat & Ketentuan</a> dan <a href="#" class="text-cyan-600 hover:text-cyan-700 hover:underline font-medium">Kebijakan Privasi</a>
          </label>
        </div>

        <button type="submit" class="w-full bg-gradient-to-r from-cyan-600 to-blue-600 text-white py-3.5 rounded-lg font-semibold hover:from-cyan-700 hover:to-blue-700 transition-all shadow-lg hover:shadow-xl transform hover:scale-[1.02]">
          Buat Akun
        </button>
      </form>
    </div>

    <!-- Login Link -->
    <p class="text-sm text-center text-slate-600 mt-8">
      Sudah punya akun? <a href="{{ url('/login') }}" class="text-cyan-600 font-semibold hover:text-cyan-700 hover:underline">Masuk di sini</a>
    </p>
  </div>
</div>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    if (typeof lucide !== 'undefined') lucide.createIcons();
  });
</script>
@endsection
