<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('todo_list_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('notes')->nullable();
            // No due dates by design — this app is deliberately just a
            // list of things, done or not done.
            $table->timestamp('completed_at')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->index(['todo_list_id', 'position']);
            $table->index(['todo_list_id', 'completed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
