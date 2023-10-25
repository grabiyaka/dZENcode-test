<?php

namespace App\Http\Controllers\Post;

use App\Http\Requests\Post\StoreRequest;
use App\Http\Resources\Post\PostResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\File;
use Str;


class StoreController extends BaseController
{
    public function __invoke(StoreRequest $request)
    {
        $filesResult = [];
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        $data['user_name'] = Auth::user()->name;
        $files = $request->file('files');
        unset($data['files']);
        $post = $this->service->store($data);
        $postResource = new PostResource($post);

        if ($files) {
            foreach ($files as $file) {
                $extension = $file->getClientOriginalExtension();
                $uniqueFileName = Str::random(40) . '.' . $extension;
                $path = $file->storeAs('uploads', $uniqueFileName);
                $path = $file->store('uploads', 'public');


                $fileOptions = [
                    'name' => $uniqueFileName,
                    'path' => $path,
                    'post_id' => $postResource['id']
                ];

                $filesResult[] = File::create($fileOptions);
            }
        }

        $postData = $postResource->resource->toArray();
        $files = [
            'files' => $filesResult
        ];
        return array_merge($postData, $files);
    }
    private function isImage($file)
    {
        return in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/gif', 'image/bmp']);
    }
}
