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
        $filesResult = [];
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        $files = $request->file('files');
        unset($data['files']);
        $post = $this->service->store($data);
        $postResource = new PostResource($post);

        if ($files) {
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'txt'];
        
            foreach ($files as $file) {
                $extension = $file->getClientOriginalExtension();
                
                // Проверка, что расширение файла находится в списке разрешенных
                if (in_array($extension, $allowedExtensions)) {
                    $fileName = $file->getClientOriginalName();
                    $uniqueFileName = Str::random(40) . '.' . $extension;
                    $path = $file->storeAs('uploads', $uniqueFileName);
                    $path = $file->store('uploads', 'public');
        
                    $fileOptions = [
                        'name' => $fileName,
                        'path' => $path,
                        'post_id' => $postResource['id']
                    ];
        
                    $filesResult[] = File::create($fileOptions);
                    if ($this->isImage($file)) {
                        try {
                            $image = Image::make(public_path('storage/' . $path));
                            $image->resize(320, 240, function ($constraint) {
                                $constraint->aspectRatio();
                            });
                            $image->save();
                        } catch (\Exception $e) {
                            // Обработка ошибки при работе с изображением
                        }
                    }
                }
            }
        }
        

        $postData = $postResource->resource->toArray();
        $files = [
            'files' => $filesResult
        ];
        $postData['user_name'] = Auth::user()->name;
        $postData['avatar'] = Auth::user()->avatar;
        return array_merge($postData, $files);
    }
    private function isImage($file)
    {
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/bmp'];
        return in_array($file->getMimeType(), $allowedMimeTypes);
    }
}
