<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EntryPost extends FormRequest
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
            'train_id'   =>'required',
            'contract_no'=>'required',
            'park_name'  =>'required',
        ];
    }

    public function messages()
    {
        return [
            'train_id.required' => '请选择培训主题',
        ];
    }
}
