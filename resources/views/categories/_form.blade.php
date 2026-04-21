{{-- Shared form partial for create and edit --}}
@php $category = $category ?? null; @endphp

<div class="form-group">
    <label for="name">Nazwa <span class="text-danger">*</span></label>
    <input type="text" class="form-control" id="name" name="name" maxlength="191"
           value="{{ old('name', $category?->name) }}" required>
    <small class="text-muted">Maksymalnie 191 znaków.</small>
</div>

<div class="form-group">
    <label for="description">Opis</label>
    <textarea class="form-control" id="description" name="description" rows="4">{{ old('description', $category?->description) }}</textarea>
</div>

<div class="form-group">
    <label for="parent_id">Kategoria nadrzędna</label>
    <select class="form-control" id="parent_id" name="parent_id">
        <option value="">— brak (kategoria główna) —</option>
        @foreach($parents as $pid => $pname)
            <option value="{{ $pid }}" {{ old('parent_id', $category?->parent_id) == $pid ? 'selected' : '' }}>
                {{ $pname }}
            </option>
        @endforeach
    </select>
    <small class="text-muted">
        Wybierz kategorię nadrzędną lub zostaw puste, aby stworzyć kategorię główną (poziom 1).
        Kategorie poziomu 2 mogą mieć podkategorie (poziom 3) — to maksymalny poziom zagłębienia.
    </small>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="priority">Priorytet (kolejność)</label>
            <input type="number" class="form-control" id="priority" name="priority" min="0" max="9999"
                   value="{{ old('priority', $category?->priority ?? 0) }}">
            <small class="text-muted">Niższa liczba = wyżej na liście.</small>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="img">URL zdjęcia</label>
            <input type="text" class="form-control" id="img" name="img" maxlength="191"
                   value="{{ old('img', $category?->img) }}" placeholder="https://...">
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label>
                <input type="hidden" name="is_visible" value="0">
                <input type="checkbox" name="is_visible" value="1"
                       {{ old('is_visible', $category?->is_visible ?? true) ? 'checked' : '' }}>
                Widoczna na stronie
            </label>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>
                <input type="hidden" name="save_name" value="0">
                <input type="checkbox" name="save_name" value="1"
                       {{ old('save_name', $category?->save_name ?? true) ? 'checked' : '' }}>
                Zachowaj nazwę
            </label>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>
                <input type="hidden" name="save_description" value="0">
                <input type="checkbox" name="save_description" value="1"
                       {{ old('save_description', $category?->save_description ?? true) ? 'checked' : '' }}>
                Zachowaj opis
            </label>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>
                <input type="hidden" name="save_image" value="0">
                <input type="checkbox" name="save_image" value="1"
                       {{ old('save_image', $category?->save_image ?? true) ? 'checked' : '' }}>
                Zachowaj zdjęcie
            </label>
        </div>
    </div>
</div>

{{-- YouTube section --}}
<hr>
<h4>Filmy YouTube <small class="text-muted">(maksymalnie 10)</small></h4>
<div id="youtube-entries">
    @php $ytItems = old('youtube', $youtube ?? []); @endphp
    @foreach($ytItems as $index => $yt)
        <div class="youtube-entry panel panel-default" style="padding:10px; margin-bottom:10px;">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group" style="margin-bottom:5px;">
                        <label>Link YouTube <span class="text-danger">*</span></label>
                        <input type="url" class="form-control" name="youtube[{{ $index }}][link]"
                               maxlength="191" value="{{ $yt['link'] ?? '' }}"
                               placeholder="https://youtu.be/...">
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group" style="margin-bottom:5px;">
                        <label>Opis</label>
                        <input type="text" class="form-control" name="youtube[{{ $index }}][description]"
                               maxlength="500" value="{{ $yt['description'] ?? '' }}"
                               placeholder="Opis filmu (opcjonalnie)">
                    </div>
                </div>
                <div class="col-md-1" style="padding-top:25px;">
                    <button type="button" class="btn btn-danger btn-sm remove-youtube">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    @endforeach
</div>

<button type="button" id="add-youtube" class="btn btn-default btn-sm">
    <i class="fa fa-plus"></i> Dodaj film
</button>

<template id="youtube-template">
    <div class="youtube-entry panel panel-default" style="padding:10px; margin-bottom:10px;">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group" style="margin-bottom:5px;">
                    <label>Link YouTube <span class="text-danger">*</span></label>
                    <input type="url" class="form-control" name="youtube[__INDEX__][link]"
                           maxlength="191" placeholder="https://youtu.be/...">
                </div>
            </div>
            <div class="col-md-5">
                <div class="form-group" style="margin-bottom:5px;">
                    <label>Opis</label>
                    <input type="text" class="form-control" name="youtube[__INDEX__][description]"
                           maxlength="500" placeholder="Opis filmu (opcjonalnie)">
                </div>
            </div>
            <div class="col-md-1" style="padding-top:25px;">
                <button type="button" class="btn btn-danger btn-sm remove-youtube">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        </div>
    </div>
</template>
