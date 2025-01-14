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
        Schema::create('follow', function (Blueprint $table) {
            $table->id(); // ID tự tăng cho bản ghi
            $table->unsignedBigInteger('candidate_id'); // Khóa ngoại tới bảng users
            $table->unsignedBigInteger('employer_id');
            $table->foreign('candidate_id')->references('id')->on('candidate_account')->onDelete('cascade');
            $table->foreign('employer_id')->references('id')->on('employer_account')->onDelete('cascade');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            $table->unique(['candidate_id', 'employer_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('follow');
    }
};
