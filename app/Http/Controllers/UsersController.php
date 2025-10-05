<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class UsersController extends Controller
{
    public function index() {
        $users = User::all();

        return view('users.index', compact('users'));
    }

    public function create(Request $request) {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request) {
        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');
        $confirm_password = $request->input('confirm-password');
        $role = $request->input('role');
        $user_id = Str::uuid()->toString();

        if($password != $confirm_password) {
            return redirect()->back()->with('error', "Passwords do not match");
        }

        try {
            $user = new User();

            $user->name = $name;
            $user->email = $email;
            $user->password = bcrypt($password);
            $user->role_id = $role;
            $user->user_id = $user_id;
            $user->avatar = "";

            $uploadFolder = 'users';
            if($request->file('avatar')) {
                $image = $request->file('avatar');
                $image_uploaded_path = $image->store($uploadFolder, 'public');
                $user->avatar = $image_uploaded_path;
            }

            $user->save();


            return redirect()->route('users.index')->with('success', 'User Created!');
        } catch (Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function edit($id) {
        $user_id = $id;

        $user = User::where('user_id', $user_id)->first();
        $roles = Role::all();

        return view('users.edit', compact("user", "roles"));
    }

    public function update(Request $request) {
        $user_id = $request->input('user_id');
        $name = $request->input('name');
        $email = $request->input('email');
        $role = $request->input('role');
        $password = bcrypt($request->input('password'));


        try {
            $uploadFolder = 'users';
            $image_uploaded_path = "";
            if($request->file('avatar')) {
                $image = $request->file('avatar');
                $image_uploaded_path = $image->store($uploadFolder, 'public');
            }
            DB::table("users")->where('user_id', $user_id)->update(
                [
                    'name' => $name,
                    'email' => $email,
                    'password' => $password,
                    'role_id' => $role,
                    'avatar' => $image_uploaded_path
                ]
            );


            return redirect()->route('users.index')->with('success', 'User updated!');
        } catch (Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function destroy(Request $request) {
        $user_id = $request->input('user_id');
        $user = User::where('user_id', $user_id)->first();

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully!');
    }
}
