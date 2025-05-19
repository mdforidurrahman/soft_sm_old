<?php

use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

if (!function_exists('defineRoleBasedRoutes')) {
    function defineRoleBasedRoutes(Closure $routeDefinitions) {
        if (Schema::hasTable('roles')) {
            $roles = Role::all()->pluck('name')->toArray();
        } else {
            $roles = ['admin', 'user', 'staff', 'manager'];
        }

        if (Auth::check()) {
            $user = Auth::user();
            $role = $user->roles->first()->name;
            Route::group([
                'prefix' => $role,
//                'middleware' => ['role:' . $role],
                'as' => $role . '.',
            ], function () use ($routeDefinitions, $role) {
                $routeDefinitions($role);
            });
        } else {
            foreach ($roles as $role) {
                Route::group([
                    'prefix' => $role,
                    'middleware' => ['role:' . $role],
                    'as' => $role . '.',
                ], function () use ($routeDefinitions, $role) {
                    $routeDefinitions($role);
                });
            }
        }
    }

    function getUserRoleName() {
        if (Auth::check()) {
            $user = Auth::user();
            $role = $user->roles->first();
            return $role->name . '.';
        }
    }
}
