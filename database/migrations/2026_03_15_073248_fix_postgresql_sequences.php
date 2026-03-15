<?php
// database/migrations/2024_xx_xx_xxxxxx_fix_postgresql_sequences.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only run for PostgreSQL
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }
        
        $tables = ['users', 'certificates'];
        
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                try {
                    DB::statement("
                        SELECT setval(
                            '{$table}_id_seq', 
                            COALESCE((SELECT MAX(id) FROM {$table}), 1)
                        )
                    ");
                    
                    // This will show in migration output
                    echo "Fixed sequence for table: {$table}\n";
                    
                } catch (\Exception $e) {
                    echo "Could not fix sequence for table {$table}: " . $e->getMessage() . "\n";
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nothing to revert
    }
};