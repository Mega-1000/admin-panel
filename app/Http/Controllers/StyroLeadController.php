<?php

namespace App\Http\Controllers;

use App\Facades\Mailer;
use App\Mail\StyroLeadInaugurationMail;
use App\StyroLead;
use App\StyroLeadMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StyroLeadController extends Controller
{
    public function getLogoWithTracker(Request $request)
    {
        // Get the email ID from the request query parameter
        $emailId = $request->input('id');

        // Find the email by ID
        $email = StyroLeadMail::find($emailId);

        // Check if the email exists
        if (!$email) {
            abort(404, 'Email not found');
        }

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
                        'website_url' => $row[2] ?: null,
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
}
