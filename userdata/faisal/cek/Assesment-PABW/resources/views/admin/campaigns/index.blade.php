@extends('admin.layouts.master')

@section('content')
<div class="container px-6 mx-auto grid">
    <div class="flex justify-between items-center my-6">
        <h2 class="text-2xl font-semibold text-gray-700">Kelola Kampanye Donasi</h2>
        <a href="{{ route('admin.campaigns.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            + Tambah Baru
        </a>
    </div>

    @if(session('success'))
    <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    <div class="w-full overflow-hidden rounded-lg shadow-xs border border-gray-200">
        <div class="w-full overflow-x-auto">
            <table class="w-full whitespace-no-wrap">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                        <th class="px-4 py-3">Kampanye Donasi</th>
                        <th class="px-4 py-3">Target</th>
                        <th class="px-4 py-3">Terkumpul</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y">
                    @forelse($campaigns as $campaign)
                    <tr class="text-gray-700">
                        <td class="px-4 py-3">
                            <div class="flex items-center text-sm">
                                <div class="relative hidden w-12 h-12 mr-3 rounded md:block">
                                    <img class="object-cover w-full h-full rounded"
                                        src="{{ $campaign->image ?: asset('images/placeholder.jpg') }}"
                                        alt="cover" loading="lazy"
                                        onerror="this.onerror=null; this.src='{{ asset('images/placeholder.jpg') }}';" />
                                </div>
                                <div>
                                    <p class="font-semibold">{{ $campaign->title }}</p>
                                    <p class="text-xs text-gray-600">Berakhir: {{ \Carbon\Carbon::parse($campaign->end_date)->format('d M Y') }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm">Rp {{ number_format($campaign->target_amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-sm">Rp {{ number_format($campaign->current_amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-xs">
                            <span class="px-2 py-1 font-semibold leading-tight rounded-full
                                @if($campaign->status == 'Active') text-green-700 bg-green-100
                                @elseif($campaign->status == 'Completed') text-blue-700 bg-blue-100
                                @elseif($campaign->status == 'Inactive') text-red-700 bg-red-100
                                @else text-yellow-700 bg-yellow-100 @endif">
                                {{ $campaign->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center space-x-2 text-sm">
                                {{-- Tombol Edit --}}
                                <a href="{{ route('admin.campaigns.edit', $campaign->id) }}" class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg focus:outline-none focus:shadow-outline-gray" aria-label="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>

                                {{-- Tombol Delete --}}
                                <form action="{{ route('admin.campaigns.destroy', $campaign->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus kampanye donasi ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-red-600 rounded-lg focus:outline-none focus:shadow-outline-gray" aria-label="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                            Belum ada kampanye donasi. Silakan tambah baru.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t bg-gray-50">
            {{ $campaigns->links() }}
        </div>
    </div>
</div>
@endsection