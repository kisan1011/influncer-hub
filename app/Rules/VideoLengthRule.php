<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\InvokableRule;

class VideoLengthRule implements InvokableRule
{
  /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
     */
    public function __invoke($attribute, $value, $fail)
    {
      $time = explode (":",$value);
      if($time[0] < 00 || strlen($time[1]) > 2 || $time[1] > 59 || $time[1] < 00){
        $fail('Average video upload duration length must be min:second format.');
      }
    }
}
