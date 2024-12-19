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
        Schema::create('informationnews', function (Blueprint $table) {
            $table->id(); // ID tự tăng cho bản ghi
            $table->unsignedBigInteger('recruitment_news_id'); // Khóa ngoại tới bảng users
            $table->unsignedBigInteger('it_id');
            $table->foreign('recruitment_news_id')->references('id')->on('recruitment_news')->onDelete('cascade');
            $table->foreign('it_id')->references('id')->on('information_technology')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['recruitment_news_id','it_id']);
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
