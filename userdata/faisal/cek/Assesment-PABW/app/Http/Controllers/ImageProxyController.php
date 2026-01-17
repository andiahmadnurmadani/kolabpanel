<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class ImageProxyController extends Controller
{
    public function show($folder, $filename)
    {
        try {
            // Validasi nama file untuk mencegah directory traversal
            if (preg_match('/\.\.(\/|\\\)/', $filename) || preg_match('/\.\.(\/|\\\)/', $folder)) {
                abort(400, 'Invalid file path');
            }

            // Gabungkan folder dan filename
            $path = $folder . '/' . $filename;

            // Cek apakah file ada di storage public
            if (!Storage::disk('public')->exists($path)) {
                abort(404, 'File not found');
            }

            // Dapatkan konten file
            $fileContent = Storage::disk('public')->get($path);
            $mimeType = Storage::disk('public')->mimeType($path);

            // Return response dengan header yang sesuai
            return Response::make($fileContent, 200, [
                'Content-Type' => $mimeType,
                'Cache-Control' => 'public, max-age=3600', // Cache selama 1 jam
            ]);
        } catch (\Exception $e) {
            abort(404, 'File not found');
        }
    }
}
