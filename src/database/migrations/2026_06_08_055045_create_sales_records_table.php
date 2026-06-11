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
        Schema::create('sales_records', function (Blueprint $table) {
            $table->id();
            // nullを許容せず、紐づく親データの物理削除を絶対に禁止（restrict）する
            $table->foreignId('seller_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('buyer_id')->constrained('users')->onDelete('restrict');
            // 同じ商品IDが2回以上登録されるのを完全に防ぐ（unique を追加）
            $table->foreignId('item_id')->unique()->constrained('items')->onDelete('restrict');
            $table->unsignedTinyInteger('payment_method');
            $table->unsignedInteger('purchase_price');
            $table->string('shipping_postcode',7);
            $table->string('shipping_address',255);
            $table->string('shipping_building',255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_records');
    }
};
