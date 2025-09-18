<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('inquiries', function (Blueprint $table) {
      $table->id();
      $table->string('inquiry_id');
      $table->foreignId('channel_id')->constrained('channels');
      $table->foreignId('sender_id')->constrained('users');
      $table->foreignId('receiver_id')->constrained('users');
      $table->string('subject');
      $table->string('link')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('inquiries');
  }
};
