<?php

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
        Schema::create('artists', function (Blueprint $table) {
            $table->id();
            $table->string('userName')->unique();
            $table->string('email')->unique();
            $table->string('fullName');
            $table->string('password');
            $table->string('profileImage');
            $table->string('location')->nullable();
            $table->text('bio')->nullable();
            $table->string('access_token');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('artists');
    }
};
