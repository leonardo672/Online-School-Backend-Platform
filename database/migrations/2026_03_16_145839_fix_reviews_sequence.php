<?php
// database/migrations/2024_03_16_xxxxxx_fix_reviews_sequence.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only run for PostgreSQL
        if (DB::getDriverName() === 'pgsql') {
            // Get the maximum ID from reviews table
            $maxId = DB::table('reviews')->max('id') ?? 0;
            
            // Reset the sequence to the next available ID
            DB::statement("SELECT setval('reviews_id_seq', {$maxId})");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nothing to undo
    }
};