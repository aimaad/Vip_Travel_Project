<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Foundation\Auth\EmailVerificationRequest;



class SetPasswordController extends Controller
{
    // Affiche le formulaire de modification du mot de passe
    public function showSetPasswordForm(Request $request)
    {
        $id = $request->query('id');
        $token = $request->query('hash');
        $expires = (int) $request->query('expires');
        $signature = $request->query('signature');
        if (is_null($id) || is_null($token) || is_null($expires) || is_null($signature)) {
            abort(404); // Lancer une erreur 404 si un paramètre est manquant
        }

        // TODO: return 404 if missing params

        // Si tout est bon, afficher le formulaire
        return view('auth.setPassword')->with([
            'id' => $id,
            'hash' => $token,
            'expires' => $expires,
            'signature' => $signature, 
        ]);
    }

    // Sauvegarde du nouveau mot de passe
    public function savePassword (EmailVerificationRequest $request) 
    {
      
    
     $request->validate([
    'password' => [
        'required',
        'confirmed',
        'min:8',  // Minimum 8 caractères
        'regex:/[a-z]/',  // Doit contenir au moins une lettre minuscule
        'regex:/[A-Z]/',  // Doit contenir au moins une lettre majuscule
        'regex:/[0-9]/',  // Doit contenir au moins un chiffre
        'regex:/[@$!%*?&#]/',  // Doit contenir au moins un caractère spécial
    ],
    'password_confirmation' => 'required', 
    'term' => 'required',  // Valider les termes et conditions
], [
    'password.regex' => __('The password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.'),
    'password.confirmed' => __('The password confirmation does not match.'),
    'term.required' => __('You must accept the terms and conditions to proceed.'),
]);
$request->fulfill();
        // Mise à jour du mot de passe et suppression du token
        $request->user()->password = Hash::make($request->password);
        $request->user()->save();

        // Rediriger l'utilisateur vers la page d'accueil avec un message de succès
        return redirect()->route('home')->with('status', 'Your password has been successfully updated. Welcome back!');
    }
}