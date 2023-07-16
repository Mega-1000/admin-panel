<?php
namespace App\Http\Requests;

use App\Enums\EmailSettingsEnum;
use Illuminate\Foundation\Http\FormRequest;

class EmailSettingsCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {

        $validStatuses = array_flip(EmailSettingsEnum::getAllStatuses());

        return [
            'status' => 'required|in:'.implode(',', $validStatuses),
            'time' => 'required|numeric',
            'title' => 'required',
            'content' => 'required',
            'is_allegro' => 'required|in:on,of'
        ];
    }

    public function validated($key = null, $default = null): array
    {
        $validated = parent::validated();
        $validated['is_allegro'] = $validated['is_allegro'] === 'on';
        return $validated;
    }
}
