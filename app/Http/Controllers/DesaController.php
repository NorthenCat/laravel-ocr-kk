<?php

namespace App\Http\Controllers;

use App\Models\Desa;
use App\Models\DesaUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DesaController extends Controller
{
    public function show($id)
    {
        $desa = Desa::findOrFail($id);
        $desa->load([
            'getRw.getKK.getWarga',
            'getRw' => function ($query) {
                $query->orderBy('nama_rw', 'asc');
            },
            'getUsers' => function ($query) {
                $query->orderBy('name', 'asc');
            }
        ]);
        $desa->loadCount(['getRw', 'getKK']);

        return view('desa.index', compact('desa'));
    }

    public function create()
    {
        return view('desa.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_desa' => 'required|string|max:255',
            'google_drive' => 'nullable|url|max:500',
        ]);

        $desa = Desa::create([
            'uuid' => Str::uuid(),
            'nama_desa' => $request->nama_desa,
            'google_drive' => $request->google_drive,
        ]);

        DesaUser::create([
            'desa_id' => $desa->id,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('desa.show', $desa->id)->with('success', 'Desa created successfully.');
    }

    public function edit($id)
    {
        $desa = Desa::findOrFail($id);
        return view('desa.form', compact('desa'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_desa' => 'required|string|max:255',
            'google_drive' => 'nullable|url|max:500',
        ]);

        $desa = Desa::findOrFail($id);
        $desa->update([
            'nama_desa' => $request->nama_desa,
            'google_drive' => $request->google_drive,
        ]);

        return redirect()->route('desa.show', $desa->id)->with('success', 'Desa updated successfully.');
    }

    public function destroy($id)
    {
        $desa = Desa::findOrFail($id);
        $desa->delete();

        return redirect()->route('desa.show')->with('success', 'Desa deleted successfully.');
    }

    public function addUser(Request $request, $id)
    {
        $request->validate([
            'user_email' => 'required|email',
        ], [
            'user_email.required' => 'Email is required.',
            'user_email.email' => 'Please enter a valid email address.',
        ]);


        $desa = Desa::findOrFail($id);

        // Check if user exists
        $user = User::where('email', $request->user_email)->first();
        if (!$user) {
            return back()->withInput()->with('error', 'User with this email does not exist in the system.');
        }

        // Check if user already has access
        if ($desa->hasAccess($user->id)) {
            return back()->withInput()->with('error', 'User already has access to this village.');
        }

        try {
            DesaUser::create([
                'desa_id' => $desa->id,
                'user_id' => $user->id,
            ]);

            return back()->with('success', 'User added successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to add user. Please try again.');
        }
    }

    public function removeUser(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $desa = Desa::findOrFail($id);

        // Prevent removing the current user
        if ($request->user_id == auth()->id()) {
            return back()->with('error', 'You cannot remove yourself from the village.');
        }

        DesaUser::where('desa_id', $desa->id)
            ->where('user_id', $request->user_id)
            ->delete();

        return back()->with('success', 'User removed successfully.');
    }
}
