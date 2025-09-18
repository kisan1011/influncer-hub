<?php

namespace App\Http\Requests\admin;

use App\Facade\CustomFacade;
use App\Models\Staticpage;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StaticPageRequest extends FormRequest
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
      "role_id" => 'required|in:' . User::ROLE_INFLUENCER . ',' . User::ROLE_BUSINESS,
      "type" => 'required|in:' . Staticpage::TYPE_TERMS . ',' . Staticpage::TYPE_PRIVACY . ',' . Staticpage::TYPE_SAFETY . ',' . Staticpage::TYPE_REFUND . ',' . Staticpage::TYPE_DISCLAIMER . ',' . Staticpage::TYPE_DMCA_POLICY . ',' . Staticpage::TYPE_COOKIE_CONSENT . ',' . Staticpage::TYPE_ABOUT_US,
      "description" => 'required|min:3',
    ];
  }

  public function messages()
  {
    return [
      'role_id.required' =>   "Role id must be required.",
      'role_id.in' =>   "Role id is invalid.",
      'type.required' =>   "Type must be required.",
      'type.in' =>   "Type is invalid.",
      'description.required' => "Description must be required.",
      'description.min' => "Description minimum 3 character required.",
    ];
  }

  protected function failedValidation(Validator $validator)
  {
    throw new HttpResponseException(
      CustomFacade::validatorError($validator)
    );
  }
}
