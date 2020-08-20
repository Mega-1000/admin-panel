<?php

namespace App\Http\Controllers;

use App\Helpers\AllegroCommissionParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AllegroController extends Controller
{

    public function setCommission(Request $request)
    {
        try {
            $file = $request->file('file');
            $maxFileSize = 20000000;
            if ($file->getSize() > $maxFileSize) {
                return redirect()->route('orders.index')->with([
                    'message' => __('transport.errors.too-big-file'),
                    'alert-type' => 'error'
                ]);
            }
            $path = Storage::put('user-files/', $file);
            $path = Storage::path($path);

            if (($handle = fopen($path, "r")) === FALSE) {
                throw new \Exception('Nie można otworzyć pliku');
            }
            $parser = new AllegroCommissionParser();
            $parsedData = $parser->parseFile($handle);
            $errors = $parsedData['errors'];
            $newLetters = $parsedData['new_letters'];
            fclose($handle);
            Storage::delete($path);
            $route = redirect()->route('orders.index');
            if ($errors) {
                $route->with(
                    'allegro_commission_errors', $errors
                );
            }
            if ($newLetters) {
                $route->with('allegro_new_letters', $newLetters);
            }
            return $route->with(['message' => __('voyager.generic.successfully_updated'),
                'alert-type' => 'success']);
        } catch (\Exception $e) {
            return redirect()->route('orders.index')->with(['message' => __('voyager.generic.update_failed'),
                'alert-type' => 'error']);
        }
    }

    public function createNewLetter(Request $request)
    {
        $data = $request->all();
        $letters = json_decode($data['letters']);
        foreach ($letters as $letter) {
            $nr = $letter->letter_number;
            $amount = $letter->real_cost_for_company;
            $pack = AllegroCommissionParser::CreatePack($nr, $amount);
            AllegroCommissionParser::createNewPackage($pack);
        }
        return redirect()->route('orders.index')->with(['message' => __('voyager.generic.successfully_updated'),
            'alert-type' => 'success']);
    }

}
