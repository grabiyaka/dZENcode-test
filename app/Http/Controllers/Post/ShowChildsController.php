<?php

namespace App\Http\Controllers\Post;
use App\Http\Controllers\Controller;
use App\Http\Resources\Post\PostResource;
use App\Models\Post;

class ShowChildsController extends BaseController
{
    public function __invoke(Post $post)
    {   
        $posts = $this->service->getChilds($post);
        return $posts;
    }
}
