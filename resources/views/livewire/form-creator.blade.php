<div>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li class="mb-0">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <button class="btn btn-primary" wire:click="showModal">
        Dodaj nowy element
    </button>

    <form wire:submit.prevent="submitForm">
        <input class="form-control" wire:model="name" placeholder="nazwa formularza (Wyświetlana jako część linka)">

        <textarea class="form-control mt-4" wire:model="description" placeholder="opis formularza"></textarea>

        <button class="btn btn-primary">
            Zapisz
        </button>
    </form>

    <button wire:click="createButton">
        okej
    </button>

    <h3>
        Elementy
    </h3>

    <hr>

    <div wire:sortable="updateElementOrder" wire:sortable-group="form-elements">
        @foreach($this->form?->elements ?? [] as $element)
            <div wire:key="element-{{ $element->id }}" wire:sortable.item="{{ $element->id }}" title="{{ $element->tooltip ?? '' }}">
                <div class="flex w-100" wire:sortable.handle style="justify-content: space-between">
                    <div>
                        <div>
                            typ: {{ $element->type }}
                        </div>

                        <br>

                        <div>
                            tekst: {{ $element->text }}
                        </div>
                    </div>

                    <div>
                        <button wire:click="deleteElement({{ $element->id }})" class="btn btn-danger">
                            Usuń
                        </button>

                        <button wire:click="editElement({{ $element->id }})" class="btn btn-primary">
                            edytuj
                        </button>
                    </div>
                </div>

                <hr>
            </div>
        @endforeach
    </div>

    @if ($this->isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="modal-overlay absolute inset-0 bg-gray-900 opacity-50"></div>
            <div class="modal-container bg-white w-96 mx-auto rounded-lg shadow-lg z-50">
                <div class="modal-content py-4 px-6">
                    <form wire:submit.prevent="{{empty($this->editElement) ? 'createNewElement' : 'updateElement'}}">
                        <h3 class="text-lg font-semibold mb-4">{{ empty($this->editElement) ? 'Dodaj nowy element' : 'Edycja elementu'}}</h3>

                        <select wire:model="type" wire:input="selectType">
                            <option value="button">
                                Przycisk
                            </option>

                            <option value="text">
                                Tekst
                            </option>

                            <option value="link">
                                Link
                            </option>
                        </select>

                        <br>

                        @if (empty($this->editElement))
                            @foreach($this->type ? $this->elementTypes[$this->type]['inputs'] : [] as $input)
                                <label for="{{ $input['name'] }}" class="mt-4">
                                    {{ $input['label'] }}
                                </label>

                                <input
                                    type="{{ $input['type'] }}"
                                    class="{{ $input['type'] !== 'checkbox' ? 'form-control' : '' }}" wire:model="newElement.{{$input['name']}}"
                                    placeholder="{{$input['placeholder']}}"
                                >
                            @endforeach
                        @else
                            @foreach($this->type ? $this->elementTypes[$this->type]['inputs'] : [] as $input)
                                <label for="{{ $input['name'] }}" class="mt-4">
                                    {{ $input['label'] }}
                                </label>

                                <input
                                    type="{{ $input['type'] }}"
                                    class="{{ $input['type'] !== 'checkbox' ? 'form-control' : '' }}" wire:model="editElement.{{$input['name']}}"
                                    placeholder="{{$input['placeholder']}}"
                                >
                            @endforeach
                        @endif

                        <br>

                        <button class="btn btn-primary">
                            Zapisz
                        </button>
                    </form>
                    <button wire:click="closeModal" class="btn btn-danger">
                        @lang('product_stock_positions.confirmation_modal.cancel')
                    </button>
                </div>
            </div>
        </div>
    @endif

    <h3 style="margin-top: 50px">
        Podgląd formularza
    </h3>

    <div>
        @foreach($form->elements as $element)
            @if($element->type === 'button')
                <button class="btn" style="background-color: {{ $element->color }}; font-size: {{ $element->size }}; width: 100%">
                    {{ $element->text }}
                </button>
            @elseif($element->type === 'text')
                <div>
                    {{ $element->text }}
                </div>
            @elseif($element->type === 'link')
                <a href="{{ $element->action }}" target="{{ $element->new_tab ? '_blank' : '' }}" style="font-size: {{ $element->size }}; color: {{ $element->color }};">
                    {{ $element->text }}
                </a>
            @endif
        @endforeach
    </div>
</div>
