<?php

namespace App\Http\Controllers;

use App\Models\RoleRequest;
use Illuminate\Http\Request;

class RoleRequestController extends Controller
{
    // Méthode pour soumettre une demande
    public function submitRequest(Request $request)
    {
        $roleRequest = new RoleRequest();
        $roleRequest->user_id = auth()->id();
        $roleRequest->type = $request->input('role_name'); // 'agent' ou 'distributor'
        $roleRequest->agency_name = $request->input('agency_name');
        $roleRequest->other_agency_name = $request->input('other_agency_name');
        $roleRequest->iata_office_id = $request->input('iata_office_id');
        $roleRequest->status = 'pending';
        $roleRequest->save();

        return redirect()->back()->with('status', 'Votre demande a été soumise et est en attente de validation.');
    }

    // Méthode pour que l'admin approuve une demande
    public function approveRequest($id)
    {
        $roleRequest = RoleRequest::find($id);
        $roleRequest->status = 'approved';
        $roleRequest->save();

        return redirect()->back()->with('status', 'La demande a été approuvée.');
    }

    // Méthode pour que l'admin rejette une demande
    public function rejectRequest($id)
    {
        $roleRequest = RoleRequest::find($id);
        $roleRequest->status = 'rejected';
        $roleRequest->save();

        return redirect()->back()->with('status', 'La demande a été rejetée.');
    }
}
