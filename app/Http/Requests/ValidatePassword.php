<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidatePassword extends FormRequest
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
    	$new_pass = $this->baru1;
        return [
            'lama'      => 'required|min:6',
            'baru1'     => 'required|min:6',
            'baru2'     => 'required|min:6|in:'.$new_pass
        ];
    }
}
