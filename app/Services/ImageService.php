<?php
 namespace App\Services;
 use App\Models\Post;
 use App\Models\Image;
 use Carbon\Carbon;
 use Illuminate\Support\Facades\Storage;

class ImageService
{
    public function getImages($id){
        $post = Post::find($id);
        $images = $post->images; 
        return $images; 
    }

    public function createImage($data){
        if(array_key_exists('front', $data)){
            $image = $data['front'];
        } else if(array_key_exists('back', $data)){
            $image = $data['back'];
        }else if(array_key_exists('plantogram', $data)){
            $image = $data['plantogram'];
        }else if(array_key_exists('ankles', $data)){
            $image = $data['ankles'];
        }else if(array_key_exists('adams', $data)){
            $image = $data['adams'];
        }else if(array_key_exists('side_tilt', $data)){
            $image = $data['side_tilt'];
        }else if(array_key_exists('left_hand', $data)){
            $image = $data['left_hand'];
        }else if(array_key_exists('right_hand', $data)){
            $image = $data['right_hand'];
        }else if(array_key_exists('side', $data)){
            $image = $data['side'];
        }
        $post_id = $data['post_id'];
        $item = $data['item'];
        $name = md5(Carbon::now() . '_' . $image->getClientOriginalName()) . '.' . $image->getClientOriginalExtension();
        $filePath = Storage::disk('public_uploads')->putFileAs('/images', $image, $name);
        $filePath = explode('/', $filePath);
        Image::create([
            'url' => $filePath[1],
            'post_id' => $post_id,
            'item' => $item
        ]);
    }
    
    
}