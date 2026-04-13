<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('decisions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('commission_id');

            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'revision'
            ])->default('pending');

            $table->text('comment')->nullable();
            $table->dateTime('decided_at')->nullable();
            $table->timestamps();


            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('commission_id')->references('id')->on('commissions')->onDelete('cascade');


        });
    }

    public function down(): void
    {
        Schema::dropIfExists('decisions');
    }
};
