<?php

namespace Dorcas\ModulesSales;
use Illuminate\Support\ServiceProvider;

class ModulesSalesServiceProvider extends ServiceProvider {

	public function boot()
	{
		$this->loadRoutesFrom(__DIR__.'/routes/web.php');
		$this->loadViewsFrom(__DIR__.'/resources/views', 'modules-sales');
		$this->publishes([
			__DIR__.'/config/modules-sales.php' => config_path('modules-sales.php'),
		], 'config');
		$this->publishes([
			__DIR__.'/assets' => public_path('vendor/modules-sales')
		], 'public');
	}

	public function register()
	{

	}

}


?>