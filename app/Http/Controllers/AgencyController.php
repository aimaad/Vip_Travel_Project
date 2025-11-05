<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use Illuminate\Http\Request;

class AgencyController extends Controller
{
    public function index()
    {
        $agencies = Agency::all();
        return view('admin.agencies.index', compact('agencies'));
    }

    // Removed the create method as it is no longer needed for inline addition
    // public function create()
    // {
    //     return view('admin.agencies.create');
    // }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:agencies,name',
        ]);

        Agency::create(['name' => $request->name]);

        return redirect()->route('admin.agencies.index')->with('success', 'Agency created successfully.');
    }

    public function update(Request $request, Agency $agency)
    {
        // Validation for inline editing
        $request->validate([
            'name' => 'required|string|max:255|unique:agencies,name,' . $agency->id,
        ]);

        $agency->update(['name' => $request->name]);

        return redirect()->route('admin.agencies.index')->with('success', __('Agency updated successfully.'));
    }

    public function destroy(Agency $agency)
    {
        $agency->delete();

        return redirect()->route('admin.agencies.index')->with('success', __('Agency deleted successfully.'));
    }
}
