@extends('layouts.admin')

@section('title', __('Статистика'))

@section('content')
  <div class="row">
    <div class="col-12">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
        <div>
          <h4 class="mb-1">{{ __('Статистика по компаниям и банкам') }}</h4>
          <p class="text-muted mb-0">{{ __('Обзор состояния банков по компаниям') }}</p>
        </div>
      </div>

      @if(session('status'))
        <div class="alert alert-success alert-dismissible mb-4" role="alert">
          <div class="d-flex align-items-center">
            <i class="ti tabler-check text-success me-2"></i>
            <span>{{ session('status') }}</span>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif

      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0">{{ __('Фильтр по странам') }}</h5>
        </div>
        <div class="card-body">
          <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.statistics.index') }}" 
               class="btn {{ !$selectedCountry ? 'btn-primary' : 'btn-outline-primary' }}">
              {{ __('Все страны') }}
            </a>
            @foreach ($countries as $code => $name)
              <a href="{{ route('admin.statistics.index', ['country' => $code]) }}" 
                 class="btn {{ $selectedCountry === $code ? 'btn-primary' : 'btn-outline-primary' }}">
                {{ __($name) }}
              </a>
            @endforeach
          </div>
        </div>
      </div>

      @if($companies->isEmpty())
        <div class="card">
          <div class="card-body text-center py-5">
            <div class="mb-3">
              <i class="mdi mdi-bank-outline" style="font-size: 48px; color: #ccc;"></i>
            </div>
            <p class="text-muted">{{ __('Компании не найдены') }}</p>
          </div>
        </div>
      @else
        <div class="card">
          <div class="card-body">
            <div class="table-responsive" style="overflow-x: auto;">
              <table class="table table-bordered table-hover" style="min-width: 800px;">
                <thead>
                  <tr>
                    <th style="position: sticky; left: 0; z-index: 10; min-width: 200px;">
                      {{ __('Компания') }}
                    </th>
                    @foreach ($banksForCountry as $bankFullName => $bankShortName)
                      <th class="text-center" style="min-width: 120px; white-space: nowrap;">
                        <div>{{ $bankShortName }}</div>
                        <small class="text-muted" style="font-size: 0.7rem;">{{ $bankFullName }}</small>
                      </th>
                    @endforeach
                  </tr>
                </thead>
                <tbody>
                  @foreach ($tableData as $row)
                    <tr>
                      <td style="position: sticky; left: 0; z-index: 9; font-weight: 500;">
                        <a href="{{ route('admin.companies.show', $row['company']) }}" class="text-body">
                          {{ $row['company']->name }}
                        </a>
                        <br>
                        <small class="text-muted">{{ $row['company']->country }}</small>
                      </td>
                      @foreach ($row['banks'] as $bankFullName => $bankData)
                        <td class="text-center align-middle">
                          @if ($bankData['exists'])
                            @php
                              $statusColors = [
                                'active' => 'success',
                                'inactive' => 'secondary',
                                'hold' => 'warning',
                                'closed' => 'danger',
                              ];
                              $statusColor = $statusColors[$bankData['status']] ?? 'secondary';
                              $statusLabels = \App\Models\Bank::getStatuses();
                            @endphp
                            <span class="badge bg-label-{{ $statusColor }}">
                              {{ $statusLabels[$bankData['status']] ?? __('Active') }}
                            </span>
                          @else
                            <span class="badge bg-label-light text-muted">{{ __('Not open') }}</span>
                          @endif
                        </td>
                      @endforeach
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      @endif
    </div>
  </div>
@endsection

@push('styles')
  <style>
    .table-responsive {
      max-height: calc(100vh - 300px);
      overflow-y: auto;
    }
    
    thead th {
      position: sticky;
      top: 0;
      z-index: 11;
      box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
    }
    
    tbody td {
      vertical-align: middle;
    }
    
    .table-bordered th,
    .table-bordered td {
    }
  </style>
@endpush

