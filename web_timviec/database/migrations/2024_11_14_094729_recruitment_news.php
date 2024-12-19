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
            $table->decimal('salary', 10, 2);
            $table->date('deadline');
            // $table->string('status');
            $table->string('experience');
            $table->text('skills');
            $table->decimal('quantity', 10, 2);
            $table->string('workingmodel');
            $table->string('qualifications');
            $table->text('requirements');
            $table->string('rank');
            $table->tinyInteger('isActive')->default(1);
            $table->unsignedBigInteger('employer_id'); 
            $table->foreign('employer_id')->references('id')->on('employer_account')->onDelete('cascade');
            $table->foreign('package_id')->references('id')->on('job_posting_packages')->onDelete('cascade');
            $table->unsignedBigInteger('invoice_id')->nullable(); 
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
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
