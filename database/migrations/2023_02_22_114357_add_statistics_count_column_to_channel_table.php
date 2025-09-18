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
    Schema::table('channels', function (Blueprint $table) {
      $table->string('video_count')->after('video_length')->default(0);
      $table->string('view_count')->after('video_count')->default(0);
      $table->string('subscriber_count')->after('view_count')->default(0);
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('channels', function (Blueprint $table) {
      //
    });
  }
};
