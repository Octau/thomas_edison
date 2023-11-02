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
        Schema::create('inventories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->bigInteger('amount');
            $table->string('type');
            $table->decimal('buy_price', 10);
            $table->decimal('sell_price', 10);
            $table->decimal('min_sell_price', 10);
            $table->string('note')->nullable();
            $table->softDeletesTz('deleted_at', 6);
            $table->timestampsTz(6);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
