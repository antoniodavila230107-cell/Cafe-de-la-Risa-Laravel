<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->enum('job_role', ['mesero', 'recepcionista', 'cocinero']);
            $table->decimal('salary', 10, 2)->default(0);
            $table->enum('salary_period', ['mensual', 'quincenal', 'diario'])->default('mensual');
            $table->string('register_station')->nullable(); // Para recepcionistas
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('employee_table', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('table_id')->constrained('tables')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_table');
        Schema::dropIfExists('employees');
    }
};
