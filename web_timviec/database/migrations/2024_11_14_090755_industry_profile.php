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
        Schema::create('industry_profile', function (Blueprint $table) {
            $table->id(); // ID tự tăng cho bản ghi
            $table->unsignedBigInteger('profile_id'); // Khóa ngoại tới bảng users
            $table->unsignedBigInteger('industry_id');
            $table->foreign('profile_id')->references('id')->on('profile')->onDelete('cascade');
            $table->foreign('industry_id')->references('id')->on('industry')->onDelete('cascade');
            $table->string('experience');
            $table->timestamps();
            $table->unique(['profile_id', 'industry_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('industry_profile');
    }
};
