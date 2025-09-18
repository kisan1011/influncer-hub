<?php

namespace App\Http\Requests\admin;

use App\Facade\CustomFacade;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CountryRequest extends FormRequest
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
            "name" => 'required|min:3|max:25|unique:countries,name',
            "code" => 'required|min:2|max:2|unique:countries,code',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => "Name must be required.",
            'name.unique' => "Name already exists.",
            'name.min' => 'Name minimum 3 character required.',
            'name.max' => 'Name maximum 25 character required.',

            'code.required' => "Code must be required.",
            'code.unique' => "Code already exists.",
            'code.min' => 'Code minimum 2 character required.',
            'code.max' => 'Code maximum 2 character required.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            CustomFacade::validatorError($validator)
        );
    }
}
