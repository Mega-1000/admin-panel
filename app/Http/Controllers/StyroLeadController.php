<?php

namespace App\Http\Controllers;

use App\Facades\Mailer;
use App\Mail\StyroLeadInaugurationMail;
use App\StyroLead;
use App\StyroLeadMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StyroLeadController extends Controller
{
    public function index(): View
    {
        return view('styro-leads.index', ['leads' => StyroLead::all()]);
    }


    public function getLogoWithTracker($id): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $email = StyroLeadMail::find($id);

        // Check if the email exists
        if (!$email) {
            abort(404, 'Email not found');
        }
        $lead = StyroLead::find($email->styro_lead_id);

        $lead->email_read = true;
        $lead->save();

        $email->email_read = true;
        $email->save();

        $logoPath = public_path('logo.jpg');

        if (!file_exists($logoPath)) {
            abort(404, 'Logo file not found');
        }

        $headers = [
            'Content-Type' => 'image/jpeg',
            'Cache-Control' => 'max-age=3600, public',
        ];

        return response()->file($logoPath, $headers);
    }

    public function importCSV(Request $request): RedirectResponse
    {
        if ($request->hasFile('csv_file')) {
            $file = $request->file('csv_file');

            // Read the CSV file
            $csvData = array_map('str_getcsv', file($file->getRealPath()));

            // Remove the header row
            array_shift($csvData);

            foreach ($csvData as $row) {
                // Check if the row has at least one non-empty value
                if (count(array_filter($row)) > 0) {
                    $lead = StyroLead::create([
                        'phone' => $row[0] ?: null,
                        'firm_name' => $row[1] ?: null,
                        'email' => $row[3] ?: null,
                    ]);

                    $mail = $lead->mails()->create([]);
                    Mailer::create()
                        ->to($row[3])
                        ->send(new StyroLeadInaugurationMail(
                            $mail
                        ));
                }
            }
        }

        return redirect()->back();
    }

    public function visitWebsite($id): RedirectResponse
    {
        $lead = StyroLead::find(StyroLeadMail::find($id)->styro_lead_id);
        $lead->on_website = true;
        $lead->save();

        return redirect('https://mega1000.pl/styropiany');
    }
}
