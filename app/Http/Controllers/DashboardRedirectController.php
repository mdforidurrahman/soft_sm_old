<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardRedirectController extends Controller
{

    public function index()
    {

        $role = getUserRoleName();
        return redirect()->route($role.'dashboard');
    }
}
