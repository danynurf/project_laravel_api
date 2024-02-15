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
        Schema::create('hdr_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('buyer_user_id');
            $table->unsignedDouble('total_price');
            $table->timestamps();

            $table->foreign('buyer_user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hdr_orders');
    }
};
