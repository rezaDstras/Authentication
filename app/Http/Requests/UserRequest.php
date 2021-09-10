<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        switch ($this->method()) {
            case 'GET':
            case 'DELETE':
                return [];
            case 'POST':
                return [
                    'name' => 'required|string',
                    'lastName' => 'string',
                    'gender' => 'boolean',
                    'avatar' => 'sometimes|mimes:jpg,png,jpeg|max:3048'
                ];
            case 'PUT':
            case 'PATCH':
            return [
                'oldPassword' => 'required|string',
                'newPassword' => 'required|string',
            ];
            default:
                break;
        }
    }
}
