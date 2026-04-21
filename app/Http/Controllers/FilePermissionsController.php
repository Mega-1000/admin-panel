<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class FilePermissionsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return RedirectResponse
     */
    public function __invoke(): RedirectResponse
    {
        $process = new Process(['sudo', 'chmod', '-R', '777', '/var/www/admin-mega/']);
        $process->run();

        if (!$process->isSuccessful()) {
            return redirect()->back()->with([
                'message' => 'Failed to change file permissions: ' . $process->getErrorOutput(),
                'alert-type' => 'error',
            ]);
        }

        return redirect()->back()->with([
            'message' => 'Ponownie ustawiono uprawnienia do plików.',
            'alert-type' => 'success',
        ]);
    }
}
