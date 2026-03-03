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
        Schema::create('dealer_profiles', function (Blueprint $table) {
            $table->uuid('id')->primary;
			$table->string('business_name')->nullable();
			$table->string('phone_no')->nullable()->unique();
			$table->string('address_street')->nullable();
			$table->string('address_city')->nullable();
			$table->string('address_county')->nullable();
			$table->string('address_country')->nullable()->default('Kenya');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dealer_profiles');
    }
};
