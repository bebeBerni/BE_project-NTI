<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title', 45);
            $table->text('description');
            $table->string('type', 45);
            $table->unsignedBigInteger('created_by_user_id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('team_id');
            $table->decimal('budget', 10, 2);
            $table->enum('status', [
                'pending',
                'active',
                'paused',
                'finished',
                'archived'
            ])->default('pending');
            $table->dateTime('deadline')->nullable();
            $table->timestamps();


            $table->foreign('created_by_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
