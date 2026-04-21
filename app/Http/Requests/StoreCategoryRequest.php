<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'              => 'required|string|max:191',
            'description'       => 'nullable|string',
            'parent_id'         => ['nullable', 'integer', 'exists:categories,id', function ($attr, $value, $fail) {
                if (!$value) return;
                $parent = \App\Entities\Category::find($value);
                if ($parent && !is_null($parent->parent_id)) {
                    $grandparent = \App\Entities\Category::find($parent->parent_id);
                    if ($grandparent && !is_null($grandparent->parent_id)) {
                        $fail('Dozwolone są maksymalnie 3 poziomy kategorii.');
                    }
                }
            }],
            'img'               => 'nullable|string|max:191',
            'priority'          => 'nullable|integer|min:0|max:9999',
            'is_visible'        => 'nullable|boolean',
            'save_name'         => 'nullable|boolean',
            'save_description'  => 'nullable|boolean',
            'save_image'        => 'nullable|boolean',
            'youtube'           => 'nullable|array|max:10',
            'youtube.*.link'    => 'required|url|max:191',
            'youtube.*.description' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'          => 'Nazwa kategorii jest wymagana.',
            'name.max'               => 'Nazwa kategorii nie może przekraczać 191 znaków.',
            'parent_id.exists'       => 'Wybrana kategoria nadrzędna nie istnieje.',
            'youtube.max'            => 'Można dodać maksymalnie 10 filmów YouTube.',
            'youtube.*.link.required'=> 'Link YouTube jest wymagany.',
            'youtube.*.link.url'     => 'Link YouTube musi być prawidłowym adresem URL.',
            'youtube.*.link.max'     => 'Link YouTube nie może przekraczać 191 znaków.',
            'youtube.*.description.max' => 'Opis filmu nie może przekraczać 500 znaków.',
        ];
    }
}
