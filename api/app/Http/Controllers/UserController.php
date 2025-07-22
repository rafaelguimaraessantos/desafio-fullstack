<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $user = User::find(1);
        $activeContract = $user->activeContract();
        
        return response()->json([
            'user' => $user,
            'active_contract' => $activeContract
        ]);
    }
}
