<?php

namespace App\Http\Controllers\Post;

class IndexController extends BaseController
{
    public function __invoke($id)
    {
        $arr = $this->service->userPosts($id);
        return $arr;
    }
}
