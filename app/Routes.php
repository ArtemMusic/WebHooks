<?php
/**
 * Web Application Route boot
 * @author Vlad Ionov <vlad@f5.com.ru>
 */
namespace App;

class Routes extends \Core\Containers\RoutesContainer
{
	/**
	 * App WEB routes
	 * Enable cache by config app.cache_route
	 * CLI command: php cmd.php cache_route
	 * Will make cache in /app/Routes/cache.php
	 * @param Route $route
	 */
	protected function web(\Components\Route $route)
	{
        // https://tests.cmdf5.ru/dev6/oauth/amocrm/integration/redirect?code=
        $route->methods(['get','post'], 'oauth/amocrm/integration/redirect', 'App\Controllers\Crm\Oauth@redirectIntegration');
        $route->get('transfer', 'App\Controllers\TransferNumbers@index');








        // получение и обработка новой сделки
//        $route->post('api/lead', 'App\Controllers\CreateLeadHook@newLead');


		$route->get('login', 'App\Controllers\Auth\Authenticate@loginPage', 'LoginPage');
		$route->post('login', 'App\Controllers\Auth\Authenticate@attempt');
		$route->get('logout', 'App\Controllers\Auth\Authenticate@logout', 'Logout');

		$route->get('user/{user_id}/{user}', 'App\Controllers\Page@user');

		$route->get('index', 'App\Controllers\Page@index');
		$route->get('page/example', 'App\Controllers\Page@example');
		$route->get('page/test', 'App\Controllers\Page@test');

		$route->get('tests', 'App\Controllers\Tests@index');
	}

	/**
	 * App CLI routes
	 * Enable cache by config app.cache_route
	 * CLI command: php cmd.php cache_route
	 * Will make cache in /app/Routes/cache.php
	 * @param Route $route
	 */
	protected function cli(\Components\Route $route)
	{
        // */5 * * * * php -d max_execution_time=68 -f/home/tests/web/tests.cmdf5.ru/public_html/dev6/index.php /cron/amo/syncContacts >/dev/null 2>&1
        $route->cli('cron/amo/syncContacts','App\Controllers\SyncContacts@index' );
	}
}
