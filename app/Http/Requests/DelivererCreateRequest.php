<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DelivererCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required',
            'action' => 'array',
            'value' => 'array',
            'columnName' => 'array',
            'columnNumber' => 'array',
            'changeTo' => 'array',
            'conditionColumnNumber' => 'array',
            'conditionValue' => 'array',
        ];
    }

    public function getName(): string
    {
        return $this->validated()['name'];
    }

    public function getImportRules(): array
    {
        return [
            'actions' => $this->validated()['action'],
            'values' => $this->validated()['value'],
            'columnNames' => $this->validated()['columnName'],
            'columnNumbers' => $this->validated()['columnNumber'],
            'changeTo' => $this->validated()['changeTo'],
            'conditionColumnNumber' => $this->validated()['conditionColumnNumber'],
            'conditionValue' => $this->validated()['conditionValue'],
        ];
    }
}
