<?php

namespace App\Http\Controllers\Avatar;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Post\BaseController;
use App\Http\Resources\Post\PostResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Импортируйте модель User
use Illuminate\Support\Facades\Storage;

class DeleteController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::find($request->user_id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if ($user->avatar) {
            // Получите путь к аватару
            $avatarPath = $user->avatar;

            // Удалите изображение из папки
            Storage::disk('public')->delete($avatarPath);

            // Обнулите поле avatar в базе данных
            $user->avatar = null;
            $user->save();

            return response()->json(['message' => 'Avatar and image deleted successfully']);
        }

        return response()->json(['message' => 'User does not have an avatar'], 404);
    }
}