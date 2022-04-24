<?php

use App\Models\Artist;
use App\Models\Event;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('artist__event__performances', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Artist::class);
            $table->foreignIdFor(Event::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('artist__event__performances');
    }
};
