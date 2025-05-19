<?php

namespace App\Http\Controllers\Subscription;

use App\Models\Plan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::all();
        return view("subscription.plans", compact("plans"));
    }
}
