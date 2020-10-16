<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidateRencanaBayar extends FormRequest
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
            'ket'    		=> 'required|max:150',
            'jml_bayar'   	=> 'required|max:10|regex:/^[0-9]{1,3}(,[0-9]{3})*(\.[0-9]+)*$/',
            'tgl_bayarR'     => 'required|date_format:d-m-Y'
        ];
    }
}
