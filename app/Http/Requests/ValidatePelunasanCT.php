<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidatePelunasanCT extends FormRequest
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
            'cara_bayarP'    => 'required',
            'tgl_bayarP'     => 'required|date_format:d-m-Y',
            'potonganP'      => 'nullable|max:10|regex:/^[0-9]{1,3}(,[0-9]{3})*(\.[0-9]+)*$/',
            'jml_bayarP'     => 'required|max:20|regex:/^[0-9]{1,3}(,[0-9]{3})*(\.[0-9]+)*$/',
            'ketP'       	 => 'required|max:150'
        ];
    }
}
