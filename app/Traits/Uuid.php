<?php

namespace App\Traits;

trait Uuid
{
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            try {
                $model->uuid = (string) str()->uuid();
            } catch (\Throwable $e) {
                abort(500, $e->getMessage());
            }
        });
    }
}
