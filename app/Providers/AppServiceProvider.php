<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use App\Models\General\Menu;
use App\Models\Api\AccessToken;
use Laravel\Sanctum\Sanctum;
use Illuminate\Pagination\Paginator;
use App\Classes\CustomPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator as LengthAwarePaginatorContract;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->alias(CustomPaginator::class, LengthAwarePaginator::class);
        $this->app->alias(CustomPaginator::class, LengthAwarePaginatorContract::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //Sanctum::usePersonalAccessTokenModel(AccessToken::class);

        Password::defaults(function () {
            $rule = Password::min(8);
            return app()->isProduction()
                ? $rule->mixedCase()->uncompromised()
                : $rule;
        });

        Paginator::defaultView('pagination-custom');
        view()->composer('*', function ($view) {
            $url = request()->path();
            if ($url == 'account/profile') {
                $data['_title'] = 'My Profile';
                $data['m_id'] = -1;
                $data['m_icon'] = 'fa fa-user-circle';
            } else {
                $menu = Menu::firstWhere(['url' => $url]);
                if ($menu) {
                    $data['m_id'] = $menu->id;
                    $data['m_icon'] = $menu->icon;
                    if ($menu->sub == 1) {
                        $data['m_is_sub'] = ($menu->sub == 1);
                        $data['m_name'] = Menu::firstWhere('id', $menu->parent_id)->name ?? '';
                    }
                }
                //dd('xx');
                $data['_title'] = $menu->name ?? config('app.name', 'Laravel');
            }
            $view->with($data);
        });
    }
}
