<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;

class TestImageController extends Controller
{
    public function testImage($folder, $filename)
    {
        $path = storage_path('app/public/' . $folder . '/' . $filename);

        if (!File::exists($path)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        $file = File::get($path);
        $type = File::mimeType($path);

        $response = new Response($file, 200);
        $response->header('Content-Type', $type);

        return $response;
    }
}