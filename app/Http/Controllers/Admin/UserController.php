<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\RespondsToStatusChange;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use RespondsToStatusChange;

    public function index(Request $request)
    {
        $search = $request->input('search');

        $users = User::when($search, function ($query) use ($search) {
            return $query->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        })->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'search'));
    }

    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function updateStatus(Request $request, User $user)
    {
        $request->validate(['active' => 'required|boolean']);

        $user->update(['active' => (bool) $request->active]);

        return $this->statusResponse(
            $request,
            $user->active ? 'Compte activé.' : 'Compte désactivé.',
            ['active' => $user->active]
        );
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur supprimé.');
    }
}
