@extends('layouts.admin')

@section('page-title', 'Konten Statis')
@section('page-subtitle', 'Kelola konten statis halaman')

@section('content')
    <div class="space-y-6">

        <!-- Info Banner -->
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
            <div class="flex items-start space-x-3">
                <i class="fas fa-info-circle text-blue-600 text-xl mt-1"></i>
                <div>
                    <h4 class="font-semibold text-blue-900">Tentang Konten Statis</h4>
                    <p class="text-sm text-blue-700 mt-1">Kelola konten yang ditampilkan di halaman landing, banner, dan informasi statis lainnya.</p>
                </div>
            </div>
        </div>

        @if(($items ?? collect())->count() > 0)
            <!-- Add New Content Button -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-gray-800">Tambah Konten Baru</h3>
                        <p class="text-sm text-gray-500 mt-1">Buat konten statis baru untuk halaman Anda</p>
                    </div>
                    <a href="{{ route('admin.content.create') }}"
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Konten
                    </a>
                </div>
            </div>
        @endif

        <!-- Content Items -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @forelse($items ?? [] as $item)
            <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            @if($item->type == 'image')
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-image text-purple-600 text-xl"></i>
                            </div>
                            @else
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-file-alt text-blue-600 text-xl"></i>
                            </div>
                            @endif
                            <div>
                                <h3 class="font-bold text-gray-800">{{ ucwords(str_replace('_', ' ', $item->key)) }}</h3>
                                <p class="text-xs text-gray-500">{{ $item->key }}</p>
                            </div>
                        </div>
                        <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $item->type == 'image' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                            {{ ucfirst($item->type) }}
                        </span>
                    </div>

                    <div class="mb-4">
                        @if($item->type == 'image')
                            @if($item->value)
                            <img src="{{ $item->value }}" alt="{{ $item->key }}" class="w-full h-40 object-cover rounded-lg border border-gray-200">
                            @else
                            <div class="w-full h-40 bg-gray-100 rounded-lg border border-gray-200 flex items-center justify-center">
                                <i class="fas fa-image text-gray-300 text-4xl"></i>
                            </div>
                            @endif
                        @else
                        <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <p class="text-sm text-gray-700 line-clamp-3">{{ $item->value ?? 'Belum ada konten' }}</p>
                        </div>
                        @endif
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                        <p class="text-xs text-gray-500">
                            <i class="fas fa-clock mr-1"></i>
                            Update: {{ $item->updated_at ? $item->updated_at->diffForHumans() : '-' }}
                        </p>
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('admin.content.edit', $item->id) }}" 
                               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-semibold">
                                <i class="fas fa-edit mr-1"></i>
                                Edit
                            </a>
                            <form action="{{ route('admin.content.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus konten ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm font-semibold">
                                    <i class="fas fa-trash mr-1"></i>
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-12 text-center">
                <i class="fas fa-file-alt text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Belum Ada Konten</h3>
                <p class="text-gray-500 mb-6">Tambahkan konten statis untuk halaman Anda</p>
                <a href="{{ route('admin.content.create') }}" 
                   class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Konten
                </a>
            </div>
            @endforelse
        </div>


    </div>

    @if(session('success'))
    <div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-3 z-50">
        <i class="fas fa-check-circle text-xl"></i>
        <span class="font-semibold">{{ session('success') }}</span>
    </div>
    @endif
@endsection
