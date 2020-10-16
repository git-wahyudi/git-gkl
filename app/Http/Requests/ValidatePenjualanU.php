<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidatePenjualanU extends FormRequest
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
            'penjualU'           => 'required|numeric',
            'konsumenU'      	=> 'required|numeric',
            'projectU'      	=> 'required|numeric',
            'tanggalU'     		=> 'required|date_format:d-m-Y',
            'tipeU' 			=> 'required|max:10',
            'saksi1U'           => 'required|min:2|max:50',
            'saksi2U'           => 'required|min:2|max:50'
        ];
    }
}
