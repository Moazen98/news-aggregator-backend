<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

abstract class MainApiController extends Controller
{
    protected $apiPaginate = 15;

    public function __construct()
    {
        $apiPaginate = config('custom_settings.api_paginate');
    }
}
