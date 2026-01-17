<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CleanApiResponse
{
    public function handle(Request $request, Closure $next)
    {
        // Bersihkan output buffer sebelum memproses request
        if (ob_get_level()) {
            $level = ob_get_level();
            while ($level > 0) {
                ob_end_clean();
                $level = ob_get_level();
            }
        }

        $response = $next($request);

        // Hanya bersihkan untuk API routes
        if ($request->is('api/*')) {
            // Bersihkan output buffer jika ada
            if (ob_get_level()) {
                $level = ob_get_level();
                while ($level > 0) {
                    ob_end_clean();
                    $level = ob_get_level();
                }
            }

            // Pastikan response adalah JSON yang valid
            if ($response->headers->get('Content-Type') === 'application/json' ||
                strpos($response->headers->get('Content-Type'), 'application/json') !== false) {

                $content = $response->getContent();

                // Log jika ditemukan karakter aneh
                if (preg_match('/[^\x20-\x7E\x0A\x0D\x09\t\n\r]/', $content)) {
                    \Log::warning('Non-ASCII characters found in JSON response', [
                        'content_length' => strlen($content),
                        'content_preview' => substr($content, 0, 1000)
                    ]);
                }

                // Bersihkan karakter tidak terlihat sebelum dan sesudah JSON
                $cleanedContent = trim($content);

                // Pastikan hanya karakter JSON valid
                $cleanedContent = preg_replace('/^[\s\n\r\t]+|[\s\n\r\t]+$/u', '', $content);

                $response->setContent($cleanedContent);
            }
        }

        return $response;
    }
}