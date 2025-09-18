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
          $table->string('username')->nullable()->after('channel_name');
          $table->string('account_type')->nullable()->after('username');
          $table->bigInteger('followers_count')->default(0)->after('subscriber_count');
          $table->bigInteger('follows_count')->default(0)->after('followers_count');
          $table->bigInteger('media_count')->default(0)->after('follows_count');
          $table->tinyInteger('type')->default(0)->after('id')->comment('0 = youtube, 1=instagram');
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
            $table->dropColumn('username');
            $table->dropColumn('account_type');
            $table->dropColumn('followers_count');
            $table->dropColumn('follows_count');
            $table->dropColumn('media_count');
            $table->dropColumn('type');
        });
    }
};
