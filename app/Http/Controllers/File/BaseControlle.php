<?php

namespace App\Http\Controllers\File;

use App\Http\Controllers\Controller;
use App\Services\FileService;

class BaseController extends Controller
{
    public $service;

    public function __construct(FileService $service)
    {
        $this->service = $service; 
    }

}