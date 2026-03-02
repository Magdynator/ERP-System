<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->string('refund_number')->unique();
            $table->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->string('currency', 3)->default('USD');
            $table->string('status', 50)->default('completed');
            $table->text('notes')->nullable();
            $table->date('refund_date');
            $table->timestamps();
            $table->softDeletes();

            $table->index('refund_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};
