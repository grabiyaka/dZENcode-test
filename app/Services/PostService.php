<?php
namespace App\Services;

use App\Models\Post;
use App\Models\User;
use App\Models\File;

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
    public function userPosts($id)
    {
        $user = User::find($id);
        $posts = $user->posts;
        $i = 0;
        $arr = [];
        foreach ($posts as $post) {
            $arr[$i]['id'] = $post->id;
            $arr[$i]['client_name'] = $post->client_name;
            $i++;
        }
        ;
        return $arr;
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

    public function buildPostsTree($page, $sortField, $sortDirection)
    {
        $query = Post::where('parent_id', null);

        $query->join('users', 'posts.user_id', '=', 'users.id')
            ->select('posts.*', 'users.email', 'users.avatar')
            ->orderBy($sortField === 'email' ? 'users.email' : 'posts.' . $sortField, $sortDirection);

        $posts = $query->paginate(25, ['*'], 'page', $page);

        foreach ($posts as $post) {
            $this->buildSubTree($post);
        }

        return $posts;
    }

    protected function buildSubTree(Post $post)
    {
        $subposts = Post::where('parent_id', $post->id)->join('users', 'posts.user_id', '=', 'users.id')
            ->select('posts.*', 'users.email', 'users.avatar')->take(2)->get();
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
        $posts = Post::where('parent_id', $post->id)->get();

        foreach ($posts as $post) {
            $this->buildSubTree($post);
        }

        return $posts;
    }



}