<?php

namespace App\Http\Controllers\File;
use App\Http\Controllers\Controller;
use App\Models\File;

class DeleteController extends Controller
{
    public function __invoke(File $file)
    {   
        $file->delete();
        return true;
    }
}
