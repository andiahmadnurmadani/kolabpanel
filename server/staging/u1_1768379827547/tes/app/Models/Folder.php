<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Folder extends Model
{
    protected $fillable = [
        'user_id',
        'parent_id',
        'name',
        'color',
        'size',
    ];

    protected $casts = [
        'size' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relasi ke User (pemilik folder)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Parent Folder (folder induk)
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Folder::class, 'parent_id');
    }

    // Relasi ke Child Folders (sub-folder)
    public function children(): HasMany
    {
        return $this->hasMany(Folder::class, 'parent_id');
    }

    // Relasi ke Files dalam folder ini
    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }

    // Relasi many-to-many ke User (shared users)
    public function sharedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'folder_user')
                    ->withPivot('permission')
                    ->withTimestamps();
    }

    // Helper: Cek apakah ini root folder
    public function isRoot(): bool
    {
        return is_null($this->parent_id);
    }

    // Helper: Dapatkan breadcrumb path
    public function getBreadcrumb(): array
    {
        $breadcrumb = [];
        $folder = $this;

        while ($folder) {
            array_unshift($breadcrumb, [
                'id' => $folder->id,
                'name' => $folder->name,
            ]);
            $folder = $folder->parent;
        }

        return $breadcrumb;
    }

    // Helper: Hitung total files dalam folder (termasuk subfolder)
    public function getTotalFilesCount(): int
    {
        $count = $this->files()->count();
        
        foreach ($this->children as $child) {
            $count += $child->getTotalFilesCount();
        }
        
        return $count;
    }

    // Helper: Format ukuran folder
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

    // Helper: Update total size folder berdasarkan files di dalamnya
    public function updateSize(): void
    {
        $totalSize = $this->files()->sum('size');
        
        // Tambahkan size dari subfolder
        foreach ($this->children as $child) {
            $totalSize += $child->size;
        }
        
        $this->update(['size' => $totalSize]);
        
        // Update parent folder jika ada
        if ($this->parent) {
            $this->parent->updateSize();
        }
    }
}
