<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // MySQL enum needs raw statement to alter allowed values
        DB::statement("
            ALTER TABLE `payments`
            MODIFY `payment_method`
            ENUM('bank_transfer','dana','gopay','cash','e_wallet','other')
            NOT NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum set
        DB::statement("
            ALTER TABLE `payments`
            MODIFY `payment_method`
            ENUM('bank_transfer','cash','e_wallet','other')
            NOT NULL
        ");
    }
};


