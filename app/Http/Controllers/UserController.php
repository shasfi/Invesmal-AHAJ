<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;

class UserController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', User::class);

        $users = User::latest()->paginate(20);
        return view('users.index', compact('users'));
    }

    public function show(User $user)
    {
        return view('users.profile', compact('user'));
    }

    public function profile(User $user)
    {
        $this->authorize('view', $user);

        $user->load('startups');

        return view('users.profile', compact('user'));
    }

    public function create()
    {
        return view('users.form', ['user' => null]);
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }
        User::create($data);
        return redirect()->route('users.index')->with('success', 'User created.');
    }

    public function edit(User $user)
    {
        $this->authorizeEdit($user);
        return view('users.form', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorizeEdit($user);
        $data = $request->validated();
        if ($request->filled('password')) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        if ($request->hasFile('avatar')) {
            if ($user->avatar) { Storage::disk('public')->delete($user->avatar); }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }
        $user->update($data);
        return redirect()->route('users.profile', $user)->with('success', 'Profile updated.');
    }

    public function destroy(User $user)
    {
        $this->authorizeEdit($user);
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted.');
    }

    protected function authorizeEdit(User $user): void
    {
        if (auth()->id() !== $user->id && auth()->user()?->role !== 'admin') {
            abort(403, 'You can only edit your own profile.');
        }
    }

    public function showInvestors()
    {
        $user = auth()->user();
        
        if ($user->role !== User::ROLE_STUDENT_FOUNDER) {
            abort(403, 'Only student founders can view investors.');
        }

        $startups = $user->startups;
        $investors = collect();

        foreach ($startups as $startup) {
            foreach ($startup->investments()->approved()->with('investor')->get() as $investment) {
                if (!$investors->has($investment->investor_id)) {
                    $investors->put($investment->investor_id, $investment->investor);
                }
            }
        }

        return view('users.investors', compact('investors', 'startups'));
    }

    public function showFounders()
    {
        $user = auth()->user();
        
        if ($user->role !== User::ROLE_INVESTOR) {
            abort(403, 'Only investors can view founders.');
        }

        $investments = $user->investments()->approved()->with('startup.founder')->get();
        $founders = collect();

        foreach ($investments as $investment) {
            if ($investment->startup && $investment->startup->founder) {
                $founders->put($investment->startup->founder->id, $investment->startup->founder);
            }
        }

        return view('users.founders', compact('founders', 'investments'));
    }
}
