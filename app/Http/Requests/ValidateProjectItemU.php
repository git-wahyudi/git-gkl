<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidateProjectItemU extends FormRequest
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
            'noU'        => 'required|max:20',
            'luasU'      => 'required|max:5|regex:/^[0-9]{1,3}(,[0-9]{3})*(\.[0-9]+)*$/',
            'hargaU'     => 'required|max:10|regex:/^[0-9]{1,3}(,[0-9]{3})*(\.[0-9]+)*$/'
        ];
    }
}
