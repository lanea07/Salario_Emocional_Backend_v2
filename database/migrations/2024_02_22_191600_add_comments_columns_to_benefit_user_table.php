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
        Schema::table('benefit_user', function (Blueprint $table) {
            $table->text('request_comment')->nullable();
            $table->text('decision_comment')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('benefit_user', function (Blueprint $table) {
            $table->dropColumn('request_comment')->nullable();
            $table->dropColumn('decision_comment')->nullable();
        });
    }
};
