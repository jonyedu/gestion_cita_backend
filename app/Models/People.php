<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class People extends Model
{
    use HasFactory;

    protected $table = 'people';

    protected $fillable = [
        'name',
        'identification_card',
        'phone',
        'direction',
        'created_at',
        'updated_at',
        'created_usu',
        'updated_usu',
        'created_ip',
        'updated_ip',
        'is_visible',
        'status',
    ];

    protected function peopleId(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => encrypt($value),
        );
    }

    public function typePeoples(){
        return $this->belongsToMany(
            TypePeople::class,
            'people_has_people_types',
            'people_id',
            'type_people_id'
        );
    }
}
