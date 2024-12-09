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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id(); // ID tự tăng
            $table->string('transaction_id')->unique(); // ID giao dịch từ cổng thanh toán
            $table->unsignedBigInteger('employer_id'); // ID người dùng (nếu có liên kết với người dùng)
            $table->decimal('amount', 10, 2); // Số tiền thanh toán
            $table->string('payment_method'); // Phương thức thanh toán (PayPal, Stripe, v.v.)
            $table->string('status')->default('pending'); // Trạng thái giao dịch (pending, success, failed)
            $table->timestamp('paid_at')->nullable(); // Thời gian thanh toán (nếu thành công)
            $table->timestamps(); // Tự động thêm created_at và updated_at

            // Nếu cần liên kết với bảng users
            $table->foreign('employer_id')->references('id')->on('employer')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
};
