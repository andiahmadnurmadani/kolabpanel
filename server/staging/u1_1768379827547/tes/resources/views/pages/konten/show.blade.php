@extends('layouts.app')

@section('title', ucwords(str_replace('_', ' ', $konten->key)))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('konten.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                ← Kembali ke Daftar Konten
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-8">
            <div class="mb-4">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    {{ ucwords(str_replace('_', ' ', $konten->key)) }}
                </h1>
                <span class="inline-block mt-2 px-3 py-1 text-xs font-medium rounded-full {{ $konten->type === 'image' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                    {{ $konten->type }}
                </span>
            </div>

            <div class="mt-6">
                @if($konten->type === 'image')
                    <img src="{{ $konten->value }}" 
                         alt="{{ $konten->key }}" 
                         class="max-w-full rounded-lg shadow-lg">
                @else
                    <div class="prose dark:prose-invert max-w-none">
                        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap text-lg leading-relaxed">
                            {{ $konten->value }}
                        </p>
                    </div>
                @endif
            </div>

            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Terakhir diperbarui: {{ $konten->updated_at->diffForHumans() }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
