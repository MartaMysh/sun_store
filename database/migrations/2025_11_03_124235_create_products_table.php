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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('manufacturer')->index();
            $table->decimal('price', 10, 2)->index();
            $table->text('description')->nullable();
            $table->enum('category', ['battery','panel','connector'])->index();

            // Category-specific attributes
            $table->decimal('capacity', 12, 3)->nullable()->index();
            $table->decimal('power_output', 12, 3)->nullable()->index();
            $table->string('connector_type')->nullable()->index();

            $table->timestamps();
        });

        // --- PostgreSQL full-text index ---
        if (Schema::getConnection()->getDriverName() === 'pgsql') {
            DB::statement("
                CREATE INDEX products_fulltext_idx
                ON products
                USING GIN (
                    to_tsvector('simple', 
                        coalesce(name,'') || ' ' || coalesce(manufacturer,'') || ' ' || coalesce(description,'')
                    )
                );
            ");
        }

        // Add fulltext index for MySQL
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE products ADD FULLTEXT INDEX products_fulltext_name_manufacturer_description (name, manufacturer, description)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
