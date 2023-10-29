<?php

namespace App\Http\Controllers\Post;

use App\Http\Requests\Post\StoreRequest;
use App\Http\Resources\Post\PostResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\File;
use Str;
use Intervention\Image\ImageManagerStatic as Image;


class StoreController extends BaseController
{
    public function __invoke(StoreRequest $request)
    {
        return $this->service->createPostWithFiles($request);
    }
}
