<?php


namespace App\Http\Traits\Premium;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

trait traitPremium
{
    public function index()
    {
        return view('premium.index', []);
    }

}
