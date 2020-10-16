<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidateUpdatePengguna extends FormRequest
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
        return [
            'namaU'              => 'required|min:2|max:30',
            'statusU'            => 'required|numeric',
            'roleU'              => 'required',
            'statusU'            => 'required'
        ];
    }
}
