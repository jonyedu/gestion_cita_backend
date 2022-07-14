<?php

use App\Models\People;
use App\Models\Pet;
use App\Models\PetType;
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
        if (!Schema::hasTable('pets')) {
            Schema::create('pets', function (Blueprint $table) {
                $table->id();
                $table->foreignIdFor(People::class)->constrained();
                $table->foreignIdFor(PetType::class)->constrained();
                $table->string('name', 100);
                $table->integer('age');
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
        Schema::dropIfExists('pets');
    }
};
