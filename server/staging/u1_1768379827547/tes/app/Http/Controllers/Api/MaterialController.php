<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Folder;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MaterialController extends Controller
{
    /**
     * List folders milik user yang login.
     * Opsional: ?parent_id=... untuk lihat subfolder.
     */
    public function folders(Request $request)
    {
        $user = $request->user();
        $parentId = $request->query('parent_id');

        $query = Folder::where('user_id', $user->id);

        if ($parentId !== null) {
            $query->where('parent_id', $parentId);
        } else {
            $query->whereNull('parent_id');
        }

        $folders = $query->orderBy('name')->get();

        return response()->json([
            'data' => $folders,
        ]);
    }

    public function folderShow(Request $request, int $id)
    {
        $user = $request->user();

        $folder = Folder::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        return response()->json([
            'data' => $folder,
        ]);
    }

    public function folderStore(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:folders,id',
            'color' => 'nullable|string|in:blue,green,purple,indigo,pink,yellow,red,orange,teal,cyan',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $folder = Folder::create([
            'user_id' => $user->id,
            'parent_id' => $request->parent_id,
            'name' => $request->name,
            'color' => $request->color ?? 'blue',
            'size' => 0,
        ]);

        return response()->json([
            'message' => 'Folder created',
            'data' => $folder,
        ], 201);
    }

    public function folderUpdate(Request $request, int $id)
    {
        $user = $request->user();

        $folder = Folder::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|in:blue,green,purple,indigo,pink,yellow,red,orange,teal,cyan',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $folder->update([
            'name' => $request->name,
            'color' => $request->color ?? $folder->color,
        ]);

        return response()->json([
            'message' => 'Folder updated',
            'data' => $folder->fresh(),
        ]);
    }

    public function folderDestroy(Request $request, int $id)
    {
        $user = $request->user();

        $folder = Folder::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $hasChildren = $folder->children()->exists();
        $hasFiles = $folder->files()->exists();

        if ($hasChildren || $hasFiles) {
            return response()->json([
                'message' => 'Folder tidak kosong. Hapus semua file dan subfolder terlebih dahulu.',
            ], 400);
        }

        $folder->delete();

        return response()->json([
            'message' => 'Folder deleted',
        ]);
    }

    /**
     * List files milik user, opsional filter ?folder_id=...
     */
    public function files(Request $request)
    {
        $user = $request->user();
        $folderId = $request->query('folder_id');

        $query = File::where('user_id', $user->id);

        if ($folderId !== null) {
            $query->where('folder_id', $folderId);
        }

        $files = $query->orderBy('original_name')->get();

        return response()->json([
            'data' => $files,
        ]);
    }

    public function fileShow(Request $request, int $id)
    {
        $user = $request->user();

        $file = File::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        return response()->json([
            'data' => $file,
        ]);
    }

    public function fileStore(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,gif|max:10240',
            'folder_id' => 'nullable|exists:folders,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $uploadedFile = $request->file('file');
        $originalName = $uploadedFile->getClientOriginalName();
        $extension = $uploadedFile->getClientOriginalExtension();
        $mimeType = $uploadedFile->getMimeType();
        $size = $uploadedFile->getSize();

        $filename = time() . '_' . uniqid() . '.' . $extension;

        $path = $uploadedFile->storeAs('materials/' . $user->id, $filename);

        $file = File::create([
            'user_id' => $user->id,
            'folder_id' => $request->folder_id,
            'name' => pathinfo($filename, PATHINFO_FILENAME),
            'original_name' => $originalName,
            'file_path' => $path,
            'file_type' => $extension,
            'mime_type' => $mimeType,
            'size' => $size,
        ]);

        if ($request->folder_id) {
            $folder = Folder::find($request->folder_id);
            if ($folder) {
                $folder->updateSize();
            }
        }

        return response()->json([
            'message' => 'File uploaded',
            'data' => $file,
        ], 201);
    }

    public function fileUpdate(Request $request, int $id)
    {
        $user = $request->user();

        $file = File::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $extension = pathinfo($file->original_name, PATHINFO_EXTENSION);
        $newName = $request->name;

        if (! str_ends_with(strtolower($newName), '.' . strtolower($extension))) {
            $newName .= '.' . $extension;
        }

        $file->update([
            'original_name' => $newName,
        ]);

        return response()->json([
            'message' => 'File renamed',
            'data' => $file->fresh(),
        ]);
    }

    public function fileDestroy(Request $request, int $id)
    {
        $user = $request->user();

        $file = File::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $folderId = $file->folder_id;

        $file->delete();

        if ($folderId) {
            $folder = Folder::find($folderId);
            if ($folder) {
                $folder->updateSize();
            }
        }

        return response()->json([
            'message' => 'File deleted',
        ]);
    }

    public function fileDownload(Request $request, int $id)
    {
        $user = $request->user();

        $file = File::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if (! $file->fileExists()) {
            abort(404, 'File tidak ditemukan');
        }

        return Storage::download($file->file_path, $file->original_name);
    }

    public function filePreview(Request $request, int $id)
    {
        $user = $request->user();

        $file = File::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if (! $file->fileExists()) {
            abort(404, 'File tidak ditemukan');
        }

        return Storage::response($file->file_path);
    }
}
