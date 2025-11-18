<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreLedgerRequest;
use App\Http\Requests\Admin\UpdateLedgerRequest;
use App\Models\Ledger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LedgerController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:ledger.view,ledger.manage')->only('index');
        $this->middleware('permission:ledger.manage')->only(['store', 'update', 'destroy']);
    }

    public function index(Request $request): View
    {
        $filters = [
            'search' => $request->string('search')->trim()->value(),
            'network' => $request->string('network')->trim()->value(),
            'currency' => $request->string('currency')->trim()->value(),
            'status' => $request->string('status')->trim()->value(),
        ];

        $ledgers = Ledger::query()
            ->when($filters['search'] !== null && $filters['search'] !== '', function ($query) use ($filters) {
                $search = $filters['search'];

                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery
                        ->where('wallet', 'like', '%' . $search . '%')
                        ->orWhere('network', 'like', '%' . $search . '%')
                        ->orWhere('currency', 'like', '%' . $search . '%');
                });
            })
            ->when($filters['network'], fn ($query, $network) => $query->where('network', $network))
            ->when($filters['currency'], fn ($query, $currency) => $query->where('currency', $currency))
            ->when($filters['status'] === Ledger::STATUS_ACTIVE, fn ($query) => $query->where('status', Ledger::STATUS_ACTIVE))
            ->when($filters['status'] === Ledger::STATUS_INACTIVE, fn ($query) => $query->where('status', Ledger::STATUS_INACTIVE))
            ->orderBy('wallet')
            ->paginate(15)
            ->withQueryString();

        $networks = Ledger::query()
            ->select('network')
            ->whereNotNull('network')
            ->where('network', '<>', '')
            ->distinct()
            ->orderBy('network')
            ->pluck('network');

        $currencies = Ledger::query()
            ->select('currency')
            ->whereNotNull('currency')
            ->where('currency', '<>', '')
            ->distinct()
            ->orderBy('currency')
            ->pluck('currency');

        $canManageLedger = $request->user()?->hasAnyPermission(['ledger.manage']);

        return view('admin.ledger.index', [
            'ledgers' => $ledgers,
            'networks' => $networks,
            'currencies' => $currencies,
            'filters' => $filters,
            'statuses' => Ledger::statuses(),
            'canManageLedger' => $canManageLedger,
        ]);
    }

    public function store(StoreLedgerRequest $request): RedirectResponse
    {
        Ledger::create($request->validated());

        return redirect()
            ->route('admin.ledger.index')
            ->with('status', __('ledger.flash_created'));
    }

    public function update(UpdateLedgerRequest $request, Ledger $ledger): RedirectResponse
    {
        $ledger->update($request->validated());

        return redirect()
            ->route('admin.ledger.index')
            ->with('status', __('ledger.flash_updated'));
    }

    public function destroy(Ledger $ledger): RedirectResponse
    {
        $ledger->delete();

        return redirect()
            ->route('admin.ledger.index')
            ->with('status', __('ledger.flash_deleted'));
    }
}

