<?php

namespace App\Http\Requests\admin;

use App\Facade\CustomFacade;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProfileRequest extends FormRequest
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
            'id'   =>  'required|exists:users,id',
            'name'   =>  'required|max:30',
            'email'   =>  'required|email|unique:users,email,'. auth()->user()->id,
            'password'=> 'nullable|min:8|max:25',
            'confirm_password' => 'same:password',
            'profile' =>  'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ];
    }

    public function messages()
    {
        return [
            'id.required' => 'User id is required.',
            'id.exists' => 'User id is invalid.',
            'name.required' => 'Name is required.',
            'name.max' => 'Name maximum 30 character required.',
            'password.min' => 'Password minimum 8 character required.',
            'password.max' => 'Password allowed maximum 25 character.',
            'confirm_password.same' => 'Password and confirm password not match.',
            'profile.image' => 'Profile allowed olny image.',
            'profile.mimes' => 'Profile allowed only jpeg,png,jpg format.',
            'profile.max' => 'Profile allowed maximum 5 mb size.',
        ];
    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(
          CustomFacade::validatorError($validator)
        );
    }
}
