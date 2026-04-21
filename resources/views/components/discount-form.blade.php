<div>
    <form
        action="
            {{
                !empty($discount->id)
                ? route('discounts.update', ['discount' => $discount->id])
                : route('discounts.store')
            }}
        "
        method="post"
    >
        @csrf
        @method($discount->id ? 'PUT' : 'POST')

        <div class="form-group">
            <label for="description">Opis (opcjonalne)</label>
            <input
                value="{{ $discount->description }}"
                placeholder="Opis promocji"
                class="form-control"
                name="description"
                id="description"
            >
        </div>

        <div class="form-group">
            <label for="new_amount">Nowa cena</label>
            <input
                value="{{ $discount->new_amount }}"
                placeholder="Nowa cena"
                class="form-control"
                name="new_amount"
                id="new_amount"
            >
        </div>

        <div class="form-group">
            <label for="old_amount">Stara cena</label>
            <input
                value="{{ $discount->old_amount }}"
                placeholder="Stara cena"
                class="form-control"
                name="old_amount"
                id="old_amount"
            >
        </div>


        <label for="product_id">Produkt</label>
        <select class="select2" data-live-search="true" name="product_id">
            @foreach($products as $product)
                <option value="{{ $product->id }}">
                    {{ $product->name }}
                </option>
            @endforeach
        </select>

        <button class="btn btn-primary">
            Zapisz
        </button>
    </form>
</div>
