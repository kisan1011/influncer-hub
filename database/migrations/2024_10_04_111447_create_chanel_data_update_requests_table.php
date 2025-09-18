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
        Schema::create('chanel_data_update_requests', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('chanel_id');
            $table->tinyInteger('type')->comment('0 = youtube, 1=instagram');
            $table->string('channel_name')->nullable();
            $table->string('username')->nullable();
            $table->string('account_type')->nullable();
            $table->bigInteger('category_id')->nullable();
            $table->string('email')->nullable();
            $table->bigInteger('video_count')->nullable();
            $table->bigInteger('view_count')->nullable();
            $table->bigInteger('subscriber_count')->nullable();
            $table->bigInteger('followers_count')->nullable();
            $table->bigInteger('follows_count')->nullable();
            $table->bigInteger('media_count')->nullable();
            $table->string('custom_url')->nullable();
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
        Schema::dropIfExists('chanel_data_update_requests');
    }
};
