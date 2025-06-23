<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('benefit_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('benefit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('benefit_detail_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->dateTime('benefit_begin_time');
            $table->dateTime('benefit_end_time');
            $table->integer('is_approved')->default(0);
            $table->timestamps();
            $table->dateTime('approved_at')->nullable();
            $table->integer('approved_by')->nullable();
            $table->unique(['benefit_id', 'benefit_detail_id', 'user_id', 'benefit_begin_time'], 'unique_benefit_per_user');
        });

        DB::statement('ALTER TABLE benefit_user ADD CONSTRAINT end_time_earlier_begin_time CHECK (benefit_end_time > benefit_begin_time);');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('benefit_user');
    }
};
