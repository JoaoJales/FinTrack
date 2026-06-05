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
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('type')->default('expense')->after('category_id');
            $table->foreignId('destination_account_id')
                ->nullable()
                ->after('account_id')
                ->constrained('accounts')
                ->cascadeOnDelete();

            $table->index(['user_id', 'type', 'date']);
            $table->index('destination_account_id');
        });

        DB::table('transactions')
            ->whereNotNull('category_id')
            ->update([
                'type' => DB::raw(
                    '(SELECT type FROM categories WHERE categories.id = transactions.category_id)'
                ),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['destination_account_id']);
            $table->dropIndex(['user_id', 'type', 'date']);
            $table->dropIndex(['destination_account_id']);
            $table->dropColumn(['type', 'destination_account_id']);
        });
    }
};
