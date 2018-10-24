<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class TestController extends Controller
{

    public function test()
    {
        $date = date('Y-m-d',intval(env('TEST')));

        echo $date;
    }
}