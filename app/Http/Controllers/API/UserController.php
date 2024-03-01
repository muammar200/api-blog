<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\DetailUser;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function show(User $user)
    {
        return new UserResource(true, 'Show User Data Successfully', $user);
    }

    public function update(Request $request, User $user)
    {
        $user =  Auth::user();
        // return $user;
        $validated = $request->validate([
            'username' => 'max:20|unique:users,username,' . $user->id,
            'firstname' => 'max:255',
            'lastname' => 'max:255',
            'birthdate' => 'date',
            'country' => 'max:255',
            'city' => 'max:255',
            'biography' => 'max:65535',
            'gender' => 'in:male,female',
            'avatar' => 'image',

            // social media link
            'type' => 'in:youtube,instagram',
            'link' => 'url',
        ]);

        
        // Save update to the users table
        $userToUpdate = $request->only(['username']);
        if (!empty($userToUpdate)) {
            $user->update($request->only(['username']));
        }

        // $newName = NULL;
        if ($request->hasFile('avatar')) {
            // if (!empty($user->detailUser->avatar_url)) {
            //     $oldAvatar = $user->detailUser->avatar_url;
            //     $oldAvatarPath = 'images/user/avatar/' . $oldAvatar;
            //     Storage::delete($oldAvatarPath);
            // }

            // $randomName = Str::random(30);
            // $extension = $request->file('avatar')->getClientOriginalExtension();
            // $newName = $randomName . '.' . $extension;

            // Storage::putFileAs('images/user/avatar', $request->file('avatar'), $newName);

            // Upload to S3
            $file = request()->file('avatar');
            $path = $file->storePublicly('public/images/avatar');
            $filename = basename($path);

            DetailUser::where('user_id', $user->id)->update(['avatar' => $filename]);
            
            return response()->json([
                'message' => 'Avatar update success',
                'filename' => $filename,
                'path' => "https://inrblog.s3.amazonaws.com/$path"
            ]);
        }

        $detailUserToUpdate = $request->only(['firstname', 'lastname', 'birthdate', 'country', 'city', 'biography', 'gender']);

        // Save update to the detail_users table
        if (!empty($detailUserToUpdate)) {
            $user->detailUser()->update($detailUserToUpdate);
        }

        // Checks if a type and link are provided in the request.
        if ($request->type && $request->link) {
            // Retrieves user details and decodes social media links from JSON.
            $detailUser = $user->detailUser;
            $socialMediaLinks = json_decode($detailUser->social_media_links, true);

            // Updates the social media link for the provided type.
            $socialMediaLinks[$validated['type']] = $validated['link'];

            // Encodes the updated social media links array into JSON format and saves it to the 'social_media_links' field of $detailUser.
            $detailUser->social_media_links = json_encode($socialMediaLinks);
            $detailUser->save(); // Saves the updated social media links for the user.
        }

        // Check if there is any updated user data or not
        if (!empty($userToUpdate) || !empty($detailUserToUpdate) || !empty($request->type && $request->link) || $request->hasFile('avatar')) {
            $user->touch();
            $user = User::where('username', $user->username)->first();
            return new UserResource(true, 'User Upadated Data Succesfully', $user);
        }

        return new UserResource(true, 'No User Upadated Data', $user);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return new UserResource(true, 'User destroy akun successfully', $user);
    }
}
