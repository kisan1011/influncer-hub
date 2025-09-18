<?php

namespace Database\Seeders;

use App\Models\Audio;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AudioSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    Schema::disableForeignKeyConstraints();
    Audio::truncate();
    Schema::enableForeignKeyConstraints();

    $youtubeAudio = ['Hindi', 'Gujarati', 'English'];
    $record = [];
    foreach ($youtubeAudio as $key => $value) {
      $record[] = [
        'name' => $value,
        'slug' =>  Str::slug($value)
      ];
    }
    Audio::insert($record);
  }
}
