<?php

use App\Models\Plan;
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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('description')->nullable();
            $table->longText('permission')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->bigInteger('period')->default(1);
            $table->enum('period_type', ['day', 'week', 'month', 'year'])->default('day');
            $table->timestamps();
        });

        $planLists =  [
              [
              'name' => 'Free',
              'description' => 'Free plan for 6 months',
              'permission' => [],
              'price' => 0.00,
              'period' => 6,
              'period_type' => 'month',
              'created_at' => now(),
              'updated_at' => now(),
          ],
      ];

      foreach ($planLists as $planDetail) {
          Plan::create($planDetail);
      }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plans');
    }
};
