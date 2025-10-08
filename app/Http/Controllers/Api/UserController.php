<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    use ApiResponseTrait;

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => ['sometimes', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['sometimes', 'string', 'max:20', Rule::unique('users')->ignore($user->id)],
            'address' => 'sometimes|string|max:500',
            'date_of_birth' => 'sometimes|date',
            'gender' => 'sometimes|in:male,female,other',
            'emergency_contact' => 'sometimes|string|max:20',
            'profile_image' => 'sometimes|string|max:255', // URL to uploaded image
        ]);

        $user->update($data);

        return $this->successResponse(
            new UserResource($user->fresh()),
            'Profile updated successfully'
        );
    }

    public function changePassword(Request $request)
    {
        $user = Auth::user();
        
        $data = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        // Verify current password
        if (!Hash::check($data['current_password'], $user->password)) {
            return $this->errorResponse('Current password is incorrect', 422);
        }

        // Update password
        $user->update([
            'password' => Hash::make($data['new_password'])
        ]);

        return $this->successResponse(
            null,
            'Password changed successfully'
        );
    }

    public function uploadProfileImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            
            // Store in public/uploads/profiles directory
            $path = $image->storeAs('uploads/profiles', $imageName, 'public');
            
            // Update user profile image URL
            $user = Auth::user();
            $user->update([
                'profile_image' => asset('storage/' . $path)
            ]);

            return $this->successResponse([
                'image_url' => asset('storage/' . $path),
                'user' => new UserResource($user->fresh())
            ], 'Profile image uploaded successfully');
        }

        return $this->errorResponse('No image file provided', 422);
    }
}