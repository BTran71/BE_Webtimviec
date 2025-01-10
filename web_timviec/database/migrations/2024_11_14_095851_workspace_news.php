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
        Schema::create('workspace_news', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('workplace_id');
            $table->foreign('workplace_id')->references('id')->on('workplace')->onDelete('cascade');
            $table->unsignedBigInteger('recruitment_news_id'); 
            $table->foreign('recruitment_news_id')->references('id')->on('recruitment_news')->onDelete('cascade');
            $table->string('homeaddress');
            $table->integer('score')->nullable();
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
        //
    }
};
