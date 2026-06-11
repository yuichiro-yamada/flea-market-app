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
        Schema::create('items', function (Blueprint $table) {
            $table->id();

            // seller_id: 販売者ID（users.id と紐付け）
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->string('item_name',255);
            $table->string('brand_name',255)->nullable();
            $table->unsignedTinyInteger('condition');
            $table->text('item_detail');
            $table->string('item_image',255);
            $table->unsignedInteger('item_price');
            $table->unsignedTinyInteger('sales_status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
