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
    Schema::create('invoice_items', function (Blueprint $table) {
$table->id();
        $table->unsignedBigInteger('invoice_id');
              $table->unsignedBigInteger('product_id')->nullable();

        // الخصائص الجديدة (تم حذف after)
        $table->string('color_name')->nullable();
        $table->string('size_name')->nullable();
        $table->boolean('is_printed')->default(false);

        // بيانات البند وقت البيع
        $table->string('name'); 
        $table->integer('qty')->default(1)->nullable();
        $table->decimal('price', 10, 2); 
        $table->decimal('cost', 10, 2)->default(0); 
        $table->decimal('total', 12, 2); 
        $table->string('description')->nullable(); 

        $table->timestamps();

        // العلاقات والفهارس
        $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
        $table->index(['invoice_id']);
    });
  }
  public function down(): void
  {
    Schema::dropIfExists('invoice_items');
  }
};
