<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;

trait ImageTrait
{

    public function imageUpload(Request $request, $fieldname = 'image', $directory = 'images')
    {
        $image_name = time() . $request->id . '.' . $request->$fieldname->extension();
        if (!is_dir(public_path($directory))) {
            mkdir(public_path($directory), 0755, true);
        }
        $request->$fieldname->move(public_path($directory), $image_name);
        return $directory . '/'.$image_name;
    }

    public function imageDelete($image)
    {
        $image = str_replace(url('/public/'), '', $image);
        $path = base_path() . '/public' . $image;
        if (str_contains($image,'storage') && file_exists($path)) {
            unlink(public_path($image));
        }
    }

    public function inquiryImageUpload($image,$directory = 'images')
    {
      $image_name = time() . rand(0,9999) . '.' . $image->extension();
      if (!is_dir(public_path($directory))) {
          mkdir(public_path($directory), 0755, true);
      }
      $image->move(public_path($directory), $image_name);
      return $directory . '/'.$image_name;
    }
}
