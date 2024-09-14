<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    // Display a listing of the users
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = User::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('profile_image', function($row) {
                    return $row->profile_image 
                        ? '<img src="'.Storage::url($row->profile_image).'" width="50" height="50" class="rounded-circle" />' 
                        : 'N/A';
                })
                ->addColumn('actions', function($row) {
                    $btn = '<button class="btn btn-warning btn-sm" onclick="editUser(' . $row->id . ')">Edit</button>';
                    $btn .= ' <button class="btn btn-danger btn-sm" onclick="deleteUser(' . $row->id . ')">Delete</button>';
                    return $btn;
                })
                ->rawColumns(['profile_image', 'actions'])
                ->make(true);
        }

        return view('users.index');
    }

    // Store a newly created user
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'profile_image' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
        ]);

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;


        if ($request->hasFile('profile_image')) {
            $user->profile_image = $request->file('profile_image')->store('profile_images', 'public');
        }
        
        $user->save();

        return response()->json(['success' => 'User created successfully.']);
    }

    // Show the form for editing the specified user
    public function edit($id)
    {
        $user = User::find($id);
        return response()->json(['user' => $user]);
    }

    // Update the specified user
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'profile_image' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
        ]);

        $user = User::find($id);
        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->hasFile('profile_image')) {
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }
            $user->profile_image = $request->file('profile_image')->store('profile_images', 'public');
        }

        $user->save();

        return response()->json(['success' => 'User updated successfully.']);
    }

    // Remove the specified user
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
