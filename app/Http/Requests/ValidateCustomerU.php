<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidateCustomerU extends FormRequest
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
            'namaU'      	=> 'required|max:50',
            'nikU'      	=> 'required|numeric|digits_between:16,20',
            'ttlU'     		=> 'required|max:50',
            'jkU' 			=> 'required',
            'alamatU' 		=> 'required|max:150',
            'agamaU' 		=> 'required',
            'pekerjaanU'	=> 'required|max:100',
            'telpU' 		=> 'required|max:50'
        ];
    }
}
