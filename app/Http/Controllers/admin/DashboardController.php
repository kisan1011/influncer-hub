<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        if(request()->ajax()){
            $influencer =  User::where('role_id',User::ROLE_INFLUENCER)->count();
            $business =  User::where('role_id',User::ROLE_BUSINESS)->count();
            $statisticArray = [
                [
                    'title' => 'Influencer',
                    'count' => $influencer,
                    'route' => route('influencer.index'),
                    'class' => "bg-primary",
                    'icon' => 'fas fa-user',
                ],
                [
                  'title' => 'Business',
                  'count' => $business,
                  'route' => route('business.index'),
                  'class' => "bg-info",
                  'icon' => 'fas fa-briefcase',
                ]
            ];
        return $statisticArray;
        }
        return view('admin.pages.dashboard.index');
    }
}
