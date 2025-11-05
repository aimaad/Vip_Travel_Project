<?php

namespace Modules\Core\Traits;

use Illuminate\Support\Str;

trait Sluggable
{
    public static function bootSluggable()
    {
        static::creating(function ($model) {
            $model->generateSlug();
        });

        static::updating(function ($model) {
            $model->generateSlug();
        });
    }

    public function generateSlug()
    {
        $slugOptions = $this->sluggable();

        if (empty($this->{$slugOptions['slug']['source']})) {
            throw new \Exception('Slug source is not defined');
        }

        $source = $this->{$slugOptions['slug']['source']};
        $this->{$slugOptions['slug']['target'] ?? 'slug'} = Str::slug($source);
    }

    abstract public function sluggable(): array;
}