<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
      return view('contents.setting.index');
    }

    public function updateLogo(Request $request)
    {
      dd($request->file('image'));
    }
}
