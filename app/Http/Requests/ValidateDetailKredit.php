<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidateDetailKredit extends FormRequest
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
            'uang_muka'     => 'required|max:11|regex:/^[0-9]{1,3}(,[0-9]{3})*(\.[0-9]+)*$/',
            'cara_bayar'    => 'required',
            'tenor'     	=> 'required|numeric|min:3|max:100',
            'angsuran'     	=> 'required|max:11|regex:/^[0-9]{1,3}(,[0-9]{3})*(\.[0-9]+)*$/',
            'tgl_bayar'     => 'required|date_format:d-m-Y'
        ];
    }
}
