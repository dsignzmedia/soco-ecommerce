<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('app_branding', function (Blueprint $table) {
            $table->id();
            $table->string('app_name')->default('The Skool Store');
            $table->string('logo_path')->nullable();
            $table->string('favicon_path')->nullable();
            $table->string('primary_color')->default('#490d59');
            $table->string('secondary_color')->default('#f7f2fb');
            $table->string('accent_color')->default('#f97316');
            $table->string('font_family')->nullable();
            $table->text('custom_css')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_branding');
    }
};

