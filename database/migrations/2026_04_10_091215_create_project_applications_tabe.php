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
        Schema::create('project_applications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();

            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();

            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();

            $table->string('status', 45)->default('pending');
            $table->text('motivation')->nullable();
            $table->text('note')->nullable();
            $table->timestamp('applied_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_applications');
    }
};
