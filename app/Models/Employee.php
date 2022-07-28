<?php

namespace App\Models;

use App\Enums\Gender;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'gender',
        'age',
        'phone',
        'photo',
        'team_id',
        'role_id',
        'verified_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'age' => 'integer',
        'gender' => Gender::class,
        'verified_at' => 'datetime',
    ];

    /**
     * Get the team that owns the Employee
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the role that owns the Employee
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function phone(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => str($value)->replaceMatches('/[^0-9]/', '')->whenStartsWith('0', function ($string) {
                return $string->replaceFirst('0', '+62');
            }),
            get: fn ($value) => str($value)->replaceMatches('/[^0-9]/', '')->whenStartsWith('0', function ($string) {
                return $string->replaceFirst('0', '+62');
            }),
        );
    }
}
