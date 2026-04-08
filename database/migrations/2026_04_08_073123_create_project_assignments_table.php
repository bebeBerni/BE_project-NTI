<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_assignments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('projects_id')
                ->constrained('projects')
                ->onDelete('cascade');

            $table->foreignId('teams_id')
                ->constrained('teams')
                ->onDelete('cascade');

            $table->dateTime('assigned_at');

            $table->enum('status', ['assigned', 'in_progress', 'completed'])
                ->default('assigned');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_assignments');
    }
};
