<form action="{{ route('styro-lead.load-csv') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <input type="file" name="csv_file">

    <button>
        Zapisz plik
    </button>
</form>
