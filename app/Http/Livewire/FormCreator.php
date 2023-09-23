<?php

namespace App\Http\Livewire;

use App\Entities\Form;
use App\FormElement;
use Illuminate\View\View;
use Livewire\Component;

class FormCreator extends Component
{
    public ?string $name = '';
    public ?string $description = '';
    public ?Form $form = null;
    public bool $isModalOpen = false;
    public array $newElement = [];
    public string $type = '';
    public array $editElement = [];

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
                    'type' => 'color',
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
        ],
        'link' => [
            'inputs' => [
                [
                    'type' => 'text',
                    'label' => 'Text',
                    'placeholder' => 'Text',
                    'name' => 'text',
                    'required' => true,
                ],
                [
                    'type' => 'color',
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
                    'label' => 'Link',
                    'placeholder' => 'link',
                    'name' => 'action',
                    'required' => true,
                ],
                [
                    'type' => 'checkbox',
                    'label' => 'Otwórz w nowej karcie',
                    'placeholder' => 'Otwórz w nowej karcie',
                    'name' => 'new_tab',
                    'required' => false,
                ],
            ]
        ],
    ];

    public function render(): View
    {
        $formId = request()->query('form_id');
        $this->form = $this->form ?? Form::find($formId);

        $this->name = !empty($this->name) ? $this->name : $this->form?->name;
        $this->description = !empty($this->description) ? $this->description : $this->form?->description;

        return view('livewire.form-creator');
    }

    public function submitForm(): mixed
    {
        $this->validate([
            'name' => 'required',
            'description' => 'required',
        ]);

        $data = [
            'name' => $this->name,
            'description' => $this->description,
        ];

        if (empty($this->form)) {
            $this->form = Form::create($data);

            return redirect()->to('/admin/form-creator/create?form_id=' . $this->form->id);
        }

        return $this->form->update($data);
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

    public function editElement(int $itemId): void
    {
        $this->editElement = $this->form->elements()->where('id', $itemId)->first()->toArray();
        $this->type = $this->editElement['type'];
        $this->showModal();
    }

    public function updateElement(): void
    {
        $this->validate([
            'editElement' => 'required',
        ]);

        $this->form->elements()->where('id', $this->editElement['id'])->update($this->editElement);

        $this->editElement = [];

        $this->closeModal();

        $this->reloafForm();
    }
}
