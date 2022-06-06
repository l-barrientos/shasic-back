<?php

use App\Models\Artist;
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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('eventName');
            $table->date('eventDate');
            $table->string('eventLocation');
            $table->string('eventImage');
            $table->string('ticketsUrl')->nullable();
            $table->string('details')->nullable();
            $table->foreignIdFor(Artist::class, 'createdBy');
            $table->timestamps();
            // $table->foreign('createdBy')->references('id')->on('artists');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('events');
    }
};
