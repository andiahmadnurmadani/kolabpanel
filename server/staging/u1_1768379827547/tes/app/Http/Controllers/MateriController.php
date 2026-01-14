<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MateriController extends Controller
{
    /**
     * Tampilkan halaman materi dengan folders & files
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $currentFolderId = $request->get('folder');
        
        // Ambil folder saat ini (jika ada)
        $currentFolder = null;
        if ($currentFolderId) {
            $currentFolder = Folder::where('id', $currentFolderId)
                ->where(function($query) use ($user) {
                    $query->where('user_id', $user->id)
                          ->orWhereHas('sharedUsers', function($q) use ($user) {
                              $q->where('user_id', $user->id);
                          });
                })
                ->firstOrFail();
        }
        
        // Ambil folders (root atau children dari current folder)
        $folders = Folder::where('user_id', $user->id)
            ->where('parent_id', $currentFolderId)
            ->orWhereHas('sharedUsers', function($q) use ($user, $currentFolderId) {
                $q->where('user_id', $user->id)
                  ->where('parent_id', $currentFolderId);
            })
            ->orderBy('name')
            ->get();
        
        // Ambil files dalam folder saat ini
        $files = File::where('user_id', $user->id)
            ->where('folder_id', $currentFolderId)
            ->orderBy('original_name')
            ->get();
        
        // Breadcrumb
        $breadcrumb = $currentFolder ? $currentFolder->getBreadcrumb() : [];
        
        // Konten Statis
        $kontenStatis = \App\Models\KontenStatis::pluck('value', 'key');
        
        return view('pages.materi', compact('folders', 'files', 'currentFolder', 'breadcrumb', 'kontenStatis'));
    }

    /**
     * Store folder baru
     */
    public function storeFolder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:folders,id',
            'color' => 'nullable|string|in:blue,green,purple,indigo,pink,yellow,red,orange,teal,cyan',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $folder = Folder::create([
            'user_id' => Auth::id(),
            'parent_id' => $request->parent_id,
            'name' => $request->name,
            'color' => $request->color ?? 'blue',
            'size' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Folder berhasil dibuat',
            'folder' => $folder
        ]);
    }

    /**
     * Update folder (rename)
     */
    public function updateFolder(Request $request, $id)
    {
        $folder = Folder::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|in:blue,green,purple,indigo,pink,yellow,red,orange,teal,cyan',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $folder->update([
            'name' => $request->name,
            'color' => $request->color ?? $folder->color,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Folder berhasil diperbarui',
            'folder' => $folder
        ]);
    }

    /**
     * Delete folder
     */
    public function deleteFolder($id)
    {
        $folder = Folder::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Cek apakah folder kosong
        $hasChildren = $folder->children()->exists();
        $hasFiles = $folder->files()->exists();

        if ($hasChildren || $hasFiles) {
            return response()->json([
                'success' => false,
                'message' => 'Folder tidak kosong. Hapus semua file dan subfolder terlebih dahulu.'
            ], 400);
        }

        $folder->delete();

        return response()->json([
            'success' => true,
            'message' => 'Folder berhasil dihapus'
        ]);
    }

    /**
     * Upload file
     */
    public function uploadFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,gif|max:10240', // max 10MB
            'folder_id' => 'nullable|exists:folders,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $uploadedFile = $request->file('file');
        $originalName = $uploadedFile->getClientOriginalName();
        $extension = $uploadedFile->getClientOriginalExtension();
        $mimeType = $uploadedFile->getMimeType();
        $size = $uploadedFile->getSize();
        
        // Generate unique filename
        $filename = time() . '_' . uniqid() . '.' . $extension;
        
        // Store file
        $path = $uploadedFile->storeAs('materials/' . Auth::id(), $filename);

        // Create file record
        $file = File::create([
            'user_id' => Auth::id(),
            'folder_id' => $request->folder_id,
            'name' => pathinfo($filename, PATHINFO_FILENAME),
            'original_name' => $originalName,
            'file_path' => $path,
            'file_type' => $extension,
            'mime_type' => $mimeType,
            'size' => $size,
        ]);

        // Update folder size jika ada folder
        if ($request->folder_id) {
            $folder = Folder::find($request->folder_id);
            if ($folder) {
                $folder->updateSize();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'File berhasil diupload',
            'file' => $file
        ]);
    }

    /**
     * Download file
     */
    public function downloadFile($id)
    {
        $file = File::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (!$file->fileExists()) {
            abort(404, 'File tidak ditemukan');
        }

        return Storage::download($file->file_path, $file->original_name);
    }

    /**
     * Delete file
     */
    public function deleteFile($id)
    {
        $file = File::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $folderId = $file->folder_id;
        
        // Delete file (akan otomatis hapus dari storage via model boot)
        $file->delete();

        // Update folder size
        if ($folderId) {
            $folder = Folder::find($folderId);
            if ($folder) {
                $folder->updateSize();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'File berhasil dihapus'
        ]);
    }

    /**
     * Rename file
     */
    public function renameFile(Request $request, $id)
    {
        $file = File::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Update original name (dengan extension)
        $extension = pathinfo($file->original_name, PATHINFO_EXTENSION);
        $newName = $request->name;
        
        // Tambahkan extension jika belum ada
        if (!str_ends_with(strtolower($newName), '.' . strtolower($extension))) {
            $newName .= '.' . $extension;
        }

        $file->update([
            'original_name' => $newName,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'File berhasil direname',
            'file' => $file
        ]);
    }

    /**
     * Preview file (open in new tab)
     */
    public function previewFile($id)
    {
        $file = File::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (!$file->fileExists()) {
            abort(404, 'File tidak ditemukan');
        }

        return Storage::response($file->file_path);
    }
}
