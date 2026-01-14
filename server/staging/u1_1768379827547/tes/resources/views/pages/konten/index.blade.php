@extends('layouts.app')

@section('title', 'Konten Statis')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">Konten Statis</h1>

        @if($konten->isEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-8 text-center">
                <p class="text-gray-500 dark:text-gray-400">Belum ada konten tersedia.</p>
            </div>
        @else
            <div class="grid gap-4">
                @foreach($konten as $item)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-white">
                                    {{ ucwords(str_replace('_', ' ', $item->key)) }}
                                </h3>
                                
                                @if($item->type === 'image')
                                    <img src="{{ $item->value }}" 
                                         alt="{{ $item->key }}" 
                                         class="max-w-md rounded-lg shadow">
                                @else
                                    <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $item->value }}</p>
                                @endif
                                
                                <div class="mt-3">
                                    <a href="{{ route('konten.show', $item->key) }}" 
                                       class="text-blue-600 dark:text-blue-400 hover:underline text-sm">
                                        Lihat detail →
                                    </a>
                                </div>
                            </div>
                            
                            <span class="ml-4 px-3 py-1 text-xs font-medium rounded-full {{ $item->type === 'image' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                {{ $item->type }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
