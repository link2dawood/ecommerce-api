<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = Address::where('user_id', Auth::id())
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($addresses, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'is_default' => 'boolean',
            'type' => 'nullable|in:home,work,other',
        ]);

        if ($validator->fails()) {
            return response()->json(['status_code' => 400, 'message' => 'Validation failed', 'errors' => $validator->errors()], 400);
        }

        DB::beginTransaction();
        try {
            // If setting as default, unset other defaults
            if ($request->is_default) {
                Address::where('user_id', Auth::id())
                    ->update(['is_default' => false]);
            }

            $address = Address::create([
                'user_id' => Auth::id(),
                'name' => $request->name,
                'phone' => $request->phone,
                'address_line1' => $request->address_line1,
                'address_line2' => $request->address_line2,
                'city' => $request->city,
                'state' => $request->state,
                'postal_code' => $request->postal_code,
                'country' => $request->country,
                'is_default' => $request->is_default ?? false,
                'type' => $request->type ?? 'home',
            ]);

            DB::commit();

            return response()->json($address, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create address'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $address = Address::where('user_id', Auth::id())->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|string|max:20',
            'address_line1' => 'sometimes|required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'sometimes|required|string|max:100',
            'state' => 'sometimes|required|string|max:100',
            'postal_code' => 'sometimes|required|string|max:20',
            'country' => 'sometimes|required|string|max:100',
            'is_default' => 'boolean',
            'type' => 'nullable|in:home,work,other',
        ]);

        if ($validator->fails()) {
            return response()->json(['status_code' => 400, 'message' => 'Validation failed', 'errors' => $validator->errors()], 400);
        }

        DB::beginTransaction();
        try {
            // If setting as default, unset other defaults
            if ($request->is_default) {
                Address::where('user_id', Auth::id())
                    ->where('id', '!=', $id)
                    ->update(['is_default' => false]);
            }

            $address->update($request->all());

            DB::commit();

            return response()->json($address, 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to update address'], 500);
        }
    }

    public function destroy($id)
    {
        $address = Address::where('user_id', Auth::id())->findOrFail($id);
        $address->delete();

        return response()->json(['message' => 'Address deleted successfully'], 200);
    }

    public function setDefault($id)
    {
        DB::beginTransaction();
        try {
            $address = Address::where('user_id', Auth::id())->findOrFail($id);

            // Unset all defaults
            Address::where('user_id', Auth::id())
                ->update(['is_default' => false]);

            // Set this as default
            $address->is_default = true;
            $address->save();

            DB::commit();

            return response()->json($address, 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to set default address'], 500);
        }
    }
}