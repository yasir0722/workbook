<?php

namespace App\Http\Requests;

use App\Models\FacebookPage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFacebookPageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var FacebookPage $page */
        $page = $this->route('page');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'facebook_page_id' => ['nullable', 'string', 'max:255', Rule::unique('facebook_pages')->ignore($page)],
            'username' => ['nullable', 'string', 'max:255', Rule::unique('facebook_pages')->ignore($page)],
            'url' => ['nullable', 'url', 'max:2048'],
            'category' => ['nullable', 'string', 'max:100'],
            'enabled' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ];
    }
}
