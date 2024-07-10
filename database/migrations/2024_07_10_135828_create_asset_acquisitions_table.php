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
        Schema::create('asset_acquisitions', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id');
            $table->string('asset_acquisition_type_id');
            $table->string('device_number')->nullable();
            $table->string('name')->nullable();
            $table->date('applied_on');
            $table->date('return_on');
            $table->string('reason')->nullable();
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
        Schema::dropIfExists('asset_acquisitions');
    }
};
