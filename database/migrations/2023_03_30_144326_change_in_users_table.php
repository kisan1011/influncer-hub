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
        Schema::table('users', function (Blueprint $table) {
          $table->dropColumn('video_count');
          $table->dropColumn('view_count');
          $table->dropColumn('subscriber_count');
          $table->renameColumn('email_visible', 'is_profile_visible');
          $table->tinyInteger('promotional_notification')->after('email_notification')->default(1)->comment('0 = notify disable, 1 = notify enable');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
          $table->string('video_count')->after('role_id')->default(0);
          $table->string('view_count')->after('video_count')->default(0);
          $table->string('subscriber_count')->after('view_count')->default(0);
          $table->renameColumn('is_profile_visible', 'email_visible');
          $table->dropColumn('promotional_notification');
        });
    }
};
