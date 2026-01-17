@extends('admin.layouts.master')

@section('content')
<div class="container px-6 mx-auto grid">
    <h2 class="my-6 text-2xl font-semibold text-gray-700">Daftar Pendaftar Relawan</h2>
    
    <div class="w-full overflow-hidden rounded-lg shadow-xs border border-gray-200">
        <div class="w-full overflow-x-auto">
            <table class="w-full whitespace-no-wrap">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                        <th class="px-4 py-3">Nama Relawan</th>
                        <th class="px-4 py-3">Kampanye Tujuan</th>
                        <th class="px-4 py-3">Posisi</th>
                        <th class="px-4 py-3">Tanggal Daftar</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y">
                    @forelse($allVolunteers as $app)
                    <tr class="text-gray-700 hover:bg-gray-50 transition-colors">
                        
                        {{-- 1. Info User --}}
                        <td class="px-4 py-3">
                            <div class="flex items-center text-sm">
                                <div class="relative hidden w-8 h-8 mr-3 rounded-full md:block">
                                    {{-- Avatar Inisial --}}
                                    <div class="w-full h-full rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xs shadow-inner">
                                        {{ substr($app->user->name ?? 'U', 0, 1) }}
                                    </div>
                                </div>
                                <div>
                                    <p class="font-semibold">{{ $app->user->name ?? 'User Terhapus' }}</p>
                                    <p class="text-xs text-gray-600">{{ $app->user->email ?? '-' }}</p>
                                </div>
                            </div>
                        </td>

                        {{-- 2. Judul Kampanye --}}
                        <td class="px-4 py-3 text-sm font-medium">
                            {{ Str::limit($app->campaign->judul ?? 'Kampanye Dihapus', 30) }}
                        </td>

                        {{-- 3. Posisi Dilamar --}}
                        <td class="px-4 py-3 text-sm">
                            <span class="px-2 py-1 bg-blue-50 text-blue-600 rounded-md text-xs font-bold border border-blue-100">
                                {{ $app->posisi_dilamar }}
                            </span>
                        </td>

                        {{-- 4. Tanggal --}}
                        <td class="px-4 py-3 text-sm">
                            {{ $app->created_at->format('d M Y') }}
                            <br>
                            <span class="text-xs text-gray-500">{{ $app->created_at->format('H:i') }} WIB</span>
                        </td>

                        {{-- 5. Status --}}
                        <td class="px-4 py-3 text-xs">
                            @php
                                $statusClass = match($app->status) {
                                    'approved' => 'text-green-700 bg-green-100',
                                    'rejected' => 'text-red-700 bg-red-100',
                                    default    => 'text-orange-700 bg-orange-100' // Pending
                                };
                            @endphp
                            <span class="px-2 py-1 font-semibold leading-tight rounded-full {{ $statusClass }}">
                                {{ ucfirst($app->status) }}
                            </span>
                        </td>

                        {{-- 6. Aksi (Tombol Review) --}}
                        <td class="px-4 py-3 text-sm">
                            <div class="flex items-center space-x-2">
                                {{-- Tombol ini mengarah ke halaman Detail (show.blade.php) --}}
                                <a href="{{ route('admin.verifikasi-relawan.show', $app->id) }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium leading-5 text-white transition-colors duration-150 bg-indigo-600 border border-transparent rounded-lg active:bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:shadow-outline-purple">
                                    <i class="fas fa-eye"></i> Review
                                </a>
                            </div>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                                <span>Belum ada pendaftar relawan yang masuk.</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-4 py-3 border-t bg-gray-50">
            {{ $allVolunteers->links() }}
        </div>
    </div>
</div>
@endsection