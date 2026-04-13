<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_mentors', function (Blueprint $table) {
            $table->id();

            $table->foreignId('team_id')
                ->constrained('teams')
                ->onDelete('cascade');

            $table->foreignId('mentor_id')
                ->constrained('mentors')
                ->onDelete('cascade');

            $table->dateTime('assigned_at');
            $table->boolean('active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_mentors');
    }
};
