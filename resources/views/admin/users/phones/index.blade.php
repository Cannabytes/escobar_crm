@extends('layouts.admin')

@section('title', __('users.phone_directory_title'))

@section('content')
  <div class="row">
    <div class="col-12">
      @php
        $authUser = auth()->user();
        $canCreatePhone = $authUser?->hasAnyPermission(['user-phones.create', 'user-phones.manage']) ?? false;
        $canEditPhone = $authUser?->hasAnyPermission(['user-phones.edit', 'user-phones.manage']) ?? false;
        $canDeletePhone = $authUser?->hasAnyPermission(['user-phones.delete', 'user-phones.manage']) ?? false;
      @endphp

      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
        <div>
          <h4 class="mb-1">{{ __('users.phone_directory_title') }}</h4>
          <p class="text-muted mb-0">{{ __('users.phone_directory_subtitle') }}</p>
        </div>
        @if($canCreatePhone)
          <div class="d-flex gap-2">
            <button
              type="button"
              class="btn btn-primary"
              data-bs-toggle="offcanvas"
              data-bs-target="#createPhoneCanvas"
              aria-controls="createPhoneCanvas">
              <i class="ti tabler-plus me-1"></i>{{ __('users.phone_directory_add') }}
            </button>
          </div>
        @endif
      </div>

      @if(session('status'))
        <div class="alert alert-success alert-dismissible mb-4" role="alert">
          <div class="d-flex align-items-center">
            <i class="ti ti-check text-success me-2"></i>
            <span>{{ session('status') }}</span>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Закрыть') }}"></button>
        </div>
      @endif
      @if($errors->any())
        <div class="alert alert-danger alert-dismissible mb-4" role="alert">
          <div class="d-flex align-items-start">
            <i class="ti tabler-alert-triangle me-2"></i>
            <div>
              <strong>{{ __('users.phone_directory_error_title') }}</strong>
              <ul class="mb-0 mt-2 ps-3">
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Закрыть') }}"></button>
        </div>
      @endif

      <div class="card mb-4">
        <div class="card-header">
          <form method="GET" action="{{ route('admin.users.phones.index') }}" class="row g-3">
            <div class="col-md-6 col-lg-5">
              <label for="search" class="form-label mb-1">{{ __('users.phone_directory_search_label') }}</label>
              <div class="input-group">
                <span class="input-group-text">
                  <i class="icon-base ti tabler-search"></i>
                </span>
                <input
                  type="search"
                  id="search"
                  name="search"
                  value="{{ $filters['search'] }}"
                  class="form-control"
                  placeholder="{{ __('users.phone_directory_search_placeholder') }}">
              </div>
            </div>
            <div class="col-md-4 col-lg-3">
              <label for="operator" class="form-label mb-1">{{ __('users.phone_directory_operator_filter') }}</label>
              <select id="operator" name="operator" class="form-select">
                <option value="">{{ __('users.phone_directory_operator_all') }}</option>
                @foreach($operators as $operatorOption)
                  <option value="{{ $operatorOption }}" {{ $filters['operator'] === $operatorOption ? 'selected' : '' }}>
                    {{ $operatorOption }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
              <div class="d-flex gap-2 w-100">
                <button type="submit" class="btn btn-label-primary w-100">
                  <i class="icon-base ti tabler-filter me-1"></i>{{ __('users.apply_filters') }}
                </button>
                @if ($filters['search'] || $filters['operator'])
                  <a href="{{ route('admin.users.phones.index') }}" class="btn btn-label-secondary w-100">
                    <i class="icon-base ti tabler-refresh me-1"></i>{{ __('users.reset_filters') }}
                  </a>
                @endif
              </div>
            </div>
          </form>
        </div>

        <div class="table-responsive">
          <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
              <tr>
                <th scope="col" style="width: 60px;">#</th>
                <th scope="col">{{ __('users.phone_directory_name') }}</th>
                <th scope="col">{{ __('users.phone_directory_operator') }}</th>
                <th scope="col">{{ __('users.phone_directory_phone') }}</th>
                <th scope="col">{{ __('users.phone_directory_comment') }}</th>
                <th scope="col">{{ __('users.phone_directory_updated_at') }}</th>
                <th scope="col" class="text-end">{{ __('users.phone_directory_actions') }}</th>
              </tr>
            </thead>
            <tbody>
              @forelse($phoneContacts as $index => $phoneContact)
                <tr>
                  <td>{{ $phoneContacts->firstItem() + $index }}</td>
                  <td>
                    <div class="d-flex flex-column">
                      <span class="fw-semibold">{{ $phoneContact->name }}</span>
                    </div>
                  </td>
                  <td>
                    @if($phoneContact->operator)
                      <span class="badge bg-label-info text-uppercase">{{ $phoneContact->operator }}</span>
                    @else
                      <span class="text-muted">—</span>
                    @endif
                  </td>
                  <td>
                    <a href="tel:{{ $phoneContact->phone }}" class="text-body">{{ $phoneContact->phone }}</a>
                  </td>
                  <td style="max-width: 260px;">
                    @if($phoneContact->comment)
                      <span class="text-break">{{ \Illuminate\Support\Str::limit($phoneContact->comment, 100) }}</span>
                    @else
                      <span class="text-muted">—</span>
                    @endif
                  </td>
                  <td>
                    <small class="text-muted">{{ optional($phoneContact->updated_at)->format('d.m.Y H:i') ?? '—' }}</small>
                  </td>
                  <td class="text-end">
                    @if($canEditPhone || $canDeletePhone)
                      <div class="d-flex justify-content-end gap-2">
                        @if($canEditPhone)
                          <button
                            type="button"
                            class="btn btn-sm btn-label-primary"
                            data-bs-toggle="offcanvas"
                            data-bs-target="#editPhoneCanvas{{ $phoneContact->id }}"
                            aria-controls="editPhoneCanvas{{ $phoneContact->id }}">
                            <i class="ti tabler-edit me-1"></i>{{ __('users.phone_directory_edit') }}
                          </button>
                        @endif
                        @if($canDeletePhone)
                          <form
                            action="{{ route('admin.users.phones.destroy', $phoneContact) }}"
                            method="POST"
                            onsubmit="return confirm('{{ __('users.phone_directory_delete_confirm') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-label-danger">
                              <i class="ti tabler-trash"></i>
                            </button>
                          </form>
                        @endif
                      </div>
                    @else
                      <span class="text-muted">—</span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="text-center py-5">
                    <div class="d-flex flex-column align-items-center">
                      <i class="ti tabler-address-book text-muted mb-3" style="font-size: 48px;"></i>
                      <h6 class="mb-1">{{ __('users.phone_directory_empty_title') }}</h6>
                      <p class="text-muted mb-0">{{ __('users.phone_directory_empty_description') }}</p>
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        @if($phoneContacts->hasPages())
          <div class="card-footer d-flex justify-content-between flex-column flex-md-row align-items-md-center gap-2">
            <div class="text-muted">
              {{ trans_choice(':count запись|:count записи|:count записей', $phoneContacts->total(), ['count' => $phoneContacts->total()]) }}
            </div>
            {{ $phoneContacts->onEachSide(1)->links('pagination::bootstrap-5') }}
          </div>
        @endif
      </div>
    </div>
  </div>

  @if($canCreatePhone)
    <div class="offcanvas offcanvas-end" tabindex="-1" id="createPhoneCanvas" aria-labelledby="createPhoneCanvasLabel">
      <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title" id="createPhoneCanvasLabel">
          {{ __('users.phone_directory_create_title') }}
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="{{ __('Закрыть') }}"></button>
      </div>
      <div class="offcanvas-body">
        <form action="{{ route('admin.users.phones.store') }}" method="POST" class="d-flex flex-column h-100">
          @csrf
          <div class="mb-3">
            <label class="form-label" for="create-name">{{ __('users.phone_directory_field_name') }}</label>
            <input
              type="text"
              id="create-name"
              name="name"
              value="{{ old('name') }}"
              class="form-control"
              required
              placeholder="{{ __('users.phone_directory_field_name_placeholder') }}">
          </div>
          <div class="mb-3">
            <label class="form-label" for="create-operator">{{ __('users.phone_directory_field_operator') }}</label>
            <input
              type="text"
              id="create-operator"
              name="operator"
              value="{{ old('operator') }}"
              class="form-control"
              list="operatorSuggestions"
              placeholder="{{ __('users.phone_directory_field_operator_placeholder') }}">
          </div>
          <div class="mb-3">
            <label class="form-label" for="create-phone">{{ __('users.phone_directory_field_phone') }}</label>
          <input
            type="text"
            id="create-phone"
            name="phone"
            value="{{ old('phone') }}"
            class="form-control"
            required
            placeholder="{{ __('users.phone_directory_field_phone_placeholder') }}">
        </div>
        <div class="mb-3">
          <label class="form-label" for="create-comment">{{ __('users.phone_directory_field_comment') }}</label>
          <textarea
            id="create-comment"
            name="comment"
            rows="4"
            class="form-control"
            placeholder="{{ __('users.phone_directory_field_comment_placeholder') }}">{{ old('comment') }}</textarea>
        </div>
        <div class="mt-auto pt-3 border-top">
          <div class="d-flex justify-content-between">
            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">
              {{ __('users.phone_directory_cancel') }}
            </button>
            <button type="submit" class="btn btn-primary">
              <i class="ti tabler-device-floppy me-1"></i>{{ __('users.phone_directory_save') }}
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
  @endif

  @if($canEditPhone)
    @foreach($phoneContacts as $phoneContact)
      <div class="offcanvas offcanvas-end" tabindex="-1" id="editPhoneCanvas{{ $phoneContact->id }}" aria-labelledby="editPhoneCanvasLabel{{ $phoneContact->id }}">
      <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title" id="editPhoneCanvasLabel{{ $phoneContact->id }}">
          {{ __('users.phone_directory_edit_title', ['name' => $phoneContact->name]) }}
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="{{ __('Закрыть') }}"></button>
      </div>
      <div class="offcanvas-body">
        <form action="{{ route('admin.users.phones.update', $phoneContact) }}" method="POST" class="d-flex flex-column h-100">
          @csrf
          @method('PUT')
          <div class="mb-3">
            <label class="form-label" for="name-{{ $phoneContact->id }}">{{ __('users.phone_directory_field_name') }}</label>
            <input
              type="text"
              id="name-{{ $phoneContact->id }}"
              name="name"
              value="{{ old('name', $phoneContact->name) }}"
              class="form-control"
              required
              placeholder="{{ __('users.phone_directory_field_name_placeholder') }}">
          </div>
          <div class="mb-3">
            <label class="form-label" for="operator-{{ $phoneContact->id }}">{{ __('users.phone_directory_field_operator') }}</label>
            <input
              type="text"
              id="operator-{{ $phoneContact->id }}"
              name="operator"
              value="{{ old('operator', $phoneContact->operator) }}"
              class="form-control"
              list="operatorSuggestions"
              placeholder="{{ __('users.phone_directory_field_operator_placeholder') }}">
          </div>
          <div class="mb-3">
            <label class="form-label" for="phone-{{ $phoneContact->id }}">{{ __('users.phone_directory_field_phone') }}</label>
            <input
              type="text"
              id="phone-{{ $phoneContact->id }}"
              name="phone"
              value="{{ old('phone', $phoneContact->phone) }}"
              class="form-control"
              required
              placeholder="{{ __('users.phone_directory_field_phone_placeholder') }}">
          </div>
          <div class="mb-3">
            <label class="form-label" for="comment-{{ $phoneContact->id }}">{{ __('users.phone_directory_field_comment') }}</label>
            <textarea
              id="comment-{{ $phoneContact->id }}"
              name="comment"
              rows="4"
              class="form-control"
              placeholder="{{ __('users.phone_directory_field_comment_placeholder') }}">{{ old('comment', $phoneContact->comment) }}</textarea>
          </div>
          <div class="mt-auto pt-3 border-top">
            <div class="d-flex justify-content-between">
              <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">
                {{ __('users.phone_directory_cancel') }}
              </button>
              <button type="submit" class="btn btn-primary">
                <i class="ti tabler-device-floppy me-1"></i>{{ __('users.phone_directory_save') }}
              </button>
            </div>
          </div>
        </form>
      </div>
      </div>
    @endforeach
  @endif

  <datalist id="operatorSuggestions">
    @foreach($operators as $operatorOption)
      <option value="{{ $operatorOption }}"></option>
    @endforeach
  </datalist>
@endsection


