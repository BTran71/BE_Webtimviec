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
        Schema::create('sending_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('recruitment_news_id'); 
            $table->foreign('recruitment_news_id')->references('id')->on('recruitment_news')->onDelete('cascade');
            $table->unsignedBigInteger('profile_id'); 
            $table->foreign('profile_id')->references('id')->on('profile')->onDelete('cascade');
            $table->date('senddate');
            $table->string('name');
            $table->string('status')->default('Đã gửi');
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
        Schema::dropIfExists('sending_details');
    }
};
