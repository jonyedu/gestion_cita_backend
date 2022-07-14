<?php

use App\Models\Pet;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('pet_medical_appointments')) {
            Schema::create('pet_medical_appointments', function (Blueprint $table) {
                $table->id();
                $table->foreignIdFor(Pet::class)->constrained();
                $table->date('registration_date');
                $table->time('registration_time');
                $table->integer('turn');
                /* Datos para auditar */
                $table->timestamps();
                $table->integer('created_usu')->nullable()->references('id')->on('users')->default(1);
                $table->integer('updated_usu')->nullable()->references('id')->on('users')->default(1);
                $table->ipAddress('created_ip')->nullable()->default('127.0.0.1');
                $table->ipAddress('updated_ip')->nullable()->default('127.0.0.1');
                $table->boolean('is_visible')->nullable()->default(true)->comment('activará o desactivará el registro');
                $table->boolean('status')->nullable()->default(true)->comment('eliminación logica');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pet_medical_appointments');
    }
};
