<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypePeople extends Model
{
    use HasFactory;

    protected $table = 'type_peoples';

    protected $fillable = [
        'descripcion',
        'created_at',
        'updated_at',
        'created_usu',
        'updated_usu',
        'created_ip',
        'updated_ip',
        'is_visible',
        'status',
    ];

    protected function petTypeId(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => encrypt($value),
        );
    }
}
