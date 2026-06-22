<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Business extends Model
{
    protected $fillable = [
        'user_id', 'name', 'phone', 'email', 'address', 'lat', 'lng', 'logo',
    ];

    // Automatically include these computed fields when the model is converted to JSON
    protected $appends = ['logo_url'];

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class)
            ->withPivot('min_price', 'max_price');
    }

    public function images(): HasMany
    {
        return $this->hasMany(BusinessImage::class)->orderBy('sort_order');
    }

    // Turns the stored path into a full public URL
    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo ? Storage::disk('public')->url($this->logo) : null;
    }
}
