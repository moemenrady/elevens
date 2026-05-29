<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            $table->enum('type', ['in', 'out']); // دخل ولا مصروف
            $table->decimal('amount', 10, 2);
            $table->foreignId('expense_type_id')
                ->nullable()
                ->constrained('expense_types')
                ->onDelete('set null');
            $table->foreignId('added_by')->constrained('users')->onDelete('cascade'); // الادمن اللي ضاف المصروف
            $table->foreignId('partner_id')->constrained()->cascadeOnDelete();
            $table->text('note')->nullable();
            $table->decimal('balance_before', 10, 2)->default(0);
            $table->decimal('balance_after', 10, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
