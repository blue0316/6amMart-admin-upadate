<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use App\Models\Module;

class CurrentModule
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (request()->get('module_id')) {
            session()->put('current_module',request()->get('module_id'));
            Config::set('module.current_module_id', request()->get('module_id'));
        }else{
            Config::set('module.current_module_id', session()->get('current_module'));
        }

        $module_id = Config::get('module.current_module_id');
        $module_id = is_array($module_id)?null:$module_id;
        $module = isset($module_id)?Module::find($module_id):Module::find(1);

        if ($module) {
            Config::set('module.current_module_id', $module->id);
            Config::set('module.current_module_type', $module->module_type);
        }
        if (Request::is('admin/users*')) {
            Config::set('module.current_module_id', null);
            Config::set('module.current_module_type', 'users');
        }
        if (Request::is('admin/transactions*')) {
            Config::set('module.current_module_id', null);
            Config::set('module.current_module_type', 'transactions');
        }
        if (Request::is('admin/dispatch*')) {
            Config::set('module.current_module_id', null);
            Config::set('module.current_module_type', 'dispatch');
        }
        if (Request::is('admin/business-settings/*')) {
            Config::set('module.current_module_id', null);
            Config::set('module.current_module_type', 'settings');
        }
        return $next($request);
    }
}
