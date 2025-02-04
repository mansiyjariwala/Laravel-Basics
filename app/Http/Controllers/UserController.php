<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\DashboardResource;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return DashboardResource::collection($users);
    }
}
