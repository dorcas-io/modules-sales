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
		], 'dorcas-modules');
	
		/*$this->publishes([
			__DIR__.'/assets' => public_path('vendor/modules-sales')
		], 'dorcas-modules');*/
	}

	public function register()
	{
		//add menu config
		$this->mergeConfigFrom(
	        __DIR__.'/config/navigation-menu.php', 'navigation-menu.modules-sales.sub-menu'
	     );
		
	}

}


?>