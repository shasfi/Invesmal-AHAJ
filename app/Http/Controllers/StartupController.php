<?php

namespace App\Http\Controllers;

use App\Events\StartupCreated;
use App\Models\Startup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreStartupRequest;
use App\Http\Requests\UpdateStartupRequest;
use App\Services\PitchDeckService;

class StartupController extends Controller
{
    protected function filteredStartupsQuery(Request $request)
    {
        return Startup::with('founder')
            ->search($request->input('search'))
            ->filterByStage($request->input('stage'))
            ->filterByIndustry($request->input('industry'));
    }

    public function landing(Request $request)
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        $stats = [
            'total_startups' => Startup::count(),
            'funded_startups' => Startup::where('amount_raised', '>', 0)->count(),
            'total_raised' => Startup::sum('amount_raised') ?: 0,
            'active_investors' => \App\Models\Investment::distinct('investor_id')->count('investor_id'),
        ];

        $featured = Startup::with('founder')->where('is_verified', true)->whereNotNull('funding_goal')->orderByDesc('amount_raised')->first();
        $trending = Startup::with('founder')->trending()->limit(6)->get();
        $recentlyFunded = Startup::with('founder')->recentlyFunded()->limit(3)->get();

        $search = $request->input('search');
        $stage = $request->input('stage');
        $industry = $request->input('industry');

        $startupsByIndustry = Startup::groupByIndustry(
            $this->filteredStartupsQuery($request)->orderBy('name')->get()
        );

        $industries = collect(Startup::INDUSTRY_CATEGORIES)
            ->merge(Startup::select('industry')->whereNotNull('industry')->distinct()->pluck('industry'))
            ->unique()
            ->sort()
            ->values();

        return view('startups.landing', compact('stats', 'featured', 'trending', 'recentlyFunded', 'startupsByIndustry', 'industries', 'search', 'stage', 'industry'));
    }

    /**
     * Public discover page — all startups (browse).
     */
    public function discover(Request $request)
    {
        $search = $request->input('search');
        $stage = $request->input('stage');
        $industry = $request->input('industry');

        $startupsByIndustry = Startup::groupByIndustry(
            $this->filteredStartupsQuery($request)->orderBy('name')->get()
        );

        $totalCount = collect($startupsByIndustry)->sum(fn ($items) => $items->count());

        $industries = collect(Startup::INDUSTRY_CATEGORIES)
            ->merge(Startup::select('industry')->whereNotNull('industry')->distinct()->pluck('industry'))
            ->unique()
            ->sort()
            ->values();

        $pageTitle = 'Discover Startups';
        $pageSubtitle = 'Browse startups from all founders on the platform';
        $listRoute = 'startups.discover';

        return view('startups.index', compact(
            'startupsByIndustry',
            'totalCount',
            'search',
            'stage',
            'industry',
            'industries',
            'pageTitle',
            'pageSubtitle',
            'listRoute'
        ));
    }

    /**
     * Authenticated list: founders see only their startups; admin sees all.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if (in_array($user->role, [User::ROLE_INVESTOR, User::ROLE_MENTOR], true)) {
            return redirect()->route('startups.discover', $request->only(['search', 'stage', 'industry']));
        }

        $search = $request->input('search');
        $stage = $request->input('stage');
        $industry = $request->input('industry');

        $query = $this->filteredStartupsQuery($request);

        if ($user->role === User::ROLE_STUDENT_FOUNDER) {
            $query->where('founder_id', $user->id);
        }

        $startupsByIndustry = Startup::groupByIndustry(
            $query->orderBy('name')->get()
        );

        $totalCount = collect($startupsByIndustry)->sum(fn ($items) => $items->count());

        $industries = collect(Startup::INDUSTRY_CATEGORIES)
            ->merge(
                Startup::query()
                    ->when($user->role === User::ROLE_STUDENT_FOUNDER, fn ($q) => $q->where('founder_id', $user->id))
                    ->whereNotNull('industry')
                    ->distinct()
                    ->pluck('industry')
            )
            ->unique()
            ->sort()
            ->values();

        $pageTitle = $user->role === User::ROLE_ADMIN ? 'All Startups' : 'My Startups';
        $pageSubtitle = $user->role === User::ROLE_ADMIN
            ? 'Manage every startup on the platform'
            : 'Only startups you created';
        $listRoute = 'startups.index';

        return view('startups.index', compact(
            'startupsByIndustry',
            'totalCount',
            'search',
            'stage',
            'industry',
            'industries',
            'pageTitle',
            'pageSubtitle',
            'listRoute'
        ));
    }

    public function search(Request $request)
    {
        $query = Startup::with('founder');
        if ($request->filled('q')) { $query->search($request->q); }
        if ($request->filled('stage')) { $query->filterByStage($request->stage); }
        if ($request->filled('industry')) { $query->filterByIndustry($request->industry); }
        $startups = $query->latest()->limit(8)->get();
        return response()->json(['startups' => $startups]);
    }

    public function show(Startup $startup)
    {
        $startup->load(['founder', 'pitchDeck', 'investments']);
        $relatedStartups = Startup::where('industry', $startup->industry)->where('id', '!=', $startup->id)->latest()->limit(3)->get();

        $pitchDeckData = null;
        if ($startup->pitch_deck_id) {
            $pitchDeckService = app(PitchDeckService::class);
            $pitchDeckData = $pitchDeckService->getPublicData($startup);
        }

        $myInvestment = null;
        $canInvest = false;

        if (auth()->check() && auth()->user()->role === \App\Models\User::ROLE_INVESTOR) {
            $canInvest = true;
            $myInvestment = \App\Models\Investment::where('investor_id', auth()->id())
                ->where('startup_id', $startup->id)
                ->first();
        }

        return view('startups.show', compact('startup', 'relatedStartups', 'pitchDeckData', 'canInvest', 'myInvestment'));
    }

    public function create()
    {
        $this->authorize('create', Startup::class);

        $founders = User::where('role', User::ROLE_STUDENT_FOUNDER)->get();
        return view('startups.form', ['startup' => null, 'founders' => $founders]);
    }

    public function store(StoreStartupRequest $request)
    {
        $data = $request->validated();

        // Auto-set founder_id for student founders
        if (auth()->user()->role === User::ROLE_STUDENT_FOUNDER) {
            $data['founder_id'] = auth()->id();
        }

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('startups', 'public');
        }

        $startup = Startup::create($data);

        // Dispatch event -> triggers activity log + in-app notification
        event(new StartupCreated($startup));

        return redirect()->route('startups.show', $startup)->with('success', 'Startup created successfully!');
    }

    public function edit(Startup $startup)
    {
        $this->authorize('update', $startup);

        $founders = User::where('role', User::ROLE_STUDENT_FOUNDER)->get();
        return view('startups.form', compact('startup', 'founders'));
    }

    public function update(UpdateStartupRequest $request, Startup $startup)
    {
        $data = $request->validated();
        if ($request->hasFile('logo')) {
            if ($startup->logo) { Storage::disk('public')->delete($startup->logo); }
            $data['logo'] = $request->file('logo')->store('startups', 'public');
        }
        $startup->update($data);
        return redirect()->route('startups.show', $startup)->with('success', 'Startup updated.');
    }

    public function destroy(Startup $startup)
    {
        $this->authorize('delete', $startup);

        $startup->delete();
        return redirect()->route('dashboard')->with('success', 'Startup deleted.');
    }
}