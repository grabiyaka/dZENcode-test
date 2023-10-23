<?php

namespace App\Http\Controllers\Post;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class DeleteController extends BaseController
{
    public function __invoke(Post $post)
    {  
        $this->service->deletePost($post);
        return response([]);
    }
}
