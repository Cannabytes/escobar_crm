@extends('layouts.admin')

@section('title', __('ledger.page_title'))

@section('content')
  <div class="row">
    <div class="col-12">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
        <div>
          <h4 class="mb-1">{{ __('ledger.page_title') }}</h4>
          <p class="text-muted mb-0">{{ __('ledger.page_subtitle') }}</p>
        </div>
        @if($canManageLedger)
          <button
            type="button"
            class="btn btn-primary"
            data-bs-toggle="offcanvas"
            data-bs-target="#createLedgerCanvas"
            aria-controls="createLedgerCanvas">
            <i class="ti tabler-plus me-1"></i>{{ __('ledger.actions.create') }}
          </button>
        @endif
      </div>

      @if(session('error'))
        <div class="alert alert-danger alert-dismissible mb-4" role="alert">
          <div class="d-flex align-items-center">
            <i class="ti tabler-alert-triangle text-danger me-2"></i>
            <span>{{ session('error') }}</span>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('ledger.actions.close') }}"></button>
        </div>
      @endif

      @if($errors->any())
        <div class="alert alert-danger alert-dismissible mb-4" role="alert">
          <div class="d-flex align-items-start">
            <i class="ti tabler-alert-triangle me-2"></i>
            <div>
              <strong>{{ __('ledger.validation_error_title') }}</strong>
              <ul class="mb-0 mt-2 ps-3">
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('ledger.actions.close') }}"></button>
        </div>
      @endif

      <div class="card mb-4">
        <div class="card-header">
          <form method="GET" action="{{ route('admin.ledger.index') }}" class="row g-3 align-items-end">
            <div class="col-md-5">
              <label for="ledger-search" class="form-label mb-1">{{ __('ledger.filters.search_label') }}</label>
              <div class="input-group">
                <span class="input-group-text">
                  <i class="icon-base ti tabler-search"></i>
                </span>
                <input
                  type="search"
                  id="ledger-search"
                  name="search"
                  value="{{ $filters['search'] }}"
                  class="form-control"
                  placeholder="{{ __('ledger.filters.search_placeholder') }}">
              </div>
            </div>
            <div class="col-md-3">
              <label for="ledger-network" class="form-label mb-1">{{ __('ledger.filters.network_label') }}</label>
              <select id="ledger-network" name="network" class="form-select select2">
                <option value="">{{ __('ledger.filters.network_all') }}</option>
                @foreach($networks as $network)
                  <option value="{{ $network }}" {{ $filters['network'] === $network ? 'selected' : '' }}>
                    {{ $network }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-2">
              <label for="ledger-currency" class="form-label mb-1">{{ __('ledger.filters.currency_label') }}</label>
              <select id="ledger-currency" name="currency" class="form-select select2">
                <option value="">{{ __('ledger.filters.currency_all') }}</option>
                @foreach($currencies as $currency)
                  <option value="{{ $currency }}" {{ $filters['currency'] === $currency ? 'selected' : '' }}>
                    {{ $currency }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-2">
              <label for="ledger-status" class="form-label mb-1">{{ __('ledger.filters.status_label') }}</label>
              <select id="ledger-status" name="status" class="form-select select2">
                <option value="">{{ __('ledger.filters.status_all') }}</option>
                @foreach($statuses as $status)
                  <option value="{{ $status }}" {{ $filters['status'] === $status ? 'selected' : '' }}>
                    {{ __('ledger.statuses.' . $status) }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-12 d-flex flex-column flex-md-row gap-2">
              <button type="submit" class="btn btn-label-primary">
                <i class="icon-base ti tabler-filter me-1"></i>{{ __('ledger.filters.apply') }}
              </button>
              @if($filters['search'] || $filters['network'] || $filters['currency'] || $filters['status'])
                <a href="{{ route('admin.ledger.index') }}" class="btn btn-label-secondary">
                  <i class="icon-base ti tabler-refresh me-1"></i>{{ __('ledger.filters.reset') }}
                </a>
              @endif
            </div>
          </form>
        </div>

        <div class="table-responsive">
          <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
              <tr>
                <th scope="col" style="width: 60px;">#</th>
                <th scope="col">{{ __('ledger.table.wallet') }}</th>
                <th scope="col">{{ __('ledger.table.network') }}</th>
                <th scope="col">{{ __('ledger.table.currency') }}</th>
                <th scope="col">{{ __('ledger.table.status') }}</th>
                <th scope="col">{{ __('ledger.table.updated_at') }}</th>
                <th scope="col" class="text-end">{{ __('ledger.table.actions') }}</th>
              </tr>
            </thead>
            <tbody>
              @forelse($ledgers as $index => $ledger)
                <tr>
                  <td>{{ $ledgers->firstItem() + $index }}</td>
                  <td class="fw-semibold">
                    <span class="ledger-wallet-text">{{ $ledger->wallet }}</span>
                  </td>
                  <td>
                    @if($ledger->network)
                      <span class="badge ledger-pill ledger-pill-network">
                        <i class="ti tabler-network me-1"></i>{{ $ledger->network }}
                      </span>
                    @else
                      <span class="text-muted">—</span>
                    @endif
                  </td>
                  <td>
                    @if($ledger->currency)
                      <span class="badge ledger-pill ledger-pill-currency">
                        <i class="ti tabler-coin me-1"></i>{{ $ledger->currency }}
                      </span>
                    @else
                      <span class="text-muted">—</span>
                    @endif
                  </td>
                  <td>
                    <span class="badge {{ $ledger->isActive() ? 'bg-label-success' : 'bg-label-secondary' }}">
                      {{ $ledger->isActive() ? __('ledger.statuses.active') : __('ledger.statuses.inactive') }}
                    </span>
                  </td>
                  <td>
                    <small class="text-muted">{{ optional($ledger->updated_at)->format('d.m.Y H:i') ?? '—' }}</small>
                  </td>
                  <td class="text-end">
                    @if($canManageLedger)
                      <div class="d-flex justify-content-end gap-2">
                        <button
                          type="button"
                          class="btn btn-sm btn-label-primary"
                          data-bs-toggle="offcanvas"
                          data-bs-target="#editLedgerCanvas{{ $ledger->id }}"
                          aria-controls="editLedgerCanvas{{ $ledger->id }}">
                          <i class="ti tabler-edit me-1"></i>{{ __('ledger.actions.edit') }}
                        </button>
                        <form
                          action="{{ route('admin.ledger.destroy', $ledger) }}"
                          method="POST"
                          class="ledger-delete-form"
                          data-ledger-wallet="{{ $ledger->wallet }}">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-sm btn-label-danger">
                            <i class="ti tabler-trash"></i>
                          </button>
                        </form>
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
                      <i class="ti tabler-wallet text-muted mb-3" style="font-size: 48px;"></i>
                      <h6 class="mb-1">{{ __('ledger.empty.title') }}</h6>
                      <p class="text-muted mb-0">{{ __('ledger.empty.description') }}</p>
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        @if($ledgers->hasPages())
          <div class="card-footer d-flex justify-content-between flex-column flex-md-row align-items-md-center gap-2">
            <div class="text-muted">
              {{ trans_choice('ledger.records_count', $ledgers->total(), ['count' => $ledgers->total()]) }}
            </div>
            {{ $ledgers->onEachSide(1)->links('pagination::bootstrap-5') }}
          </div>
        @endif
      </div>
    </div>
  </div>

  @if($canManageLedger)
    <div class="offcanvas offcanvas-end" tabindex="-1" id="createLedgerCanvas" aria-labelledby="createLedgerCanvasLabel">
      <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title" id="createLedgerCanvasLabel">
          {{ __('ledger.create.title') }}
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="{{ __('ledger.actions.close') }}"></button>
      </div>
      <div class="offcanvas-body">
        <form action="{{ route('admin.ledger.store') }}" method="POST" class="d-flex flex-column h-100">
          @csrf
          <div class="mb-3">
            <label class="form-label" for="create-ledger-wallet">{{ __('ledger.fields.wallet') }}</label>
            <input
              type="text"
              id="create-ledger-wallet"
              name="wallet"
              value="{{ old('wallet') }}"
              class="form-control"
              required
              placeholder="{{ __('ledger.placeholders.wallet') }}">
          </div>
          <div class="mb-3">
            <label class="form-label" for="create-ledger-network">{{ __('ledger.fields.network') }}</label>
            <input
              type="text"
              id="create-ledger-network"
              name="network"
              value="{{ old('network') }}"
              class="form-control"
              list="ledgerNetworkSuggestions"
              placeholder="{{ __('ledger.placeholders.network') }}">
          </div>
          <div class="mb-3">
            <label class="form-label" for="create-ledger-currency">{{ __('ledger.fields.currency') }}</label>
            <input
              type="text"
              id="create-ledger-currency"
              name="currency"
              value="{{ old('currency') }}"
              class="form-control"
              list="ledgerCurrencySuggestions"
              placeholder="{{ __('ledger.placeholders.currency') }}">
          </div>
          <div class="mb-3">
            <label class="form-label">{{ __('ledger.fields.status') }}</label>
            <div class="d-flex gap-4">
              @foreach($statuses as $status)
                <div class="form-check">
                  <input
                    class="form-check-input"
                    type="radio"
                    name="status"
                    id="create-ledger-status-{{ $status }}"
                    value="{{ $status }}"
                    {{ old('status', \App\Models\Ledger::STATUS_ACTIVE) === $status ? 'checked' : '' }}
                    required>
                  <label class="form-check-label" for="create-ledger-status-{{ $status }}">
                    {{ __('ledger.statuses.' . $status) }}
                  </label>
                </div>
              @endforeach
            </div>
          </div>
          <div class="mt-auto pt-3 border-top">
            <div class="d-flex justify-content-between">
              <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">
                {{ __('ledger.actions.cancel') }}
              </button>
              <button type="submit" class="btn btn-primary">
                <i class="ti tabler-device-floppy me-1"></i>{{ __('ledger.actions.save') }}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>

    @foreach($ledgers as $ledger)
      <div class="offcanvas offcanvas-end" tabindex="-1" id="editLedgerCanvas{{ $ledger->id }}" aria-labelledby="editLedgerCanvasLabel{{ $ledger->id }}">
        <div class="offcanvas-header border-bottom">
          <h5 class="offcanvas-title" id="editLedgerCanvasLabel{{ $ledger->id }}">
            {{ __('ledger.edit.title', ['wallet' => $ledger->wallet]) }}
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="{{ __('ledger.actions.close') }}"></button>
        </div>
        <div class="offcanvas-body">
          <form action="{{ route('admin.ledger.update', $ledger) }}" method="POST" class="d-flex flex-column h-100">
            @csrf
            @method('PUT')
            <div class="mb-3">
              <label class="form-label" for="edit-ledger-wallet-{{ $ledger->id }}">{{ __('ledger.fields.wallet') }}</label>
              <input
                type="text"
                id="edit-ledger-wallet-{{ $ledger->id }}"
                name="wallet"
                value="{{ old('wallet', $ledger->wallet) }}"
                class="form-control"
                required
                placeholder="{{ __('ledger.placeholders.wallet') }}">
            </div>
            <div class="mb-3">
              <label class="form-label" for="edit-ledger-network-{{ $ledger->id }}">{{ __('ledger.fields.network') }}</label>
              <input
                type="text"
                id="edit-ledger-network-{{ $ledger->id }}"
                name="network"
                value="{{ old('network', $ledger->network) }}"
                class="form-control"
                list="ledgerNetworkSuggestions"
                placeholder="{{ __('ledger.placeholders.network') }}">
            </div>
            <div class="mb-3">
              <label class="form-label" for="edit-ledger-currency-{{ $ledger->id }}">{{ __('ledger.fields.currency') }}</label>
              <input
                type="text"
                id="edit-ledger-currency-{{ $ledger->id }}"
                name="currency"
                value="{{ old('currency', $ledger->currency) }}"
                class="form-control"
                list="ledgerCurrencySuggestions"
                placeholder="{{ __('ledger.placeholders.currency') }}">
            </div>
            <div class="mb-3">
              <label class="form-label">{{ __('ledger.fields.status') }}</label>
              <div class="d-flex gap-4">
                @foreach($statuses as $status)
                  <div class="form-check">
                    <input
                      class="form-check-input"
                      type="radio"
                      name="status"
                      id="edit-ledger-status-{{ $ledger->id }}-{{ $status }}"
                      value="{{ $status }}"
                      {{ old('status', $ledger->status) === $status ? 'checked' : '' }}
                      required>
                    <label class="form-check-label" for="edit-ledger-status-{{ $ledger->id }}-{{ $status }}">
                      {{ __('ledger.statuses.' . $status) }}
                    </label>
                  </div>
                @endforeach
              </div>
            </div>
            <div class="mt-auto pt-3 border-top">
              <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">
                  {{ __('ledger.actions.cancel') }}
                </button>
                <button type="submit" class="btn btn-primary">
                  <i class="ti tabler-device-floppy me-1"></i>{{ __('ledger.actions.save') }}
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    @endforeach
  @endif

  <datalist id="ledgerNetworkSuggestions">
    @foreach($networks as $network)
      <option value="{{ $network }}"></option>
    @endforeach
  </datalist>

  <datalist id="ledgerCurrencySuggestions">
    @foreach($currencies as $currency)
      <option value="{{ $currency }}"></option>
    @endforeach
  </datalist>
@endsection

@push('styles')
  <link rel="stylesheet" href="{{ asset('vendor/vuexy/vendor/libs/sweetalert2/sweetalert2.css') }}">
  <link rel="stylesheet" href="{{ asset('vendor/vuexy/vendor/libs/select2/select2.css') }}">
  <style>
    .ledger-pill {
      font-weight: 600;
      border-radius: 999px;
      display: inline-flex;
      align-items: center;
      gap: 4px;
      padding: 0.35rem 0.85rem;
      font-size: 0.85rem;
    }
    .ledger-pill-network {
      background: rgba(105, 108, 255, 0.12);
      color: #696cff;
    }
    .ledger-pill-currency {
      background: rgba(113, 221, 55, 0.12);
      color: #71dd37;
    }
    .ledger-wallet-text {
      font-size: 0.95rem;
    }
    /* Select2 стили - z-index ниже чем у offcanvas (1045) */
    .select2-container {
      z-index: 1040 !important;
    }
    .select2-dropdown {
      z-index: 1041 !important;
    }
    .select2-container--open {
      z-index: 1042 !important;
    }
    /* Скрываем оригинальный select */
    select.select2 {
      opacity: 0;
      position: absolute;
      z-index: -1;
    }
    /* Убеждаемся, что Select2 контейнер перехватывает все клики */
    .select2-selection {
      pointer-events: auto !important;
    }
    .was-validated .select2-selection {
      border-color: #dc3545 !important;
    }
  </style>
@endpush

@push('scripts')
  <script src="{{ asset('vendor/vuexy/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
  <script src="{{ asset('vendor/vuexy/vendor/libs/select2/select2.js') }}"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // Инициализация Select2
      if (typeof $ !== 'undefined' && $.fn.select2) {
        $('.select2').each(function() {
          const $select = $(this);
          // Проверяем, не инициализирован ли уже select2
          if ($select.data('select2')) {
            return;
          }
          
          // Определяем dropdownParent - для элементов в offcanvas используем body
          const dropdownParent = $select.closest('.offcanvas').length ? $('body') : $('body');
          
          $select.select2({
            placeholder: $select.data('placeholder') || '{{ __('Выберите из списка') }}',
            allowClear: false,
            width: '100%',
            dropdownParent: dropdownParent,
            language: {
              noResults: function() {
                return '{{ __('Результаты не найдены') }}';
              }
            }
          });
        });
      }

      // Управление z-index select2 при открытии/закрытии offcanvas
      document.querySelectorAll('.offcanvas').forEach(function(offcanvas) {
        offcanvas.addEventListener('show.bs.offcanvas', function() {
          // Снижаем z-index select2 когда offcanvas открывается
          document.querySelectorAll('.select2-container').forEach(function(container) {
            container.style.zIndex = '1035';
          });
          document.querySelectorAll('.select2-dropdown').forEach(function(dropdown) {
            dropdown.style.zIndex = '1036';
          });
        });
        
        offcanvas.addEventListener('hidden.bs.offcanvas', function() {
          // Восстанавливаем z-index select2 когда offcanvas закрывается
          document.querySelectorAll('.select2-container').forEach(function(container) {
            container.style.zIndex = '';
          });
          document.querySelectorAll('.select2-dropdown').forEach(function(dropdown) {
            dropdown.style.zIndex = '';
          });
        });
      });

      const messages = {
        confirmTitle: @json(__('ledger.delete_confirm_title')),
        confirmText: @json(__('ledger.delete_confirm_text')),
        fallback: @json(__('ledger.delete_fallback')),
        confirmButton: @json(__('ledger.actions.delete')),
        cancelButton: @json(__('ledger.actions.cancel')),
        walletLabel: @json(__('ledger.table.wallet')),
      };

      document.querySelectorAll('.ledger-delete-form').forEach(function (form) {
        form.addEventListener('submit', async function (event) {
          event.preventDefault();

          const wallet = this.dataset.ledgerWallet || messages.walletLabel;
          const confirmText = messages.confirmText.replace(':wallet', wallet);
          const fallbackText = messages.fallback.replace(':wallet', wallet);

          if (typeof Swal === 'undefined') {
            if (confirm(fallbackText)) {
              this.submit();
            }
            return;
          }

          const result = await Swal.fire({
            title: messages.confirmTitle,
            text: confirmText,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: messages.confirmButton,
            cancelButtonText: messages.cancelButton,
            reverseButtons: true,
          });

          if (result.isConfirmed) {
            this.submit();
          }
        });
      });
    });
  </script>
@endpush

