<?php

namespace Dorcas\ModulesSales\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Dorcas\Contactform\Models\ModulesSales;
use App\Dorcas\Hub\Utilities\UiResponse\UiResponse;
use App\Http\Controllers\HomeController;
use Hostville\Dorcas\Sdk;
use Illuminate\Support\Facades\Log;

class ModulesSalesController extends Controller {

    public function __construct()
    {
        parent::__construct();
        $this->data = [
            'page' => ['title' => 'Sales Module'],
            'header' => ['title' => 'Sales Module'],
            'selectedMenu' => 'sales'
        ];
    }

    public function index()
    {
    	$this->data['availableModules'] = HomeController::SETUP_UI_COMPONENTS;
    	return view('modules-sales::index', $this->data);
    }


}