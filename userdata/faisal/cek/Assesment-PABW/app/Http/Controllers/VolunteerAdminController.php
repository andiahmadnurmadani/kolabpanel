<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VolunteerAdminController extends Controller
{
    public function pendaftarIndex()
    {
        // Mengambil data pendaftar beserta data usernya dan kampanye nya
        $applications = \App\Models\VolunteerApplication::with(['user', 'campaign'])
            ->latest()
            ->paginate(10);

        return view('admin.relawan.pendaftar', compact('applications'));
    }
}
