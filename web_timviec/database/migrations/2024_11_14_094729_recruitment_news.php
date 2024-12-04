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
        Schema::create('recruitment_news', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('describe');
            $table->date('posteddate');
            $table->text('benefit');
            $table->decimal('salary');
            $table->date('deadline');
            $table->string('status');
            $table->string('experience');
            $table->text('skills');
            $table->decimal('quantity');
            $table->string('workingmodel');
            $table->string('qualifications');
            $table->text('requirements');
            $table->tinyInteger('isActive')->default(1);
            $table->unsignedBigInteger('employer_id'); 
            $table->foreign('employer_id')->references('id')->on('employer_account')->onDelete('cascade');
            $table->unsignedBigInteger('industry_id'); 
            $table->foreign('industry_id')->references('id')->on('industry')->onDelete('cascade');
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
        Schema::dropIfExists('recruitment_news');
    }
};
