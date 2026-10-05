<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFacebookPageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'facebook_page_id' => ['nullable', 'string', 'max:255', 'unique:facebook_pages,facebook_page_id'],
            'username' => ['nullable', 'string', 'max:255', 'unique:facebook_pages,username'],
            'url' => ['nullable', 'url', 'max:2048'],
            'category' => ['nullable', 'string', 'max:100'],
            'enabled' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
