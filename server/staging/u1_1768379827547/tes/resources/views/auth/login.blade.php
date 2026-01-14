@extends('layouts.topnav-landing')
@section('title','Masuk | Notezque')
@section('content')
<div class="flex justify-center items-center min-h-screen py-12 px-4">
  <div class="w-full max-w-md">
    <!-- Logo/Brand -->
    <div class="text-center mb-10">
      <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-600 to-cyan-600 rounded-2xl mb-4 shadow-lg">
        <i data-lucide="book-open" class="w-8 h-8 text-white"></i>
      </div>
      <h1 class="text-3xl font-bold text-slate-800 mb-2">Selamat Datang Kembali</h1>
      <p class="text-slate-600">Masuk untuk melanjutkan ke Notezque</p>
    </div>

    <!-- Login Card -->
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-8 md:p-10">
      @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center gap-2">
          <i data-lucide="alert-circle" class="w-5 h-5 text-red-600"></i>
          <span class="text-sm text-red-800">{{ $errors->first() }}</span>
        </div>
      @endif

      @if(session('status'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center gap-2">
          <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
          <span class="text-sm text-green-800">{{ session('status') }}</span>
        </div>
      @endif

      <form action="{{ route('login.post') }}" method="POST" class="space-y-6">
        @csrf
        
        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-2">Email</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i data-lucide="mail" class="w-5 h-5 text-slate-400"></i>
            </div>
            <input type="email" name="email" required value="{{ old('email') }}" 
              class="w-full pl-10 pr-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow" 
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
              class="w-full pl-10 pr-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow" 
              placeholder="Masukkan kata sandi">
          </div>
        </div>

        <div class="flex justify-between items-center pt-2">
          <label class="flex items-center">
            <input type="checkbox" class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
            <span class="ml-2 text-sm text-slate-600">Ingat saya</span>
          </label>
          <a href="{{ route('forgot') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium hover:underline">Lupa kata sandi?</a>
        </div>

        <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-cyan-600 text-white py-3.5 rounded-lg font-semibold hover:from-blue-700 hover:to-cyan-700 transition-all shadow-lg hover:shadow-xl transform hover:scale-[1.02]">
          Masuk ke Akun
        </button>
      </form>
    </div>

    <!-- Register Link -->
    <p class="text-sm text-center text-slate-600 mt-8">
      Belum punya akun? <a href="{{ route('register') }}" class="text-blue-600 font-semibold hover:text-blue-700 hover:underline">Daftar Sekarang</a>
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
