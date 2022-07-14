<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PetMedicalAppointment extends Model
{
    use HasFactory;

    protected $table = 'pet_medical_appointments';

    protected $fillable = [
        'pet_id',
        'registration_date',
        'registration_time',
        'turn',
        'created_at',
        'updated_at',
        'created_usu',
        'updated_usu',
        'created_ip',
        'updated_ip',
        'is_visible',
        'status',
    ];

    protected function petMedicalAppointmentId(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => encrypt($value),
        );
    }

    protected function petId(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => encrypt($value),
        );
    }

    public function scopePet($query)
    {
        $pet = Pet::select('name')
            ->whereColumn('pet_id', 'pets.id')
            ->where('status', 1)
            ->limit(1);

        $query->addSelect(['pet' => $pet]);
    }
}
