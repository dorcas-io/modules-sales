<?php

namespace Dorcas\ModulesSales;
use Illuminate\Support\ServiceProvider;

class ModulesSalesServiceProvider extends ServiceProvider {

	public function boot()
	{
		$this->loadRoutesFrom(__DIR__.'/routes/web.php');
		$this->loadViewsFrom(__DIR__.'/resources/views', 'modules-sales');
	}

	public function register()
	{

	}

}


?>