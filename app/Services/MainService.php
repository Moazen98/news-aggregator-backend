<?php

namespace App\Services;

/**
 * Class MainDashboardService.
 */
abstract class MainService
{
    protected $apiPaginate = 15;
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->apiPaginate = config('custom_settings.api_paginate');
    }
}
