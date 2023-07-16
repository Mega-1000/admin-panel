<?php

namespace App\Http\Controllers;

use App\MailReport;
use Illuminate\View\View;

class MailReportController extends Controller
{
    /**
     * @return View
     */
    public function index(): View
    {
        return view('mail-reports.index', [
            'mailReports' => MailReport::paginate(30),
        ]);
    }
}
