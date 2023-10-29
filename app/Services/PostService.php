<?php
namespace App\Services;

use App\Models\Post;
use App\Models\User;
use App\Models\File;
use App\Http\Resources\Post\PostResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Str;
use Intervention\Image\ImageManagerStatic as Image;

class PostService
{
    public function store($data)
    {
        $post = Post::create($data);
        return $post;
    }
    public function update($data, Post $post)
    {
        $post->update($data);
    }
    public function getAllPosts()
    {
        $posts = Post::all();
        return $posts;
    }

    public function deletePost(Post $post)
    {
        $post->delete();
    }

    public function deletePostWithImages(Post $post)
    {
        $post->images()->delete();
        $post->delete();
    }

    public function createPostWithFiles($request){
        $filesResult = [];
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        $files = $request->file('files');
        unset($data['files']);
        $post = $this->store($data);
        $postResource = new PostResource($post);

        if ($files) {
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'txt'];
        
            foreach ($files as $file) {
                $extension = $file->getClientOriginalExtension();
                
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

    public function buildPostsTree($page, $sortField, $sortDirection)
    {
        $query = Post::where('parent_id', null);

        $query->join('users', 'posts.user_id', '=', 'users.id')
            ->selectRaw('posts.*, users.email, users.avatar, users.name AS user_name')
            ->orderBy($sortField === 'email' ? 'users.email' : ($sortField === 'user_name' ? 'user_name' : 'posts.' . $sortField), $sortDirection);

        $posts = $query->paginate(25, ['*'], 'page', $page);

        foreach ($posts as $post) {
            $this->buildSubTree($post);
        }

        return $posts;
    }


    protected function buildSubTree(Post $post)
    {
        $subposts = Post::where('parent_id', $post->id)->join('users', 'posts.user_id', '=', 'users.id')
            ->selectRaw('posts.*, users.email, users.avatar, users.name AS user_name')->take(2)->get();
        $morePosts = Post::where('parent_id', $post->id)->take(3)->count() > 2;

        $files = File::where('post_id', $post->id)->get();
        $post->setAttribute('files', $files);

        $post->setAttribute('more_posts', $morePosts);

        foreach ($subposts as $subpost) {

            $this->buildSubTree($subpost);
        }

        $post->setAttribute('posts', $subposts);
    }

    public function getChilds($post)
    {
        $posts = Post::where('parent_id', $post->id)->join('users', 'posts.user_id', '=', 'users.id')
            ->selectRaw('posts.*, users.email, users.avatar, users.name AS user_name')->get();

        foreach ($posts as $post) {
            $this->buildSubTree($post);
        }

        return $posts;
    }

    private function isImage($file)
    {
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/bmp'];
        return in_array($file->getMimeType(), $allowedMimeTypes);
    }

}