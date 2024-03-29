<?php

use App\Models\LeadSource;
use App\Models\PipelineStage;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone_number')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignIdFor(LeadSource::class)->nullable()->constrained();
            $table->foreignIdFor(PipelineStage::class)->nullable()->constrained();
            $table->foreignIdFor(User::class, 'employee_id')->nullable()->constrained('users');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
