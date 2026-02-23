<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('privacy_policy', function (Blueprint $table) {
            $table->id();
            $table->longText('content')->nullable();
            $table->timestamps();
        });

        Schema::create('shipping_policy', function (Blueprint $table) {
            $table->id();
            $table->longText('content')->nullable();
            $table->timestamps();
        });

        Schema::create('terms_conditions', function (Blueprint $table) {
            $table->id();
            $table->longText('content')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('privacy_policy');
        Schema::dropIfExists('shipping_policy');
        Schema::dropIfExists('terms_conditions');
    }
};
