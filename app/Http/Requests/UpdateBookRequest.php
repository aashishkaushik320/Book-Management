<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'author' => ['sometimes', 'required', 'string', 'max:255'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'published_date' => ['sometimes', 'required', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'price.min' => 'Book price cannot be a negative value.',
            'cover_image.image' => 'Please upload a valid image file for the book cover.',
        ];
    }
}
