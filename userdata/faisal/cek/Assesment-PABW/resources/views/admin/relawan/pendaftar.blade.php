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
                        <th class="px-4 py-3">Kampanye</th>
                        <th class="px-4 py-3">Posisi</th>
                        <th class="px-4 py-3">CV</th>
                        <th class="px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y">
                    @foreach($applications as $app)
                    <tr class="text-gray-700">
                        <td class="px-4 py-3">
                            <div class="flex items-center text-sm">
                                <div>
                                    <p class="font-semibold">{{ $app->user->name }}</p>
                                    <p class="text-xs text-gray-600">{{ $app->user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <div class="flex items-center">
                                @if($app->campaign->image)
                                    <div class="w-8 h-8 rounded overflow-hidden mr-2">
                                        <img src="{{ $app->campaign->image }}"
                                             alt="{{ $app->campaign->judul }}"
                                             class="w-full h-full object-cover"
                                             onerror="this.onerror=null; this.src='https://placehold.co/32x32?text=No+Image';">
                                    </div>
                                @endif
                                <div>{{ $app->campaign->judul }}</div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm">{{ $app->posisi_dilamar }}</td>
                        <td class="px-4 py-3 text-sm">
                            <a href="{{ asset('storage/' . $app->cv_path) }}" target="_blank" class="text-blue-600 hover:underline">Download CV</a>
                        </td>
                        <td class="px-4 py-3 text-xs">
                            <span class="px-2 py-1 font-semibold leading-tight rounded-full {{ $app->status == 'pending' ? 'bg-orange-100 text-orange-700' : 'bg-green-100 text-green-700' }}">
                                {{ ucfirst($app->status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t">
            {{ $applications->links() }}
        </div>
    </div>
</div>
@endsection