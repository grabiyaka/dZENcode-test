<?php

namespace App\Http\Controllers\Avatar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User; // Импортируйте модель User
use Illuminate\Support\Facades\Storage;
use Str;

class StoreController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'user_id' => 'required|exists:users,id',
        ]);
    
        $file = $request->file('avatar');
        $extension = $file->getClientOriginalExtension();
        $uniqueFileName = Str::random(40) . '.' . $extension;
        $avatarPath = $file->storeAs('avatars', $uniqueFileName, 'public');
    
        $user = User::find($request->user_id);
    
        // Проверяем, есть ли у пользователя предыдущий аватар
        if ($user->avatar) {
            // Удаляем предыдущий аватар
            Storage::disk('public')->delete($user->avatar);
        }
    
        $user->avatar = $avatarPath;
        $user->save();
    
        return response()->json(['message' => 'Avatar uploaded successfully']);
    }
}
