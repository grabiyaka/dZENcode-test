<?php

namespace App\Http\Controllers\Post;
use App\Http\Requests\Post\StoreRequest;
use App\Http\Resources\Post\PostResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoreController extends BaseController
{
    public function __invoke(StoreRequest $request)
    {    
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        $data['user_name'] = Auth::user()->name;
        $post = $this->service->store($data);
        return new PostResource($post); 
    }
}
