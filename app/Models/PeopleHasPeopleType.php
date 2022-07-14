<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeopleHasPeopleType extends Model
{
    use HasFactory;

    protected $table = 'people_has_people_types';

    protected $fillable = [
        'people_id',
        'type_people_id',
        'created_at',
        'updated_at',
        'created_usu',
        'updated_usu',
        'created_ip',
        'updated_ip',
        'is_visible',
        'status',
    ];

    protected function peopleHasPeopleTypesId(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => encrypt($value),
        );
    }
}
