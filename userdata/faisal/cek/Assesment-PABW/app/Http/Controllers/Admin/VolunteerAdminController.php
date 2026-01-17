<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VolunteerRegistration;
use Illuminate\Http\Request;

class VolunteerAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $volunteers = VolunteerRegistration::latest()->get();
        return view('admin.volunteers.index', compact('volunteers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.volunteers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'motivation' => 'required|string',
            'skills' => 'nullable|string',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        VolunteerRegistration::create($validated);

        return redirect()->route('admin.volunteers.index')
            ->with('success', 'Relawan berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(VolunteerRegistration $volunteer)
    {
        return view('admin.volunteers.show', compact('volunteer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VolunteerRegistration $volunteer)
    {
        return view('admin.volunteers.edit', compact('volunteer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VolunteerRegistration $volunteer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'motivation' => 'required|string',
            'skills' => 'nullable|string',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $volunteer->update($validated);

        return redirect()->route('admin.volunteers.index')
            ->with('success', 'Relawan berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VolunteerRegistration $volunteer)
    {
        $volunteer->delete();
        return redirect()->route('admin.volunteers.index')
            ->with('success', 'Relawan berhasil dihapus');
    }
}
