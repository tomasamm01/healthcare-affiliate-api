<?php

use App\Enums\AffiliateStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliates', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('dni')->unique();
            $table->enum('status', array_column(AffiliateStatus::cases(), 'value'));
            $table->foreignId('plan_id')->constrained()->onDelete('restrict');
            $table->foreignId('holder_id')->nullable()->constrained('affiliates')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliates');
    }
};
