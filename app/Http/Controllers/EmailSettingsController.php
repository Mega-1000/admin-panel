<?php

namespace App\Http\Controllers;

use App\Entities\EmailSetting;
use App\Enums\EmailSettingsEnum;
use App\Http\Requests\EmailSettingsCreateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use App\Entities\Tag;

class EmailSettingsController extends Controller
{
    /**
     * Show the return form of a specific resource.
     *
     * @return View
     */
    public function index(): View
    {
        $emailSettings = EmailSetting::get();
        return view('email_module.index', compact('emailSettings'));
    }

    /**
     * @return View
     */
    public function create(): View
    {
        $statuses = EmailSettingsEnum::getAllStatuses();

        $tags = Tag::all();

        return view('email_module.create', compact('statuses', 'tags'));
    }

    /**
     * @param EmailSettingsCreateRequest $request
     *
     * @return RedirectResponse
     */
    public function store(EmailSettingsCreateRequest $request): RedirectResponse {

        $email_setting = new EmailSetting;
        $email_setting->fill($request->all());
        $email_setting->save();

        return redirect()->route('emailSettings')->with([
            'message' => 'Ustawienia E-mail dodane poprawnie!',
            'alert-type' => 'success'
        ]);
    }

    /**
     * @param EmailSetting $emailSetting
     *
     * @return View
     */
    public function edit(EmailSetting $emailSetting): View {

        $statuses = EmailSettingsEnum::getAllStatuses();

        $tags = Tag::all();

        return view('email_module.edit', compact('emailSetting', 'statuses', 'tags'));
    }

    /**
     * @param  EmailSettingsCreateRequest $request
     * @param  EmailSetting               $emailSetting
     * @return RedirectResponse
     */
    public function update(EmailSettingsCreateRequest $request, EmailSetting $emailSetting): RedirectResponse {

        $emailSetting->fill($request->all());
        $emailSetting->save();

        return redirect()->route('emailSettings')->with([
            'message' => 'Ustawienia E-mail zapisany poprawnie!',
            'alert-type' => 'success'
        ]);
    }


    /**
     * Remove the specified resource.
     *
     * @param  EmailSetting     $emailSetting
     *
     * @return RedirectResponse
     */
    public function destroy(EmailSetting $emailSetting): RedirectResponse
    {
        if (empty($emailSetting)) {
            abort(404);
        }
        
        $emailSetting->delete();

        return redirect()->route('emailSettings')->with([
            'message' => 'Ustawienia E-mail usunięte!',
            'alert-type' => 'success'
        ]);
    }
}
