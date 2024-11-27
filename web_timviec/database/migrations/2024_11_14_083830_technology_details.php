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
        Schema::create('technology_details', function (Blueprint $table) {
            $table->id(); // ID tự tăng cho bản ghi
            $table->unsignedBigInteger('profile_id'); // Khóa ngoại tới bảng users
            $table->unsignedBigInteger('it_id');
            $table->foreign('profile_id')->references('id')->on('profile')->onDelete('cascade');
            $table->foreign('it_id')->references('id')->on('information_technology')->onDelete('cascade');
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
        Schema::dropIfExists('technology_details');
    }
};
