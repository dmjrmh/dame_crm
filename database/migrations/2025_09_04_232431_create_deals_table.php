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
    Schema::create('deals', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')
        ->constrained()
        ->cascadeOnUpdate()
        ->restrictOnDelete();

      $table->foreignId('customer_id')
        ->nullable()
        ->constrained()
        ->cascadeOnUpdate()
        ->nullOnDelete();

      $table->foreignId('lead_id')
        ->nullable()
        ->constrained()
        ->cascadeOnUpdate()
        ->nullOnDelete();

      $table->string('title');
      $table->date('date');
      $table->decimal('amount', 18, 2)->default(0);

      $table->foreignId('pipeline_stage_id')
        ->nullable()
        ->constrained()
        ->cascadeOnUpdate()
        ->nullOnDelete();

      $table->string('approval_status', 20)->default('none');     // none|pending|approved|rejected
      $table->timestamp('approved_at')->nullable();
      $table->foreignId('approver_id')
        ->nullable()
        ->constrained('users')
        ->cascadeOnUpdate()
        ->nullOnDelete();
      $table->text('approval_notes')->nullable();

      $table->text('notes')->nullable();

      $table->index(['user_id']);
      $table->index(['customer_id']);
      $table->index(['lead_id']);
      $table->timestamps();
      $table->softDeletes();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('deals');
  }
};
