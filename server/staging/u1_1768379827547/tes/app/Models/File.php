<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    protected $fillable = [
        'user_id',
        'folder_id',
        'name',
        'original_name',
        'file_path',
        'file_type',
        'mime_type',
        'size',
    ];

    protected $casts = [
        'size' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relasi ke User (pemilik file)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Folder (folder induk)
    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }

    // Helper: Format ukuran file (bytes ke MB/KB)
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->size;
        
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        
        return $bytes . ' bytes';
    }

    // Helper: Cek apakah file adalah gambar
    public function isImage(): bool
    {
        return in_array($this->file_type, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
    }

    // Helper: Dapatkan icon berdasarkan tipe file
    public function getIconClass(): string
    {
        return match($this->file_type) {
            'pdf' => 'text-red-500',
            'doc', 'docx' => 'text-blue-500',
            'xls', 'xlsx' => 'text-green-500',
            'jpg', 'jpeg', 'png', 'gif' => 'text-purple-500',
            default => 'text-slate-500',
        };
    }

    // Helper: Dapatkan full path untuk storage
    public function getFullPath(): string
    {
        return storage_path('app/' . $this->file_path);
    }

    // Helper: Cek apakah file exists
    public function fileExists(): bool
    {
        return Storage::exists($this->file_path);
    }

    // Helper: Delete file dari storage saat model dihapus
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($file) {
            if ($file->fileExists()) {
                Storage::delete($file->file_path);
            }
        });
    }
}
