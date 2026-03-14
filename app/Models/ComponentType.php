<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ComponentType extends Model
{
    protected $fillable = ['name', 'slug', 'attributes', 'sort_order'];

    protected $casts = [
        'attributes' => 'array',
        'sort_order' => 'integer',
    ];

    public function serverComponents()
    {
        return $this->hasMany(ServerComponent::class, 'component_type_id');
    }

    public function getAttributeSlugs(): array
    {
        $attrs = $this->attributes ?? [];
        return array_map(fn ($a) => $a['slug'] ?? Str::slug($a['name'] ?? ''), $attrs);
    }

    public static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->slug) && ! empty($model->name)) {
                $model->slug = Str::slug($model->name);
            }
        });
        static::updating(function ($model) {
            if ($model->isDirty('name') && empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });
    }
}
