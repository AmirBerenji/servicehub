<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\BusinessImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BusinessController extends Controller
{
    public function index()
    {
        return Business::with(['services', 'images'])->get();
    }

    public function store(Request $r)
    {
        $validator = Validator::make($r->all(), [
            'name'        => 'required|string|max:255',
            'phone'       => 'nullable|string|max:30',
            'email'       => 'nullable|email|max:255',
            'address'     => 'nullable|string|max:255',
            'lat'         => 'nullable|numeric',
            'lng'         => 'nullable|numeric',
            'logo'        => 'nullable|image|max:4096',          // single logo
            'images'      => 'nullable|array|max:3',              // up to 3 business images
            'images.*'    => 'image|max:4096',
            'services'    => 'nullable|array',
            'services.*.id'        => 'required_with:services|exists:services,id',
            'services.*.min_price' => 'nullable|numeric',
            'services.*.max_price' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $b = Business::create([
            'user_id' => auth()->id(),
            'name'    => $r->name,
            'phone'   => $r->phone,
            'email'   => $r->email,
            'address' => $r->address,
            'lat'     => $r->lat,
            'lng'     => $r->lng,
        ]);

        // Handle logo upload
        if ($r->hasFile('logo')) {
            $path = $r->file('logo')->store('business/logos', 'public');
            $b->update(['logo' => $path]);
        }

        // Handle business images (up to 3)
        if ($r->hasFile('images')) {
            foreach ($r->file('images') as $index => $image) {
                $path = $image->store('business/images', 'public');
                BusinessImage::create([
                    'business_id' => $b->id,
                    'image_path'  => $path,
                    'sort_order'  => $index,
                ]);
            }
        }

        // Attach services
        if ($r->has('services')) {
            foreach ($r->services as $s) {
                $b->services()->attach($s['id'], [
                    'min_price' => $s['min_price'] ?? null,
                    'max_price' => $s['max_price'] ?? null,
                ]);
            }
        }

        return $b->load(['services', 'images']);
    }

    public function show(string $id)
    {
        $business = Business::with(['services', 'images'])->findOrFail($id);
        return $business;
    }

    public function update(Request $r, string $id)
    {
        $b = Business::findOrFail($id);

        $validator = Validator::make($r->all(), [
            'name'     => 'sometimes|string|max:255',
            'phone'    => 'nullable|string|max:30',
            'email'    => 'nullable|email|max:255',
            'address'  => 'nullable|string|max:255',
            'lat'      => 'nullable|numeric',
            'lng'      => 'nullable|numeric',
            'logo'     => 'nullable|image|max:4096',
            'images'   => 'nullable|array|max:3',
            'images.*' => 'image|max:4096',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $b->fill($r->only(['name', 'phone', 'email', 'address', 'lat', 'lng']));

        // Replace logo if a new one is uploaded
        if ($r->hasFile('logo')) {
            if ($b->logo) {
                Storage::disk('public')->delete($b->logo);
            }
            $b->logo = $r->file('logo')->store('business/logos', 'public');
        }

        $b->save();

        // Add new images (existing ones are kept unless deleted separately)
        if ($r->hasFile('images')) {
            $existingCount = $b->images()->count();
            foreach ($r->file('images') as $index => $image) {
                $path = $image->store('business/images', 'public');
                BusinessImage::create([
                    'business_id' => $b->id,
                    'image_path'  => $path,
                    'sort_order'  => $existingCount + $index,
                ]);
            }
        }

        return $b->load(['services', 'images']);
    }

    public function destroy(string $id)
    {
        $b = Business::findOrFail($id);

        if ($b->logo) {
            Storage::disk('public')->delete($b->logo);
        }

        foreach ($b->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $b->delete(); // business_images rows auto-deleted via onDelete('cascade')

        return response()->json(['message' => 'Business deleted']);
    }

    // Optional: delete a single business image
    public function destroyImage(string $imageId)
    {
        $image = BusinessImage::findOrFail($imageId);
        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return response()->json(['message' => 'Image deleted']);
    }
}
