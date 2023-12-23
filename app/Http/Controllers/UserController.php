<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($username)
    {
        $currentUser = Auth::user();
        $user = User::where('username', $username)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Periksa apakah pengguna yang sedang login memiliki username yang sama dengan username yang diminta
        if ($currentUser && $currentUser->username === $user->username) {
            return new UserResource($user->loadMissing('detailUser'), true, 'Show User Data Successfully');
        }

        // Jika pengguna yang sedang login tidak memiliki hak akses untuk melihat profil
        return response()->json(['message' => 'Unauthorized'], 403);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'username' => 'max:20|unique:users,username,' . $id,
            'firstname' => 'max:255',
            'lastname' => 'max:255',
            'birthdate' => 'date',
            'country' => 'max:255',
            'city' => 'max:255',
            'biography' => 'max:65535',
            'gender' => 'in:male,female',
            'file' => 'image',

            // social media link
            'type' => 'in:youtube,instagram',
            'link' => 'url',
        ]);

        return response()->json($request->file);
        $user = User::findOrFail($id);

        // Simpan perubahan pada tabel users
        $userToUpdate = $request->only(['username']);
        if (!empty($userToUpdate)) {
            $user->update($request->only(['username']));
        }

        // $newName = NULL;
        // if($request->file){
        //     $randomName = Str::random(30);
        //     $extension = $request->file->getClientOriginalExtension();
        //     $newName = $randomName . '.' . $extension;

        //     Storage::putFileAs('images/user/avatar', $request->file, $newName);
        // }

        $dataToUpdate = $request->only(['firstname', 'lastname', 'birthdate', 'country', 'city', 'biography', 'gender']);

        if (!empty($dataToUpdate)) {
            $user->detailUser()->update($dataToUpdate);
            return new UserResource($user->loadMissing(['detailUser']), true, 'User updated successfully!');
        }

        if ($request->type && $request->link) {
            $detailUser = $user->detailUser;
            $socialMediaLinks = json_decode($detailUser->social_media_links, true);
            
            // Pastikan socialMediaLinks adalah array
            if (!is_array($socialMediaLinks)) {
                $socialMediaLinks = [];
            }
            
            $socialMediaLinks[$validated['type']] = $validated['link'];

            // Simpan kembali sebagai JSON ke basis data
            $result = $detailUser->social_media_links = json_encode($socialMediaLinks);
            $detailUser->save();            
        }

        // cek apakah ada data user yang di update atau tidak
        if (!empty($userToUpdate) || !empty($dataToUpdate)) {
            return new UserResource($user->loadMissing(['detailUser']), true, 'User Upadated Data Succesfully');
        }

        return new UserResource($user->loadMissing(['detailUser']), true, 'No User Upadated Data');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        
        return new UserResource($user->loadMissing(['detailUser']), true, 'User destroy akun successfully');
    }
}
