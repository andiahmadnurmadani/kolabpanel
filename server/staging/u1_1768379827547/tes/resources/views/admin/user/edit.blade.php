@extends('layouts.admin')

@section('page-title', 'Edit User')
@section('page-subtitle', 'Edit informasi user')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" 
           class="inline-flex items-center text-gray-600 hover:text-gray-800 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali ke Daftar User
        </a>
    </div>

    <!-- Edit Form -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-2xl font-bold">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">{{ $user->name }}</h2>
                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')

            <!-- User Name -->
            <div class="mb-6">
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-3">
                    <i class="fas fa-user mr-1"></i>
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="name" 
                       id="name" 
                       value="{{ old('name', $user->name) }}"
                       class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                       required>
                @error('name')
                <p class="mt-2 text-sm text-red-600">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    {{ $message }}
                </p>
                @enderror
            </div>

            <!-- Email -->
            <div class="mb-6">
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-3">
                    <i class="fas fa-envelope mr-1"></i>
                    Email <span class="text-red-500">*</span>
                </label>
                <input type="email" 
                       name="email" 
                       id="email" 
                       value="{{ old('email', $user->email) }}"
                       class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                       required>
                @error('email')
                <p class="mt-2 text-sm text-red-600">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    {{ $message }}
                </p>
                @enderror
            </div>

            <!-- User Permissions -->
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-3">
                    <i class="fas fa-shield-alt mr-1"></i>
                    Hak Akses & Status
                </label>
                <div class="space-y-3">
                    <!-- Admin Access -->
                    <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                        <input type="checkbox" 
                               name="is_admin" 
                               value="1"
                               {{ old('is_admin', $user->is_admin ?? false) ? 'checked' : '' }}
                               class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <div class="ml-3">
                            <span class="text-sm font-semibold text-gray-800">Admin Access</span>
                            <p class="text-xs text-gray-500">User memiliki akses ke admin panel</p>
                        </div>
                    </label>

                    <!-- Blocked Status -->
                    <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                        <input type="checkbox" 
                               name="blocked" 
                               value="1"
                               {{ old('blocked', $user->blocked ?? false) ? 'checked' : '' }}
                               class="w-5 h-5 text-red-600 border-gray-300 rounded focus:ring-red-500">
                        <div class="ml-3">
                            <span class="text-sm font-semibold text-gray-800">Blokir User</span>
                            <p class="text-xs text-gray-500">User tidak dapat login ke sistem</p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- User Information -->
            <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <h3 class="font-semibold text-gray-700 mb-3">
                    <i class="fas fa-info-circle mr-1"></i>
                    Informasi User
                </h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">User ID</p>
                        <p class="font-semibold text-gray-800">#{{ $user->id }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Terdaftar Sejak</p>
                        <p class="font-semibold text-gray-800">{{ $user->created_at->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Last Login</p>
                        <p class="font-semibold text-gray-800">{{ $user->last_login_at ? $user->last_login_at->format('d M Y, H:i') : 'Belum pernah' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Email Verified</p>
                        <p class="font-semibold text-gray-800">
                            @if($user->email_verified_at)
                                <span class="text-green-600">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Terverifikasi
                                </span>
                            @else
                                <span class="text-yellow-600">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    Belum Verifikasi
                                </span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <a href="{{ route('admin.users.index') }}" 
                   class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-semibold">
                    <i class="fas fa-times mr-2"></i>
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <!-- Danger Zone -->
    <div class="mt-6 bg-white rounded-xl shadow-sm overflow-hidden border border-red-200">
        <div class="p-6 bg-red-50">
            <h3 class="font-bold text-red-800 flex items-center">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Danger Zone
            </h3>
            <p class="text-sm text-red-600 mt-1">Aksi di bawah ini bersifat permanen dan tidak dapat dibatalkan</p>
        </div>
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="font-semibold text-gray-800">Hapus User</h4>
                    <p class="text-sm text-gray-500 mt-1">Hapus user ini beserta semua data terkait secara permanen</p>
                </div>
                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini? Aksi ini tidak dapat dibatalkan!')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-semibold">
                        <i class="fas fa-trash-alt mr-2"></i>
                        Hapus User
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
