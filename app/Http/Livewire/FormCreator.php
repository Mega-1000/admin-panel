<?php

namespace App\Http\Livewire;

use App\Entities\Form;
use App\FormElement;
use Illuminate\View\View;
use Livewire\Component;

class FormCreator extends Component
{
    public string $name = '';
    public string $description = '';
    public ?Form $form = null;
    public bool $isModalOpen = false;
    public array $newElement = [];
    public string $type = '';

    public array $elementTypes = [
        'button' => [
            'inputs' => [
                [
                    'type' => 'text',
                    'label' => 'Text',
                    'placeholder' => 'Text',
                    'name' => 'text',
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'label' => 'Color',
                    'placeholder' => 'Color',
                    'name' => 'color',
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'label' => 'Size',
                    'placeholder' => 'Size',
                    'name' => 'size',
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'label' => 'AKCJA',
                    'placeholder' => 'AKCJA',
                    'name' => 'action',
                    'required' => true,
                ],
            ]
        ],
        'text' => [
            'inputs' => [
                [
                    'type' => 'text',
                    'label' => 'Zawartość tekstu',
                    'placeholder' => 'Zawartość tekstu',
                    'name' => 'text',
                    'required' => true,
                ],
            ]
        ]
    ];

    public function render(): View
    {
        $formId = request()->query('form_id');
        $this->form = $this->form ?? Form::find($formId);

        $this->name = $this->form?->name ?? $this->name;
        $this->description = $this->form?->description ?? $this->description;

        return view('livewire.form-creator');
    }

    public function submitForm(): mixed
    {
        $this->validate([
            'name' => 'required',
            'description' => 'required',
        ]);


        $this->form = Form::create([
            'name' => $this->name,
            'description' => $this->description,
        ]);

        return redirect()->to('/admin/form-creator/create?form_id=' . $this->form->id);
    }

    public function selectType(): void
    {
        $this->validate([
            'type' => 'required',
        ]);

        $this->newElement = array_map(function ($input) {
            $res = [];
            foreach ($input as $value) {
                $res[$value['name']] = '';
            }
            return $res;
        }, $this->elementTypes[$this->type]);
    }

    public function showModal(): void
    {
        $this->isModalOpen = true;

        $this->createNewElement();
    }

    public function closeModal(): void
    {
        $this->isModalOpen = false;
    }

    public function createNewElement(): void
    {
        $this->validate([
            'newElement' => 'required',
        ]);

        $this->form->elements()->create($this->newElement + ['type' => $this->type]);

        $this->closeModal();

        $this->reloafForm();
    }

    public function deleteElement(int $itemId): void
    {
        $this->form->elements()->where('id', $itemId)->delete();

        $this->reloafForm();
    }

    private function reloafForm(): void
    {
        $this->form = Form::find($this->form->id);
    }

    public function updateElementOrder($list)
    {
        foreach ($list as $item) {
            $element = FormElement::where('id', (int)$item['value'])->first();
            $element->update(['order' => $item['order']]);
        }

        $this->reloafForm();
    }
}
