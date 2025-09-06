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
    Schema::create('deal_items', function (Blueprint $table) {
      $table->id();
      $table->foreignId('deal_id')
        ->constrained('deals')
        ->cascadeOnUpdate()
        ->cascadeOnDelete();

      $table->foreignId('product_id')
        ->constrained()
        ->cascadeOnUpdate()
        ->restrictOnDelete();

      $table->unsignedInteger('quantity')->default(1);
      $table->decimal('unit_price', 18, 2)->default(0);

      $table->softDeletes();
      $table->timestamps();

      $table->index(['deal_id']);
      $table->index(['product_id']);
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('deal_items');
  }
};
