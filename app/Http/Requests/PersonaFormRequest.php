<?php

namespace ideas\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PersonaFormRequest extends FormRequest
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

            'nombre'=>'required|max:100',
            'tipo_documento'=>'max:20',
            'codigo'=>'max:20',
            'num_documento'=>'max:15',
            'direccion'=>'max:70',
            'telefono'=>'max:15',
            'email'=>'max:50',
        ];
    }
}
