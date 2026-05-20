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
         Schema::table('dentists', function (Blueprint $table) {
        $table->unsignedBigInteger('user_id')->nullable();
    });

    Schema::table('appointments', function (Blueprint $table) {
        $table->string('reschedule_reason')->nullable();
        $table->date('rescheduled_from')->nullable();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
   {
   Schema::table('dentists', function (Blueprint $table) {
        if (Schema::hasColumn('dentists', 'user_id')) {
            $table->dropColumn('user_id'); // no dropForeign since no constraint was added
        }
    });

    Schema::table('appointments', function (Blueprint $table) {
        if (Schema::hasColumn('appointments', 'reschedule_reason')) {
            $table->dropColumn(['reschedule_reason', 'rescheduled_from']);
        }
    });
}
};