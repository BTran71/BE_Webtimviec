<?php

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
        Schema::create('profile', function (Blueprint $table) {
            $table->id();
            $table->string('fullname');
            $table->string('email')->unique();
            $table->string('image')->nullable();
            $table->integer('phone_number');
            $table->tinyInteger('gender')->default(0);
            $table->text('skills');
            $table->dateTime('day_ofbirth');
            $table->float('salary');
            $table->string('experience');
            $table->string('address');
            $table->unsignedBigInteger('candidate_id'); 
            $table->foreign('candidate_id')->references('id')->on('candidate_account')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile');
    }
};
