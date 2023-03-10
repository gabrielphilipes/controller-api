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
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')
                ->constrained('business')
                ->onDelete('cascade');
            $table->string('name');
            $table->string('email')->unique();
            $table->json('permissions')->default('["*"]');
            $table->string('password');
            $table->enum('status', ['active', 'blocked', 'delete'])->default('active');
            $table->string('timezone')->nullable();
            $table->string('language')->nullable();
            $table->timestampsTz();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
