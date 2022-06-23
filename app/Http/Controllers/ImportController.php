<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Entities\Import;

/**
 * Class ImportController
 * @package App\Http\Controllers
 */
class ImportController extends Controller
{
    public function index()
    {
        $import = Import::find(1);
        $importDone = Import::find(2);

        return view('import.index', compact('import', 'importDone'));
    }

    public function store(Request $request)
    {
        $file = $request->file('importFile');

        if (File::exists(Storage::path('user-files/baza/baza.csv'))) {
            \Session::flash('flash-message', ['type' => 'danger', 'message' => 'Plik już istnieje. Prosimy poczekać, aż zostanie przetworzony.']);
        } else {
            Storage::disk()->put('/user-files/baza/baza.csv', fopen($file, 'r+'));
            \Session::flash('flash-message', ['type' => 'success', 'message' => 'Plik został zapisany, import zostanie wykonany w ciągu kilku minut']);
        }

        return redirect(route('import.index'));
    }
}
