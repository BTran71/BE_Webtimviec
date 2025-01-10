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
        Schema::create('industrynews', function (Blueprint $table) {
            $table->id(); // ID tự tăng cho bản ghi
            $table->unsignedBigInteger('recruitment_news_id'); // Khóa ngoại tới bảng users
            $table->unsignedBigInteger('industry_id');
            $table->foreign('recruitment_news_id')->references('id')->on('recruitment_news')->onDelete('cascade');
            $table->foreign('industry_id')->references('id')->on('industry')->onDelete('cascade');
            $table->integer('score')->nullable();
            $table->string('experience');
            $table->timestamps();
            $table->unique(['recruitment_news_id', 'industry_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
