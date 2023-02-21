<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;

class DebugController extends Controller
{
    public function debug (Request $request) {
        $user = Auth::user();

        return [
            'user' => $user,
//            'model' => $userModel,
            // 'business' => $userModel->business
        ];
    }
}
