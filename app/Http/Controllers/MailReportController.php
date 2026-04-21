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

    public function getMailsByOrder($order_id)
    {
        return view('mail-reports.index', [
            'mailReports' => MailReport::where('body', 'like', '% ' . $order_id . '%')
                ->orWhere('subject', 'like', '% ' . $order_id . '%')
                ->orderBy('created_at', 'desc')
                ->paginate(100),
        ]);
    }
}
