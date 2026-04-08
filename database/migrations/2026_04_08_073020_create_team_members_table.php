<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();

            $table->foreignId('students_id')
                ->constrained('students')
                ->onDelete('cascade');

            $table->foreignId('teams_id')
                ->constrained('teams')
                ->onDelete('cascade');

            $table->string('member_role', 20);
            $table->dateTime('joined_at');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_members');
    }
};
