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
        Schema::create('users', function (Blueprint $table) {
            $table->id('id');
            $table->string('member_name',255);
            $table->string('email',255)->unique();
            $table->string('password',255);
            $table->string('member_image',255)->nullable();
            $table->string('postcode',7)->nullable();
            $table->string('address',255)->nullable();
            $table->string('building',255)->nullable();
            $table->string('shipping_postcode',7)->nullable();
            $table->string('shipping_address',255)->nullable();
            $table->string('shipping_building',255)->nullable();
            $table->unsignedTinyInteger('default_payment_method')->default(1);
            // 退会フラグ（デフォルトは退会していない状態の false）
            $table->boolean('is_withdrawn')->default(false);
            // メール認証を完了した日付
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
