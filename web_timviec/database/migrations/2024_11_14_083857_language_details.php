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
        Schema::create('language_details', function (Blueprint $table) {
            $table->id(); // ID tự tăng cho bản ghi
            $table->unsignedBigInteger('profile_id'); // Khóa ngoại tới bảng users
            $table->unsignedBigInteger('language_id');
            $table->foreign('profile_id')->references('id')->on('profile')->onDelete('cascade');
            $table->foreign('language_id')->references('id')->on('language')->onDelete('cascade');
            $table->string('level');
            $table->integer('score')->nullable();
            $table->timestamps();
            $table->unique(['profile_id', 'language_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('language_details');
    }
};
