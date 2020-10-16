<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidatePelunasan extends FormRequest
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
            'potonganP'      => 'nullable|max:20|regex:/^[0-9]{1,3}(,[0-9]{3})*(\.[0-9]+)*$/',
            'catatanP'       => 'nullable|max:150'
        ];
    }
}
