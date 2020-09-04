<?php

namespace Dorcas\ModulesServiceRequests;
use Illuminate\Support\ServiceProvider;

class ModulesServiceRequestsServiceProvider extends ServiceProvider {

	public function boot()
	{
		$this->loadRoutesFrom(__DIR__.'/routes/web.php');
		$this->loadViewsFrom(__DIR__.'/resources/views', 'modules-service-requests');
		$this->publishes([
			__DIR__.'/config/modules-service-requests.php' => config_path('modules-service-requests.php'),
		], 'dorcas-modules');
		/*$this->publishes([
			__DIR__.'/assets' => public_path('vendor/modules-service-requests')
		], 'dorcas-modules');*/
	}

	public function register()
	{
		//add menu config
		$this->mergeConfigFrom(
	        __DIR__.'/config/navigation-menu.php', 'navigation-menu.modules-service-requests.sub-menu'
	     );
	}

}


?>