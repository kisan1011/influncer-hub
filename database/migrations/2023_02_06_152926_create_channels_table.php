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
    Schema::create('channels', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')->constrained('users');
      $table->string('channel_id');
      $table->string('channel_name')->nullable();
      $table->foreignId('category_id')->constrained('categories');
      $table->foreignId('audio_id')->constrained('audio');
      $table->string('email');
      $table->tinyInteger('upload_time');
      $table->string('image');
      $table->text('description');
      $table->string('minimum_price')->nullable();
      $table->string('video_length')->nullable();
      $table->date('published_at');
      $table->enum('status', ['1', '0'])->default('1')->comment("1 = Active, 0= Inactive");
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
    Schema::dropIfExists('channels');
  }
};
