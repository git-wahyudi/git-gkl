<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidateCustomer extends FormRequest
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
            'nama'      => 'required|max:50',
            'nik'      	=> 'required|numeric|digits_between:16,20',
            'ttl'     	=> 'required|max:50',
            'jk' 		=> 'required',
            'alamat' 	=> 'required|max:150',
            'agama' 	=> 'required',
            'pekerjaan'	=> 'required|max:100',
            'telp' 		=> 'required|max:50'
        ];
    }
}
