@extends('layouts.topnav-landing')

@section('title', 'Lupa Kata Sandi | Notezque')

@section('content')
<div class="flex justify-center items-center min-h-[80vh]">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md">
        <h2 class="text-2xl font-bold text-center text-v4-text mb-2">Lupa Kata Sandi?</h2>
        <p class="text-center text-gray-500 mb-6">Masukkan email kamu untuk verifikasi.</p>

        @if (session('error'))
            <div class="bg-red-100 text-red-600 px-4 py-2 rounded-lg mb-4 text-sm">{{ session('error') }}</div>
        @endif
        @if (session('info'))
            <div class="bg-blue-100 text-blue-600 px-4 py-2 rounded-lg mb-4 text-sm">{{ session('info') }}</div>
        @endif

        <form action="{{ route('forgot.post') }}" method="POST">
            @csrf
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-600 mb-1">Email</label>
                <input type="email" name="email" required
                    class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-v4-primary focus:outline-none">
            </div>

            <button type="submit"
                class="w-full py-3 rounded-lg bg-v4-primary text-white font-semibold hover:opacity-90 transition">
                Kirim Verifikasi
            </button>
        </form>

        <div class="text-center mt-4">
            <a href="/login" class="text-v4-primary text-sm hover:underline">Kembali ke Login</a>
        </div>
    </div>
</div>
@endsection
