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

        $users = User::withCount('reports')
            ->when($search, function ($query) use ($search) {
                $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'search'));
    }

    public function show(User $user)
    {
        $user->loadCount('reports');

        return view('admin.users.show', compact('user'));
    }

    public function updateStatus(Request $request, User $user)
    {
        $request->validate(['active' => 'required|boolean']);

        if ($refusal = $this->protectedAccountReason($user)) {
            return $this->statusRefused($request, $refusal);
        }

        $user->update(['active' => (bool) $request->active]);

        logAdminAction($user->active ? 'Activate' : 'Deactivate', 'User', $user->id, "{$user->email}");

        return $this->statusResponse(
            $request,
            $user->active ? __('admin.flash_user_activated') : __('admin.flash_user_deactivated'),
            ['active' => $user->active]
        );
    }

    public function destroy(User $user)
    {
        if ($refusal = $this->protectedAccountReason($user)) {
            return back()->with('error', $refusal);
        }

        $email = $user->email;
        $reports = $user->reports()->count();

        // Their reports stay (reports.user_id is set to null): they are part of
        // the public record and other citizens may have confirmed them.
        $user->delete();

        logAdminAction('Delete', 'User', $user->id, "{$email} ({$reports} report(s) kept, now anonymous)");

        return redirect()->route('admin.users.index')->with('success', $reports > 0
            ? __('admin.flash_user_deleted_reports', ['count' => $reports])
            : __('admin.flash_user_deleted'));
    }

    /**
     * Admin accounts can't be deactivated or deleted from here: it would let an
     * admin lock themselves (or every other admin) out of the panel.
     */
    private function protectedAccountReason(User $user): ?string
    {
        if ($user->is(auth()->user())) {
            return __('admin.flash_user_self');
        }

        if ($user->is_admin) {
            return __('admin.flash_user_admin');
        }

        return null;
    }
}
