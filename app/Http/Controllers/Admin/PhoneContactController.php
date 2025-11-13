<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePhoneContactRequest;
use App\Http\Requests\Admin\UpdatePhoneContactRequest;
use App\Models\PhoneContact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PhoneContactController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:user-phones.view,user-phones.manage')->only('index');
        $this->middleware('permission:user-phones.create,user-phones.manage')->only('store');
        $this->middleware('permission:user-phones.edit,user-phones.manage')->only('update');
        $this->middleware('permission:user-phones.delete,user-phones.manage')->only('destroy');
    }

    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->value();
        $operator = $request->string('operator')->trim()->value();

        $phoneContacts = PhoneContact::query()
            ->when($search !== null && $search !== '', function ($query) use ($search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery
                        ->where('name', 'like', '%' . $search . '%')
                        ->orWhere('phone', 'like', '%' . $search . '%')
                        ->orWhere('comment', 'like', '%' . $search . '%');
                });
            })
            ->when($operator !== null && $operator !== '', function ($query) use ($operator) {
                $query->where('operator', $operator);
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $operators = PhoneContact::query()
            ->select('operator')
            ->whereNotNull('operator')
            ->where('operator', '<>', '')
            ->distinct()
            ->orderBy('operator')
            ->pluck('operator');

        return view('admin.users.phones.index', [
            'phoneContacts' => $phoneContacts,
            'operators' => $operators,
            'filters' => [
                'search' => $search,
                'operator' => $operator,
            ],
        ]);
    }

    public function store(StorePhoneContactRequest $request): RedirectResponse
    {
        PhoneContact::create($request->validated());

        return redirect()
            ->route('admin.users.phones.index')
            ->with('status', __('users.phone_directory_created'));
    }

    public function update(UpdatePhoneContactRequest $request, PhoneContact $phoneContact): RedirectResponse
    {
        $phoneContact->update($request->validated());

        return redirect()
            ->route('admin.users.phones.index')
            ->with('status', __('users.phone_directory_updated'));
    }

    public function destroy(PhoneContact $phoneContact): RedirectResponse
    {
        $phoneContact->delete();

        return redirect()
            ->route('admin.users.phones.index')
            ->with('status', __('users.phone_directory_deleted'));
    }
}


