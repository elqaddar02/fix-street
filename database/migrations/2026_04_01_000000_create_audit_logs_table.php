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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');
            $table->string('action'); // Create, Update, Delete, Approve, Reject, etc.
            $table->string('model'); // Category, Report, User, Comment, etc.
            $table->unsignedBigInteger('model_id')->nullable(); // ID of the affected model
            $table->text('description'); // Human readable description
            $table->string('ip_address')->nullable();
            $table->timestamps();

            // Index for performance
            $table->index('admin_id');
            $table->index('created_at');
            $table->index(['model', 'model_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
