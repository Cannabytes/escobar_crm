@extends('layouts.admin')

@section('title', __('logs.page_title'))

@section('content')
    <div class="row">
        <div class="col-12">
            <!-- Заголовок -->
            <div class="card mb-4">
                <div class="card-body">
                    <h4 class="fw-bold mb-1">
                        <i class="menu-icon tf-icons bx bx-list-ul"></i>
                        {{ __('logs.page_title') }}
                    </h4>
                    <p class="mb-0">{{ __('logs.page_subtitle') }}</p>
                </div>
            </div>

            <!-- Фильтры -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bx bx-filter-alt me-2"></i>{{ __('logs.filter_apply') }}
                    </h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.logs.index') }}" class="row g-3">
                        <!-- Поиск -->
                        <div class="col-md-4">
                            <label class="form-label">{{ __('logs.filter_search') }}</label>
                            <input type="text" name="search" class="form-control" 
                                   value="{{ request('search') }}" 
                                   placeholder="{{ __('logs.filter_search') }}">
                        </div>

                        <!-- Пользователь -->
                        <div class="col-md-4">
                            <label class="form-label">{{ __('logs.filter_user') }}</label>
                            <select name="user_id" class="form-select">
                                <option value="">{{ __('logs.filter_all') }}</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" 
                                            {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Действие -->
                        <div class="col-md-4">
                            <label class="form-label">{{ __('logs.filter_action') }}</label>
                            <select name="action" class="form-select">
                                <option value="">{{ __('logs.filter_all') }}</option>
                                @foreach($actions as $action)
                                    <option value="{{ $action }}" 
                                            {{ request('action') == $action ? 'selected' : '' }}>
                                        {{ $action }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Модель -->
                        <div class="col-md-4">
                            <label class="form-label">{{ __('logs.filter_model') }}</label>
                            <select name="model_type" class="form-select">
                                <option value="">{{ __('logs.filter_all') }}</option>
                                @foreach($modelTypes as $modelType)
                                    <option value="{{ $modelType }}" 
                                            {{ request('model_type') == $modelType ? 'selected' : '' }}>
                                        {{ class_basename($modelType) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Уровень -->
                        <div class="col-md-4">
                            <label class="form-label">{{ __('logs.filter_level') }}</label>
                            <select name="level" class="form-select">
                                <option value="">{{ __('logs.filter_all') }}</option>
                                <option value="info" {{ request('level') == 'info' ? 'selected' : '' }}>
                                    {{ __('logs.level_info') }}
                                </option>
                                <option value="warning" {{ request('level') == 'warning' ? 'selected' : '' }}>
                                    {{ __('logs.level_warning') }}
                                </option>
                                <option value="error" {{ request('level') == 'error' ? 'selected' : '' }}>
                                    {{ __('logs.level_error') }}
                                </option>
                                <option value="critical" {{ request('level') == 'critical' ? 'selected' : '' }}>
                                    {{ __('logs.level_critical') }}
                                </option>
                            </select>
                        </div>

                        <!-- Дата от -->
                        <div class="col-md-2">
                            <label class="form-label">{{ __('logs.filter_date_from') }}</label>
                            <input type="date" name="date_from" class="form-control" 
                                   value="{{ request('date_from') }}">
                        </div>

                        <!-- Дата до -->
                        <div class="col-md-2">
                            <label class="form-label">{{ __('logs.filter_date_to') }}</label>
                            <input type="date" name="date_to" class="form-control" 
                                   value="{{ request('date_to') }}">
                        </div>

                        <!-- Кнопки -->
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-search me-1"></i>{{ __('logs.filter_apply') }}
                            </button>
                            <a href="{{ route('admin.logs.index') }}" class="btn btn-label-secondary">
                                <i class="bx bx-reset me-1"></i>{{ __('logs.filter_reset') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Таблица логов -->
            <div class="card">
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('logs.table_date') }}</th>
                                <th>{{ __('logs.table_user') }}</th>
                                <th>{{ __('logs.table_action') }}</th>
                                <th>{{ __('logs.table_model') }}</th>
                                <th>{{ __('logs.table_description') }}</th>
                                <th>{{ __('logs.table_level') }}</th>
                                <th>{{ __('logs.table_ip') }}</th>
                                <th>{{ __('logs.table_details') }}</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse($logs as $log)
                                <tr>
                                    <td>
                                        <small class="text-muted">
                                            {{ $log->created_at->format('d.m.Y H:i:s') }}
                                        </small>
                                    </td>
                                    <td>
                                        @if($log->user)
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-xs me-2">
                                                    <img src="{{ $log->user->avatar_url }}" 
                                                         alt="{{ $log->user->name }}" 
                                                         class="rounded-circle">
                                                </div>
                                                <span>{{ $log->user->name }}</span>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-label-primary">
                                            {{ $log->action_name }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($log->model_name)
                                            <span class="badge bg-label-secondary">
                                                {{ $log->model_name }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ \Illuminate\Support\Str::limit($log->description, 50) }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-{{ $log->level_color }}">
                                            {{ __('logs.level_' . $log->level) }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $log->ip_address }}</small>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-icon btn-label-primary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#logDetailModal{{ $log->id }}">
                                            <i class="bx bx-show"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Modal для деталей -->
                                <div class="modal fade" id="logDetailModal{{ $log->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">{{ __('logs.view_details') }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <dl class="row mb-0">
                                                    <dt class="col-sm-3">{{ __('logs.table_date') }}:</dt>
                                                    <dd class="col-sm-9">{{ $log->created_at->format('d.m.Y H:i:s') }}</dd>

                                                    <dt class="col-sm-3">{{ __('logs.table_user') }}:</dt>
                                                    <dd class="col-sm-9">{{ $log->user ? $log->user->name : '-' }}</dd>

                                                    <dt class="col-sm-3">{{ __('logs.table_action') }}:</dt>
                                                    <dd class="col-sm-9">{{ $log->action_name }}</dd>

                                                    <dt class="col-sm-3">{{ __('logs.table_model') }}:</dt>
                                                    <dd class="col-sm-9">{{ $log->model_name ?? '-' }}</dd>

                                                    <dt class="col-sm-3">{{ __('logs.table_description') }}:</dt>
                                                    <dd class="col-sm-9">{{ $log->description }}</dd>

                                                    <dt class="col-sm-3">{{ __('logs.table_ip') }}:</dt>
                                                    <dd class="col-sm-9">{{ $log->ip_address }}</dd>

                                                    <dt class="col-sm-3">URL:</dt>
                                                    <dd class="col-sm-9"><small>{{ $log->url }}</small></dd>

                                                    <dt class="col-sm-3">HTTP Method:</dt>
                                                    <dd class="col-sm-9">{{ $log->http_method }}</dd>

                                                    <dt class="col-sm-3">User Agent:</dt>
                                                    <dd class="col-sm-9"><small>{{ $log->user_agent }}</small></dd>

                                                    @if($log->old_values)
                                                        <dt class="col-sm-3">{{ __('logs.old_values') }}:</dt>
                                                        <dd class="col-sm-9">
                                                            <pre class="bg-light p-2 rounded"><code>{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                                                        </dd>
                                                    @endif

                                                    @if($log->new_values)
                                                        <dt class="col-sm-3">{{ __('logs.new_values') }}:</dt>
                                                        <dd class="col-sm-9">
                                                            <pre class="bg-light p-2 rounded"><code>{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                                                        </dd>
                                                    @endif

                                                    @if($log->metadata)
                                                        <dt class="col-sm-3">{{ __('logs.metadata') }}:</dt>
                                                        <dd class="col-sm-9">
                                                            <pre class="bg-light p-2 rounded"><code>{{ json_encode($log->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                                                        </dd>
                                                    @endif
                                                </dl>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="bx bx-info-circle bx-lg mb-3"></i>
                                            <p class="mb-0">{{ __('logs.no_logs') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($logs->hasPages())
                    <div class="card-footer">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

