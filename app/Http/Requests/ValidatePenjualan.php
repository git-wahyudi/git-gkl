<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidatePenjualan extends FormRequest
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
            'penjual'       => 'required|numeric',
            'konsumen'      => 'required|numeric',
            'project'      	=> 'required|numeric',
            'tanggal'     	=> 'required|date_format:d-m-Y',
            'tipe' 			=> 'required|max:10',
            'saksi1'        => 'required|min:2|max:50',
            'saksi2'        => 'required|min:2|max:50'
        ];
    }
}
