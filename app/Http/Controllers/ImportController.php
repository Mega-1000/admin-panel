<?php

namespace App\Http\Controllers;

use App\Jobs\ImportCsvFileJob;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * Class ImportController
 * @package App\Http\Controllers
 */
class ImportController extends Controller
{
    public function index()
    {
        $import = DB::table('import')->first();
        $importDone = DB::table('import')->get();
        $importDone = $importDone[1];

        return view('import.index', compact('import', 'importDone'));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function doImport()
    {
        $import = DB::table('import')->where('id', '=', 1)->where('processing', '=', true)->first();

        if ($import === null) {
            dispatch(new ImportCsvFileJob())->onConnection('database');

            DB::table('import')->where('id', '=', 1)->update(
                ['name' => 'Import products', 'processing' => true, 'last_import' => Carbon::now()]
            );

            return response()->json([
                'status' => '200',
                'message' => 'Import zostanie wykonany w ciągu kilku minut',
                'error' => false
            ]);
        } else {
            return response()->json([
                'status' => '200',
                'message' => 'Import jest w trakcie przetwarzania',
                'error' => false
            ]);
        }
    }

    public function store(Request $request) {
        // cache the file
        $file = $request->file('importFile');

        if (File::exists(public_path('Baza.csv'))) {
            File::delete(public_path('Baza.csv'));
        }
        $disk = Storage::disk();

        $disk->put('/public/Baza.csv', fopen($file, 'r+'));

        return redirect('/admin/import');
    }
}
