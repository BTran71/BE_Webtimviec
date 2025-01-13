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
        Schema::create('follownews', function (Blueprint $table) {
            $table->id(); // ID tự tăng cho bản ghi
            $table->unsignedBigInteger('candidate_id'); // Khóa ngoại tới bảng users
            $table->unsignedBigInteger('recruitment_news_id');
            $table->foreign('candidate_id')->references('id')->on('candidate_account')->onDelete('cascade');
            $table->foreign('recruitment_news_id')->references('id')->on('recruitment_news')->onDelete('cascade');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            $table->unique(['candidate_id', 'recruitment_news_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('follownews');
    }
};
