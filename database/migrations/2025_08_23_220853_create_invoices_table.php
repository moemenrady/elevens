<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('invoices', function (Blueprint $table) {
      $table->id();
      $table->string('invoice_number')->unique();

      // ربط العميل
      $table->foreignId('client_id')
        ->nullable()
        ->constrained()
        ->nullOnDelete();

      // ربط المستخدم الذي أنشأ الفاتورة
      $table->foreignId('created_by')
        ->nullable()
        ->constrained('users')
        ->nullOnDelete();

      $table->decimal('total', 12, 2);
      $table->decimal('profit', 12, 2)->default(0);
      $table->text('notes')->nullable();
      $table->timestamps();

      // تم حذف الـ type من الفهرس هنا
      $table->index('client_id');
    });
  }
  public function down(): void
  {
    Schema::dropIfExists('invoices');
  }
};
