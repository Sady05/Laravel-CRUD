<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    // Show all users
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    // Show form for creating a new user
    public function create()
    {
        return view('users.create');
    }

    // Store new user
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $profile_image = null;
        if ($request->file('profile_image')) {
            $profile_image = $request->file('profile_image')->store('profile_images', 'public');
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'profile_image' => $profile_image,
        ]);

        return response()->json(['success' => 'User created successfully.']);
    }

    // Show form to edit the user
    public function edit($id)
    {
        $user = User::find($id);
        return compact('user');
        // return view('users.edit', compact('user'));
    }

    // Update the user
    public function update(Request $request)
    {
         
        $user = User::find($request->id);

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->file('profile_image')) {
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }
            $user->profile_image = $request->file('profile_image')->store('profile_images', 'public');
        }

        $user->update($request->all());

        return response()->json(['success' => 'User updated successfully.']);
    }

    // Delete the user
    public function destroy($id)
    {
        $user = User::find($id);

        if ($user->profile_image) {
            Storage::disk('public')->delete($user->profile_image);
        }

        $user->delete();

        return response()->json(['success' => 'User deleted successfully.']);
    }
}
