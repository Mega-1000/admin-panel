<form action="{{ route('styro-lead.load-csv') }}" method="POST" enctype="application/x-www-form-urlencoded">
    @csrf

    <input type="file" name="csv_file">

    <button>
        Zapisz plik
    </button>
</form>
