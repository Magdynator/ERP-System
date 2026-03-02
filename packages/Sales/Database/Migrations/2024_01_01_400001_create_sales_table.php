<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('sale_number')->unique();
            $table->foreignId('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->string('currency', 3)->default('USD');
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('status', 50)->default('completed');
            $table->text('notes')->nullable();
            $table->date('sale_date');
            $table->timestamps();
            $table->softDeletes();

            $table->index('sale_date');
            $table->index('branch_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
