<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // Refresh data dari database
        $user = DB::table('users')->where('id', $user->id)->first();
        return view('profile.index', compact('user'));
    }
    
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string', 
                'email', 
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
        ]);
        
        // Gunakan DB facade
        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'name' => $request->name,
                'email' => $request->email,
                'updated_at' => now()
            ]);
        
        return back()->with('success', 'Profil berhasil diupdate');
    }
    
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ]);
        
        $user = Auth::user();
        
        // Ambil password dari database langsung
        $currentPassword = DB::table('users')->where('id', $user->id)->value('password');
        
        if (!Hash::check($request->current_password, $currentPassword)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah']);
        }
        
        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'password' => Hash::make($request->password),
                'updated_at' => now()
            ]);
        
        return back()->with('success', 'Password berhasil diubah');
    }
}