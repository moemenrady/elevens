<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {

        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('category_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name');

            $table->text('description')->nullable();

            $table->decimal('price', 10, 2);
            $table->decimal('cost', 8, 2);
            $table->unsignedInteger('quantity')->default(0);
            $table->unsignedInteger('min_quantity')->default(0);
            $table->boolean('is_produced')->default(0);

          
            $table->string('image')->nullable();

            $table->boolean('is_available')->default(true);

            $table->boolean('is_featured')->default(false);

            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->softDeletes();
        });
    }



    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
