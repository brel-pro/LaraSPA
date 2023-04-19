<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    public $api_prefix;
    public $site_sub_url;
    public $larave_index_blade_enable;
    public $larave_index_blade_prefix;
    public $admin_panel_enable;
    public $admin_panel_prefix;

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //
        parent::boot();

        $this->api_prefix = config('app.api_prefix', 'api/v1');
        $this->site_sub_url = config('app.site_sub_url');
        $this->larave_index_blade_enable = config('app.larave_index_blade_enable');
        $this->larave_index_blade_prefix = config('app.larave_index_blade_prefix');
        $this->admin_panel_enable = config('app.admin_panel_enable');
        $this->admin_panel_prefix = config('app.admin_panel_prefix');
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });

        $this->mapApiRoutes();

        $this->mapModulesRoutes();

        $this->mapWebRoutes();

        $this->mapSPARoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::prefix($this->site_sub_url)
             ->middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix($this->site_sub_url.$this->api_prefix)
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }

    /**
     * All non matchable resources we will show standard Vue page,.
     *
     * and redirect it through VueRoutes on client side
     *
     * @return void
     */
    protected function mapSPARoutes()
    {
        Route::namespace($this->namespace)
            ->middleware('web')
            ->group(function () {
                if ($this->larave_index_blade_enable == 1) {
                    Route::view($this->site_sub_url.$this->larave_index_blade_prefix, 'index');
                }

                if ($this->admin_panel_enable == 1) {
                    Route::view($this->site_sub_url.$this->admin_panel_prefix, 'spa')
                        ->where('any', '^(?!api).*');
                    Route::view($this->site_sub_url.$this->admin_panel_prefix.'/{any}', 'spa')
                        ->where('any', '^(?!api).*');
                }
            });
    }

    /**
     * Define the "modules" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapModulesRoutes()
    {
        $modules_folder = app_path('Modules');
        $modules = $this->getModulesList($modules_folder);

        foreach ($modules as $module) {
            $routesPath = $modules_folder.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.'routes_api.php';

            if (file_exists($routesPath)) {
                Route::prefix($this->site_sub_url.$this->api_prefix)
                    ->middleware(['api', 'auth:sanctum'])
                    ->namespace("\\App\\Modules\\$module\Controllers")
                    ->group($routesPath);
            }
        }
    }

    /**
     * @param  string  $modules_folder
     * @return array
     */
    private function getModulesList(string $modules_folder): array
    {
        return
            array_values(
                array_filter(
                    scandir($modules_folder),
                    function ($item) use ($modules_folder) {
                        return is_dir($modules_folder.DIRECTORY_SEPARATOR.$item) && ! in_array($item, ['.', '..']);
                    }
                )
            );
    }
}
