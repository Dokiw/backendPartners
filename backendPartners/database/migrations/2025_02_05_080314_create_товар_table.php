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
        Schema::disableForeignKeyConstraints();

        Schema::create('товар', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->bigInteger('category')->nullable();
            $table->foreign('category')->references('id')->on('каталог');
            $table->boolean('eighteen')->nullable();
            $table->json('characters_in')->nullable();
            $table->json('image')->nullable();
            $table->string('articul')->nullable();
            $table->string('brand')->nullable();
            $table->text('description')->nullable();
            $table->bigInteger('price')->nullable();
            $table->string('Barcodes')->nullable();
            $table->bigInteger('length')->nullable();
            $table->bigInteger('Width')->nullable();
            $table->bigInteger('Height')->nullable();
            $table->bigInteger('Weight_product_with_pack')->nullable();
            $table->boolean('quality_document')->nullable();
            $table->text('quality_number')->nullable();
            $table->date('datafrom')->nullable();
            $table->date('databefore')->nullable();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('товар');
    }
};
