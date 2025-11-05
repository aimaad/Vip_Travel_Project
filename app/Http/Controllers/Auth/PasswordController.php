<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    public function create($token)
    {
        // Vérifiez si le token est valide
        $user = User::where('email_verification_token', $token)->firstOrFail();
        
        return view('auth.create-password', compact('user', 'token'));
    }

    public function createPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('email_verification_token', $request->token)->firstOrFail();
        
        // Mettre à jour le mot de passe
        $user->password = Hash::make($request->password);
        $user->email_verification_token = null; // Réinitialiser le token
        $user->save();

        return redirect()->route('login')->with('status', 'Mot de passe créé avec succès. Vous pouvez vous connecter.');
    }
}