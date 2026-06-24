<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function rules(): array
    {
      
        $hexColor = ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/'];

        return [
         
            'name' => ['sometimes', 'required', 'string', 'max:255'],

            'email' => [
                'sometimes',
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],

            'bio' => ['nullable', 'string', 'max:500'],

           
            'theme_bg' => $hexColor,
            'theme_surface' => $hexColor,
            'theme_text' => $hexColor,
            'theme_accent' => $hexColor,

            'cover_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'theme_bg.regex' => 'Background color valid hex code hovo joiye (e.g. #ffffff).',
            'theme_surface.regex' => 'Surface color valid hex code hovo joiye (e.g. #ffffff).',
            'theme_text.regex' => 'Text color valid hex code hovo joiye (e.g. #111111).',
            'theme_accent.regex' => 'Accent color valid hex code hovo joiye (e.g. #4f46e5).',
        ];
    }
}