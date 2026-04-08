<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_aplications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('projects_id');
            $table->unsignedBigInteger('teams_id');
            $table->unsignedBigInteger('categories_id');

            $table->enum('status', [
                'draft',
                'submitted',
                'formal_check',
                'evaluation',
                'revision',
                'approved',
                'rejected',
                'onboarding'
            ])->default('draft');

            $table->text('motivation');
            $table->text('note')->nullable();
            $table->dateTime('applied_at')->nullable();
            $table->timestamps();

            $table->foreign('projects_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('teams_id')->references('id')->on('teams')->onDelete('cascade');
            $table->foreign('categories_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_aplications');
    }
};
