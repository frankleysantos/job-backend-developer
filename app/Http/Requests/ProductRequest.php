<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class ProductRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }


    public function rules(Request $request)
    {
        if ($request->id)
            return [];
            
        return [
            'name'    => 'required',
            'price'    => 'required|numeric',
            'description'    => 'required',
            'category'    => 'required',
        ];
    }

    public function messages()
    {
        return [
            'name.required'     => 'O nome do produto é obrigatório!',
            'price.required'     => 'O preço é obrigatório!',
            'decription.required'     => 'A descrição é obrigatória!',
            'category.required'     => 'A categoria é obrigatório!',
        ];
    }
}
