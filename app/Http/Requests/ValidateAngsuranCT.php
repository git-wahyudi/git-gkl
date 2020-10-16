<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidateAngsuranCT extends FormRequest
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
            'tgl_bayar'     => 'required|date_format:d-m-Y',
            'cara_bayar'    => 'required',
            'jml_bayar' 	=> 'required|max:10|regex:/^[0-9]{1,3}(,[0-9]{3})*(\.[0-9]+)*$/'
        ];
    }
}
