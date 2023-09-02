<?php

namespace App\Http\Controllers;

use App\Entities\NewsletterMessage;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NewsletterMessageController extends Controller
{
    public function create(Request $request): View
    {
        $message = NewsletterMessage::where('id', $request->get('message'))->first();

        return view('newsletter_messages.create', compact('message'));
    }

    public function store(Request $request): void
    {

    }
}
