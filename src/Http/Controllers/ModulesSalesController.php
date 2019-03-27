<?php

namespace Dorcas\ModulesSales\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Dorcas\Contactform\Models\ModulesSales;

class ModulesSalesController extends Controller {

    public function index()
    {
       return view('modules-sales::index');
    }


}