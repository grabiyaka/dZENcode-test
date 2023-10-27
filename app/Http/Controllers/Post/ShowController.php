<?php

namespace App\Http\Controllers\Post;
use App\Http\Controllers\Controller;
use App\Http\Resources\Post\PostResource;
use App\Models\Post;

class ShowController extends BaseController
{
    public function __invoke(Post $post)
    {   
        $page = request('page', 1);
        $sortField = request('sortField', 'created_at'); 
        $sortDirection = request('sortDirection', 'desc');
        $posts = $this->service->buildPostsTree($page, $sortField, $sortDirection);
        return $posts;
    }
}
