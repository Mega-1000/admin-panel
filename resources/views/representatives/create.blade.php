<form action="{{ route('store-represents', $firm->id) }}" method="POST">
    @csrf
    <div id="products">
        @for ($i = 0; $i < 3; $i++)
            <div class="product-group">
                <h3>Product {{ $i + 1 }}</h3>
                <div class="form-group">
                    <label for="products[{{ $i }}][contact_info]">Informacje kontaktowe do przedstawiciela {{ $i }}</label>
                    <input type="text" name="products[{{ $i }}][contact_info]" class="form-control" required>
                </div>
            </div>
            <hr>
        @endfor
    </div>

    <button type="submit" class="btn btn-primary">Zapisz</button>
</form>
