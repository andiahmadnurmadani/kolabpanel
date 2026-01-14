<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KontenStatis;
use Illuminate\Support\Facades\Storage;

class AdminKontenController extends Controller
{
    public function index()
    {
        $items = KontenStatis::orderBy('key')->get();
        $kontenStatis = KontenStatis::pluck('value', 'key');
        return view('admin.konten.index', compact('items', 'kontenStatis'));
    }

    public function edit(KontenStatis $content)
    {
        $kontenStatis = KontenStatis::pluck('value', 'key');
        return view('admin.konten.edit', ['konten' => $content, 'kontenStatis' => $kontenStatis]);
    }

    public function update(Request $req, KontenStatis $content)
    {
        $data = $req->validate([
            'value' => 'nullable|string',
            'file' => 'nullable|file|mimes:png,jpg,jpeg,svg,webp|max:2048'
        ]);

        if ($req->hasFile('file')) {
            $path = $req->file('file')->store('public/konten');
            // simpan path relatif ke storage link
            $content->value = Storage::url($path);
            $content->type = 'image';
        } else {
            $content->value = $data['value'] ?? $content->value;
            $content->type = $content->type ?? 'text';
        }
        $content->save();

        return redirect()->route('admin.content.index')->with('success', 'Konten diperbarui');
    }

    public function create()
    {
        return view('admin.konten.create');
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'key' => 'required|string|unique:konten_statis,key',
            'value' => 'nullable|string',
            'file' => 'nullable|file|mimes:png,jpg,jpeg,svg,webp|max:2048',
            'type' => 'nullable|string'
        ]);
        $value = $data['value'] ?? null;
        $type = $data['type'] ?? 'text';
        if ($req->hasFile('file')) {
            $path = $req->file('file')->store('public/konten');
            $value = \Illuminate\Support\Facades\Storage::url($path);
            $type = 'image';
        }
        KontenStatis::create(['key' => $data['key'], 'value' => $value, 'type' => $type]);
        
        return redirect()->route('admin.content.index')->with('success', 'Konten dibuat');
    }

    public function destroy(KontenStatis $content)
    {
        $content->delete();
        return redirect()->route('admin.content.index')->with('success', 'Konten dihapus');
    }
}
