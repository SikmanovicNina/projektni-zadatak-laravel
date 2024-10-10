<?php

use App\Models\Policy;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('policies', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->integer('period');
            $table->timestamps();
        });

        Policy::create(['name' => 'reservation period', 'period' => 7]);
        Policy::create(['name' => 'rental period', 'period' => 30]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('policies');
    }
};
