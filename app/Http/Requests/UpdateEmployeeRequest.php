<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $employee = $this->route('employee');

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|string|max:255|unique:employees,email,'.$employee->id,
            'gender' => 'required|in:male,female',
            'age' => 'required|integer|string|min:16|max:99',
            'phone' => 'nullable|numeric|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'role_id' => 'required|integer|exists:roles,id',
            'team_id' => 'required|integer|exists:teams,id',
        ];
    }
}
