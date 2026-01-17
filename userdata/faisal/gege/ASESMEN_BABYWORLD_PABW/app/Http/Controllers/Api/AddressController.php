<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    // GET /api/address
    public function show(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user()->address
        ]);
    }

    // POST /api/address
    public function store(Request $request)
    {
        $request->validate([
            'address' => 'required|string',
            'city' => 'required|string',
            'postal_code' => 'required|string'
        ]);

        $address = $request->user()->address()->create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Address added',
            'data' => $address
        ]);
    }

    // PUT /api/address
    public function update(Request $request)
    {
        $request->validate([
            'address' => 'required|string',
            'city' => 'required|string',
            'postal_code' => 'required|string'
        ]);

        $address = $request->user()->address;
        $address->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Address updated',
            'data' => $address
        ]);
    }
}
