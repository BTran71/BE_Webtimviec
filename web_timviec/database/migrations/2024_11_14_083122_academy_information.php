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
        Schema::create('academy_information', function (Blueprint $table) {
            $table->id();
            $table->string('schoolname');
            $table->string('company_name');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->unsignedBigInteger('profile_id'); 
            $table->foreign('profile_id')->references('id')->on('profile')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('academy_information');
    }
};
