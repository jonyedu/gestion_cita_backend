<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    use HasFactory;

    protected $table = 'pets';

    protected $fillable = [
        'people_id',
        'pet_type_id',
        'name',
        'age',
        'created_at',
        'updated_at',
        'created_usu',
        'updated_usu',
        'created_ip',
        'updated_ip',
        'is_visible',
        'status',
    ];

    protected function petId(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => encrypt($value),
        );
    }

    protected function peopleId(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => encrypt($value),
        );
    }

    protected function petTypeId(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => encrypt($value),
        );
    }

    public function scopePeople($query)
    {
        $people = People::select('name')
            ->whereColumn('people_id', 'people.id')
            ->where('status', 1)
            ->limit(1);

        $query->addSelect(['people' => $people]);
    }

    public function scopePetType($query)
    {
        $pet_type = PetType::select('descripcion')
            ->whereColumn('pet_type_id', 'pet_types.id')
            ->where('status', 1)
            ->limit(1);

        $query->addSelect(['pet_type' => $pet_type]);
    }
}
