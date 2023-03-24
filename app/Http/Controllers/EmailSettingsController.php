<?php

namespace App\Http\Controllers;

use App\Entities\EmailSetting;
use App\Enums\EmailSettingsEnum;
use App\Http\Requests\EmailSettingsCreateRequest;

class EmailSettingsController extends Controller
{
    /**
     * Show the return form of a specific resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $emailSettings = EmailSetting::get();
        return view('email_module.index',compact('emailSettings'));
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $status['NEW'] = EmailSettingsEnum::STATUS_NEW;
        $status['PRODUCED'] = EmailSettingsEnum::STATUS_PRODUCED;
        $status['PICKED_UP'] = EmailSettingsEnum::STATUS_PICKED_UP;
        $status['PROVIDED'] = EmailSettingsEnum::STATUS_PROVIDED;

        return view('email_module.create',compact('status'));
    }

    /**
     * @param EmailSettingsCreateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(EmailSettingsCreateRequest $request){

        $email_setting = new EmailSetting;
        $email_setting->fill($request->all());
        $email_setting->save();

        return redirect()->route('emailSettings')->with([
            'message' => 'Ustawienia E-mail dodane poprawnie!',
            'alert-type' => 'success'
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id){
        $emailSetting = EmailSetting::findOrFail($id);

        $status['NEW'] = EmailSettingsEnum::STATUS_NEW;
        $status['PRODUCED'] = EmailSettingsEnum::STATUS_PRODUCED;
        $status['PICKED_UP'] = EmailSettingsEnum::STATUS_PICKED_UP;
        $status['PROVIDED'] = EmailSettingsEnum::STATUS_PROVIDED;

        return view('email_module.edit',compact('emailSetting','status'));
    }

    /**
     * @param EmailSettingsCreateRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(EmailSettingsCreateRequest $request,$id){
        $emailSetting = EmailSetting::find($id);
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
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $emailSetting = EmailSetting::find($id);

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
