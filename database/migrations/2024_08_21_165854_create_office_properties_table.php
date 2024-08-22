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
        Schema::create('office_properties', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id');
            $table->string('purpose');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('accompany_by')->nullable();
            $table->string('status')->default('Pending');
            $table->string('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('office_properties');
    }
};
