<?php

namespace App\Http\Requests\admin;

use App\Facade\CustomFacade;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CategoryRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize()
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, mixed>
   */
  public function rules()
  {
    return [
      "id" => 'nullable|exists:categories,id',
      "name" => 'required|min:3|max:25|unique:categories,name,' . $this->id,
      'logo' => 'required_without:id|image|mimes:jpeg,png,jpg|max:5120',
    ];
  }

  public function messages()
  {
    return [
      'id.exists' =>   "Category not found.",
      'name.required' => "Name must be required.",
      'name.regex' => "Name only characters allowed.",
      'name.unique' => "Name already exists.",
      'name.min' => 'Name minimum 3 character required.',
      'name.max' => 'Name maximum 25 character required.',
      'logo.required_without' => 'Logo must be required.',
      'logo.image' => 'Logo allowed only Image.',
      'logo.mimes' => 'Profile allowed only jpeg,png,jpg format.',
      'logo.max' => 'Logo allowed maximum 5 MB size.',
    ];
  }

  protected function failedValidation(Validator $validator)
  {
    throw new HttpResponseException(
      CustomFacade::validatorError($validator)
    );
  }
}
