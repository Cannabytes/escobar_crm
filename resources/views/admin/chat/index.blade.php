@extends('layouts.admin')

@section('title', __('Чат'))

@push('styles')
  <link rel="stylesheet" href="{{ url('public/vendor/vuexy/vendor/css/pages/app-chat.css') }}">
@endpush

@section('content')
  @php
    /** @var \App\Models\User $currentUser */
    $currentUser = auth()->user();
  @endphp
  <div class="app-chat card overflow-hidden" id="chat-app">
    <div class="row g-0 h-100">
      <div class="col-12 col-lg-3 col-xl-3 border-end app-chat-contacts" id="chat-sidebar">
        <div class="sidebar-header px-4 py-4 border-bottom d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center">
            <div class="avatar avatar-online">
              <img src="{{ $currentUser->avatar_url }}" class="rounded-circle" alt="{{ $currentUser->name }}">
            </div>
            <div class="ms-3">
              <h6 class="mb-0">{{ $currentUser->name }}</h6>
              <small class="text-body-secondary">{{ __('Онлайн') }}</small>
            </div>
          </div>
          <div class="d-flex gap-2">
            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#createPublicRoomModal">
              <i class="icon-base ti tabler-messages"></i>
              <span class="d-none d-xl-inline ms-1">{{ __('Публичный чат') }}</span>
            </button>
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createPrivateRoomModal">
              <i class="icon-base ti tabler-lock"></i>
              <span class="d-none d-xl-inline ms-1">{{ __('Приватный чат') }}</span>
            </button>
          </div>
        </div>
        <div class="px-4 py-3 border-bottom">
          <div class="input-group input-group-merge">
            <span class="input-group-text">
              <i class="icon-base ti tabler-search"></i>
            </span>
            <input type="text" class="form-control" placeholder="{{ __('Поиск чатов или пользователей') }}" id="chat-search-input">
          </div>
        </div>

        <div class="sidebar-body px-0">
          <ul class="list-unstyled chat-contact-list mb-0" id="public-room-list">
            <li class="chat-contact-list-item chat-contact-list-item-title mt-0 px-4">
              <h6 class="text-body-secondary text-uppercase mb-0">{{ __('Публичные чаты') }}</h6>
            </li>
            <li class="chat-contact-list-item py-4 text-center text-body-secondary d-none" id="public-room-empty">
              {{ __('Публичных чатов пока нет') }}
            </li>
          </ul>

          <ul class="list-unstyled chat-contact-list mb-0 mt-4" id="private-room-list">
            <li class="chat-contact-list-item chat-contact-list-item-title mt-0 px-4">
              <h6 class="text-body-secondary text-uppercase mb-0">{{ __('Приватные чаты') }}</h6>
            </li>
            <li class="chat-contact-list-item py-4 text-center text-body-secondary d-none" id="private-room-empty">
              {{ __('Других пользователей пока нет') }}
            </li>
          </ul>
        </div>
      </div>

      <div class="col-12 col-lg-9 col-xl-9 d-flex flex-column" id="chat-content">
        <div class="h-100 d-flex flex-column align-items-center justify-content-center text-center" id="chat-empty-state">
          <div class="bg-label-primary p-5 rounded-circle mb-4">
            <i class="icon-base ti tabler-message-2 icon-40px"></i>
          </div>
          <h5 class="mb-2">{{ __('Выберите чат для начала общения') }}</h5>
          <p class="text-body-secondary mb-4">{{ __('Создайте новый чат или выберите существующий из списка слева.') }}</p>
          <div class="d-flex gap-2 flex-wrap justify-content-center">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPublicRoomModal">
              <i class="icon-base ti tabler-messages me-1"></i>{{ __('Создать публичный чат') }}
            </button>
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#createPrivateRoomModal">
              <i class="icon-base ti tabler-lock me-1"></i>{{ __('Создать приватный чат') }}
            </button>
          </div>
        </div>

        <div class="chat-history-wrapper d-none h-100 d-flex flex-column" id="chat-history-wrapper">
          <div class="chat-history-header border-bottom px-4 py-3 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center overflow-hidden">
              <div class="avatar avatar-online me-3" id="chat-room-avatar">
                <span class="avatar-initial rounded-circle bg-label-primary">C</span>
              </div>
              <div class="flex-grow-1">
                <h6 class="mb-0 text-truncate" id="chat-room-name"></h6>
                <small class="text-body-secondary text-truncate d-block" id="chat-room-meta"></small>
              </div>
            </div>
            <div class="d-flex align-items-center gap-2">
              <button class="btn btn-sm btn-text-secondary btn-icon rounded-pill" id="refresh-messages-btn" title="{{ __('Обновить сообщения') }}">
                <i class="icon-base ti tabler-refresh"></i>
              </button>
            </div>
          </div>

          <div class="chat-history-body flex-grow-1 overflow-auto" id="chat-history-body">
            <ul class="list-unstyled chat-history px-4 py-4 mb-0" id="chat-message-list"></ul>
            <div class="text-center py-3 d-none" id="chat-loading-indicator">
              <span class="spinner-border spinner-border-sm text-primary" role="status"></span>
            </div>
          </div>

          <div class="chat-history-footer px-4 py-3 border-top">
            <form class="d-flex align-items-center gap-3" id="chat-message-form">
              @csrf
              <input type="text" class="form-control message-input border-0 shadow-none" id="chat-message-input" placeholder="{{ __('Введите сообщение...') }}" autocomplete="off">
              <button class="btn btn-primary d-flex align-items-center gap-1" type="submit">
                <span class="d-none d-sm-inline">{{ __('Отправить') }}</span>
                <i class="icon-base ti tabler-send"></i>
              </button>
            </form>
            <div class="invalid-feedback d-block mt-2 d-none" id="chat-error-box"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Создание публичного чата -->
  <div class="modal fade" id="createPublicRoomModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <form class="modal-content" id="create-public-room-form">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">{{ __('Создать публичный чат') }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Закрыть') }}"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="public-room-name" class="form-label">{{ __('Название') }}</label>
            <input type="text" class="form-control" id="public-room-name" name="name" maxlength="120" required>
            <div class="invalid-feedback" id="public-room-error"></div>
          </div>
          <p class="text-body-secondary mb-0">
            {{ __('Публичные чаты доступны всем пользователям системы и отображаются в общем списке.') }}
          </p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-text-secondary" data-bs-dismiss="modal">{{ __('Отмена') }}</button>
          <button type="submit" class="btn btn-primary">
            <i class="icon-base ti tabler-plus me-1"></i>{{ __('Создать') }}
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Создание приватного чата -->
  <div class="modal fade" id="createPrivateRoomModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <form class="modal-content" id="create-private-room-form">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">{{ __('Создать приватный чат') }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Закрыть') }}"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="private-participant-search" class="form-label">{{ __('Пользователь') }}</label>
            <input type="text" class="form-control" id="private-participant-search" placeholder="{{ __('Введите имя или email') }}" autocomplete="off">
            <input type="hidden" name="participant_id" id="private-participant-id">
            <div class="list-group mt-2 d-none" id="private-participant-results"></div>
            <div class="invalid-feedback d-block d-none" id="private-room-error"></div>
          </div>
          <p class="text-body-secondary mb-0">
            {{ __('Приватный чат виден только выбранным участникам. Создайте индивидуальный канал общения.') }}
          </p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-text-secondary" data-bs-dismiss="modal">{{ __('Отмена') }}</button>
          <button type="submit" class="btn btn-primary">
            <i class="icon-base ti tabler-send me-1"></i>{{ __('Начать чат') }}
          </button>
        </div>
      </form>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    (function () {
      const currentUserId = {{ (int) $currentUser->id }};
      const routes = {
        rooms: @json(route('admin.chat.rooms.index')),
        createRoom: @json(route('admin.chat.rooms.store')),
        roomMessages: (roomId) => @json(route('admin.chat.rooms.messages.index', ['room' => '__room__'])).replace('__room__', roomId),
        sendMessage: (roomId) => @json(route('admin.chat.rooms.messages.store', ['room' => '__room__'])).replace('__room__', roomId),
        searchUsers: @json(route('admin.chat.users.search')),
      };

      const initialRooms = @json($initialRooms ?? []);
      const initialPrivateUsers = @json($initialPrivateUsers ?? []);

      const state = {
        rooms: initialRooms,
        privateUsers: initialPrivateUsers,
        currentRoomId: null,
        currentContactId: null,
        creatingRoomForUserId: null,
        messagesInterval: null,
        refreshInterval: null,
      };

      const dom = {
        publicList: document.getElementById('public-room-list'),
        privateList: document.getElementById('private-room-list'),
        publicEmpty: document.getElementById('public-room-empty'),
        privateEmpty: document.getElementById('private-room-empty'),
        emptyState: document.getElementById('chat-empty-state'),
        historyWrapper: document.getElementById('chat-history-wrapper'),
        roomName: document.getElementById('chat-room-name'),
        roomMeta: document.getElementById('chat-room-meta'),
        messageList: document.getElementById('chat-message-list'),
        messageForm: document.getElementById('chat-message-form'),
        messageInput: document.getElementById('chat-message-input'),
        messageError: document.getElementById('chat-error-box'),
        loadingIndicator: document.getElementById('chat-loading-indicator'),
        refreshBtn: document.getElementById('refresh-messages-btn'),
        searchInput: document.getElementById('chat-search-input'),
        publicRoomForm: document.getElementById('create-public-room-form'),
        publicRoomName: document.getElementById('public-room-name'),
        publicRoomError: document.getElementById('public-room-error'),
        privateRoomForm: document.getElementById('create-private-room-form'),
        privateSearch: document.getElementById('private-participant-search'),
        privateUserId: document.getElementById('private-participant-id'),
        privateResults: document.getElementById('private-participant-results'),
        privateError: document.getElementById('private-room-error'),
      };

      function getXsrfToken() {
        const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
        return match ? decodeURIComponent(match[1]) : null;
      }

      function ensureCsrfInBody(options, csrfToken) {
        const method = (options.method ?? 'GET').toUpperCase();
        if (!['POST', 'PUT', 'PATCH', 'DELETE'].includes(method)) {
          return options;
        }

        const headers = options.headers ?? {};
        const contentType = Object.keys(headers).find((key) => key.toLowerCase() === 'content-type');

        if (contentType && headers[contentType].includes('application/json') && typeof options.body === 'string') {
          try {
            const payload = JSON.parse(options.body);
            if (payload && typeof payload === 'object') {
              payload._token = csrfToken;
              options.body = JSON.stringify(payload);
            }
          } catch (e) {}
        } else if (options.body instanceof URLSearchParams) {
          options.body.set('_token', csrfToken);
        } else if (options.body instanceof FormData) {
          if (!options.body.has('_token')) {
            options.body.append('_token', csrfToken);
          }
        } else if (!contentType && typeof options.body === 'undefined') {
          options.headers = {
            ...(options.headers ?? {}),
            'Content-Type': 'application/json',
          };
          options.body = JSON.stringify({ _token: csrfToken });
        }

        return options;
      }

      async function fetchJson(url, options = {}) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
        const xsrfToken = getXsrfToken();
        options = ensureCsrfInBody(options, csrfToken);

        const response = await fetch(url, {
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken,
            ...(xsrfToken ? { 'X-XSRF-TOKEN': xsrfToken } : {}),
            ...(options.headers ?? {}),
          },
          credentials: 'same-origin',
          ...options,
        });

        if (response.status === 419) {
          window.location.reload();
          return Promise.reject({ message: '{{ __('Сессия истекла, перезagружаем страницу...') }}' });
        }

        if (!response.ok) {
          const error = await response.json().catch(() => ({}));
          throw error;
        }

        return response.json();
      }

      function sanitize(text) {
        const div = document.createElement('div');
        div.innerText = text ?? '';
        return div.innerHTML;
      }

      function getSearchQuery() {
        return (dom.searchInput.value || '').trim().toLowerCase();
      }

      function renderPublicRooms() {
        const query = getSearchQuery();
        dom.publicList.querySelectorAll('li.chat-room-item').forEach(item => item.remove());
        dom.publicEmpty.classList.add('d-none');

        const publicRooms = state.rooms.filter(room => room.type === 'public');
        const filtered = publicRooms.filter(room => {
          if (!query) {
            return true;
          }

          const participantNames = room.participants.map(participant => participant.name.toLowerCase());

          return room.name.toLowerCase().includes(query)
            || participantNames.some(name => name.includes(query));
        });

        if (!filtered.length) {
          dom.publicEmpty.classList.remove('d-none');
          return;
        }

        filtered.forEach(room => {
          const listItem = document.createElement('li');
          listItem.className = 'chat-contact-list-item chat-room-item px-4 py-3 cursor-pointer';
          listItem.dataset.roomId = room.id;
          listItem.classList.toggle('active', state.currentRoomId === room.id);

          listItem.innerHTML = `
            <div class="d-flex align-items-center">
              <div class="avatar avatar-sm avatar-online flex-shrink-0">
                <span class="avatar-initial rounded-circle bg-label-primary">
                  ${room.name.slice(0, 1).toUpperCase()}
                </span>
              </div>
              <div class="chat-contact-info flex-grow-1 ms-3">
                <div class="d-flex justify-content-between align-items-center">
                  <h6 class="m-0 fw-normal text-truncate">${room.name}</h6>
                  <small class="chat-contact-list-item-time">${room.last_message?.created_at_human ?? ''}</small>
                </div>
                <small class="chat-contact-status text-truncate">
                  ${room.last_message ? sanitize(room.last_message.body) : '{{ __('Нет сообщений') }}'}
                </small>
              </div>
            </div>
          `;

          listItem.addEventListener('click', () => openRoom(room.id));
          dom.publicList.appendChild(listItem);
        });
      }

      function renderPrivateUsers() {
        const query = getSearchQuery();
        dom.privateList.querySelectorAll('li.private-user-item').forEach(item => item.remove());
        dom.privateEmpty.classList.add('d-none');

        const filtered = state.privateUsers.filter(user => {
          if (!query) {
            return true;
          }

          return `${user.name} ${user.email ?? ''}`.toLowerCase().includes(query);
        });

        if (!filtered.length) {
          dom.privateEmpty.classList.remove('d-none');
          return;
        }

        filtered.forEach(user => {
          const room = user.room;
          const lastMessagePreview = room?.last_message
            ? sanitize(room.last_message.body)
            : '{{ __('Нажмите, чтобы начать диалог') }}';
          const lastMessageTime = room?.last_message?.created_at_human ?? '';

          const listItem = document.createElement('li');
          listItem.className = 'chat-contact-list-item private-user-item px-4 py-3 cursor-pointer';
          listItem.dataset.userId = user.id;

          if (state.currentContactId === user.id || (room && state.currentRoomId === room.id)) {
            listItem.classList.add('active');
          }

          if (state.creatingRoomForUserId === user.id) {
            listItem.classList.add('opacity-75');
          }

          listItem.innerHTML = `
            <div class="d-flex align-items-center">
              <div class="avatar avatar-sm flex-shrink-0">
                <img src="${user.avatar_url}" alt="${user.name}" class="rounded-circle">
              </div>
              <div class="chat-contact-info flex-grow-1 ms-3">
                <div class="d-flex justify-content-between align-items-center">
                  <h6 class="m-0 fw-normal text-truncate">${user.name}</h6>
                  <small class="chat-contact-list-item-time">${lastMessageTime}</small>
                </div>
                <small class="chat-contact-status text-truncate">
                  ${lastMessagePreview}
                </small>
              </div>
            </div>
          `;

          listItem.addEventListener('click', () => handlePrivateUserClick(user));
          dom.privateList.appendChild(listItem);
        });
      }

      function highlightActiveUser() {
        document.querySelectorAll('#private-room-list li.private-user-item').forEach(item => {
          const userId = Number(item.dataset.userId);
          const room = state.privateUsers.find(user => user.id === userId)?.room;
          const isActive = (room && state.currentRoomId === room.id) || state.currentContactId === userId;
          item.classList.toggle('active', Boolean(isActive));
        });
      }

      function showConversation() {
        dom.emptyState.classList.add('d-none');
        dom.historyWrapper.classList.remove('d-none');
      }

      async function loadRoomsAndUsers() {
        try {
          const response = await fetchJson(routes.rooms);
          state.rooms = response.data ?? [];
          state.privateUsers = response.private_users ?? [];

          if (state.currentRoomId) {
            const currentRoom = state.rooms.find(room => room.id === state.currentRoomId);
            if (currentRoom && currentRoom.type === 'private') {
              const otherParticipant = currentRoom.participants.find(participant => !participant.is_self);
              state.currentContactId = otherParticipant ? otherParticipant.id : state.currentContactId;
            }
          }

          renderPublicRooms();
          renderPrivateUsers();
          highlightActiveUser();
        } catch (error) {
          console.error('Failed to load chat data', error);
        }
      }

      async function refreshAll() {
        await loadRoomsAndUsers();
      }

      function findRoom(roomId) {
        return state.rooms.find(room => room.id === roomId);
      }

      async function openRoom(roomId) {
        if (state.currentRoomId === roomId && dom.historyWrapper.classList.contains('d-none')) {
          showConversation();
        }

        state.currentRoomId = roomId;
        clearInterval(state.messagesInterval);

        document.querySelectorAll('#public-room-list li.chat-room-item').forEach(item => {
          const itemRoomId = Number(item.dataset.roomId);
          item.classList.toggle('active', itemRoomId === roomId);
        });

        let room = findRoom(roomId);
        if (!room) {
          await refreshAll();
          room = findRoom(roomId);
        }

        if (!room) {
          return;
        }

        showConversation();
        updateRoomHeader(room);
        syncContactFromRoom(room);

        await loadMessages(roomId);

        state.messagesInterval = setInterval(() => {
          loadMessages(roomId, { silent: true });
        }, 15000);
      }

      function updateRoomHeader(room) {
        dom.roomName.textContent = room.name;
        const participants = room.participants.filter(participant => !participant.is_self).map(participant => participant.name);

        dom.roomMeta.textContent = room.type === 'public'
          ? '{{ __('Публичный чат') }}'
          : (participants.join(', ') || '{{ __('Приватный чат') }}');

        const initial = room.name.slice(0, 1).toUpperCase();
        dom.roomAvatar.innerHTML = `
          <span class="avatar-initial rounded-circle bg-label-${room.type === 'public' ? 'primary' : 'warning'}">${initial}</span>
        `;
      }

      function syncContactFromRoom(room) {
        if (room.type === 'private') {
          const otherParticipant = room.participants.find(participant => !participant.is_self);
          state.currentContactId = otherParticipant ? otherParticipant.id : null;
        } else {
          state.currentContactId = null;
        }

        highlightActiveUser();
      }

      async function loadMessages(roomId, options = {}) {
        const silent = options.silent === true;
        dom.loadingIndicator.classList.toggle('d-none', silent);

        try {
          const response = await fetchJson(routes.roomMessages(roomId));
          renderMessages(response.data ?? []);
        } catch (error) {
          console.error('Failed to load messages', error);
        } finally {
          dom.loadingIndicator.classList.add('d-none');
        }
      }

      function renderMessages(messages) {
        dom.messageList.innerHTML = '';

        messages.forEach(message => {
          const item = document.createElement('li');
          item.className = `chat-message ${message.is_mine ? 'chat-message-right' : ''}`;
          item.innerHTML = `
            <div class="d-flex overflow-hidden">
              ${!message.is_mine ? `
                <div class="user-avatar flex-shrink-0 me-3">
                  <div class="avatar avatar-sm">
                    <img src="${message.user?.avatar_url ?? ''}" alt="${message.user?.name ?? ''}" class="rounded-circle">
                  </div>
                </div>
              ` : ''}
              <div class="chat-message-wrapper flex-grow-1">
                <div class="chat-message-text">
                  <p class="mb-0">${sanitize(message.body)}</p>
                </div>
                <div class="${message.is_mine ? 'text-end' : ''} text-body-secondary mt-1">
                  <small>${message.created_at_human ?? ''}</small>
                </div>
              </div>
              ${message.is_mine ? `
                <div class="user-avatar flex-shrink-0 ms-3">
                  <div class="avatar avatar-sm">
                    <img src="${message.user?.avatar_url ?? ''}" alt="${message.user?.name ?? ''}" class="rounded-circle">
                  </div>
                </div>
              ` : ''}
            </div>
          `;

          dom.messageList.appendChild(item);
        });

        dom.messageList.parentElement.scrollTop = dom.messageList.parentElement.scrollHeight;
      }

      async function handlePrivateUserClick(user) {
        state.currentContactId = user.id;
        highlightActiveUser();

        if (user.room) {
          await openRoom(user.room.id);
          return;
        }

        if (state.creatingRoomForUserId) {
          return;
        }

        state.creatingRoomForUserId = user.id;
        renderPrivateUsers();

        try {
          const response = await fetchJson(routes.createRoom, {
            method: 'POST',
            body: JSON.stringify({ type: 'private', participant_id: user.id }),
            headers: {
              'Content-Type': 'application/json',
            },
          });

          await loadRoomsAndUsers();
          await openRoom(response.data.id);
        } catch (error) {
          const message = Array.isArray(error?.errors?.participant_id)
            ? error.errors.participant_id[0]
            : (error?.message ?? '{{ __('Не удалось создать приватный чат') }}');
          window.alert(message);
        } finally {
          state.creatingRoomForUserId = null;
          renderPrivateUsers();
        }
      }

      async function handleMessageSubmit(event) {
        event.preventDefault();

        if (!state.currentRoomId) {
          return;
        }

        const message = dom.messageInput.value.trim();
        if (!message) {
          return;
        }

        dom.messageForm.classList.add('opacity-75');
        dom.messageError.classList.add('d-none');

        try {
          await fetchJson(routes.sendMessage(state.currentRoomId), {
            method: 'POST',
            body: JSON.stringify({ message }),
            headers: {
              'Content-Type': 'application/json',
            },
          });

          dom.messageInput.value = '';
          await loadMessages(state.currentRoomId, { silent: true });
          await refreshAll();
        } catch (error) {
          const messageText = Array.isArray(error?.errors?.message)
            ? error.errors.message[0]
            : (error?.message ?? '{{ __('Не удалось отправить сообщение') }}');
          dom.messageError.textContent = messageText;
          dom.messageError.classList.remove('d-none');
        } finally {
          dom.messageForm.classList.remove('opacity-75');
          dom.messageInput.focus();
        }
      }

      async function handlePublicRoomSubmit(event) {
        event.preventDefault();

        const name = dom.publicRoomName.value.trim();
        if (!name) {
          dom.publicRoomError.textContent = '{{ __('Введите название чата') }}';
          dom.publicRoomError.classList.add('d-block');
          return;
        }

        dom.publicRoomError.classList.remove('d-block');

        try {
          const response = await fetchJson(routes.createRoom, {
            method: 'POST',
            body: JSON.stringify({ type: 'public', name }),
            headers: {
              'Content-Type': 'application/json',
            },
          });

          const publicModalEl = document.getElementById('createPublicRoomModal');
          const publicModal = window.bootstrap ? bootstrap.Modal.getInstance(publicModalEl) : null;
          if (publicModal) {
            publicModal.hide();
          }

          dom.publicRoomName.value = '';
          await refreshAll();
          await openRoom(response.data.id);
        } catch (error) {
          const message = Array.isArray(error?.errors?.name)
            ? error.errors.name[0]
            : (error?.message ?? '{{ __('Не удалось создать чат') }}');
          dom.publicRoomError.textContent = message;
          dom.publicRoomError.classList.add('d-block');
        }
      }

      async function handlePrivateRoomSubmit(event) {
        event.preventDefault();

        dom.privateError.classList.add('d-none');
        const participantId = dom.privateUserId.value;

        if (!participantId) {
          dom.privateError.textContent = '{{ __('Выберите пользователя для приватного чата') }}';
          dom.privateError.classList.remove('d-none');
          return;
        }

        try {
          const room = await createPrivateRoom(Number(participantId));

          const privateModalEl = document.getElementById('createPrivateRoomModal');
          const privateModal = window.bootstrap ? bootstrap.Modal.getInstance(privateModalEl) : null;
          if (privateModal) {
            privateModal.hide();
          }

          dom.privateSearch.value = '';
          dom.privateUserId.value = '';
          dom.privateResults.innerHTML = '';
          dom.privateResults.classList.add('d-none');

          await refreshAll();
          await openRoom(room.id);
        } catch (error) {
          const message = Array.isArray(error?.errors?.participant_id)
            ? error.errors.participant_id[0]
            : (error?.message ?? '{{ __('Не удалось создать чат') }}');
          dom.privateError.textContent = message;
          dom.privateError.classList.remove('d-none');
        }
      }

      async function searchUsers(query) {
        if (!query || query.length < 2) {
          dom.privateResults.classList.add('d-none');
          dom.privateResults.innerHTML = '';
          return;
        }

        try {
          const response = await fetchJson(`${routes.searchUsers}?q=${encodeURIComponent(query)}`);
          const users = response.data ?? [];

          dom.privateResults.innerHTML = '';

          if (!users.length) {
            dom.privateResults.classList.remove('d-none');
            dom.privateResults.innerHTML = `
              <div class="list-group-item text-body-secondary">{{ __('Пользователи не найдены') }}</div>
            `;
            return;
          }

          users.forEach(user => {
            const item = document.createElement('button');
            item.type = 'button';
            item.className = 'list-group-item list-group-item-action d-flex align-items-center';
            item.innerHTML = `
              <div class="avatar avatar-sm me-2 flex-shrink-0">
                <img src="${user.avatar_url}" class="rounded-circle" alt="${user.name}">
              </div>
              <div class="text-start">
                <div>${user.name}</div>
                <small class="text-body-secondary">${user.email ?? ''}</small>
              </div>
            `;

            item.addEventListener('click', () => {
              dom.privateSearch.value = `${user.name}${user.email ? ' (' + user.email + ')' : ''}`;
              dom.privateUserId.value = user.id;
              dom.privateResults.classList.add('d-none');
              dom.privateResults.innerHTML = '';
            });

            dom.privateResults.appendChild(item);
          });

          dom.privateResults.classList.remove('d-none');
        } catch (error) {
          console.error('Failed to search users', error);
        }
      }

      dom.messageForm.addEventListener('submit', handleMessageSubmit);
      dom.refreshBtn.addEventListener('click', () => {
        if (state.currentRoomId) {
          loadMessages(state.currentRoomId);
        }
        refreshAll().catch(console.error);
      });
      dom.publicRoomForm.addEventListener('submit', handlePublicRoomSubmit);
      dom.privateRoomForm.addEventListener('submit', handlePrivateRoomSubmit);
      dom.searchInput.addEventListener('input', () => {
        renderPublicRooms();
        renderPrivateUsers();
        highlightActiveUser();
      });
      dom.privateSearch.addEventListener('input', (event) => searchUsers(event.target.value));

      document.getElementById('createPrivateRoomModal').addEventListener('hidden.bs.modal', () => {
        dom.privateSearch.value = '';
        dom.privateUserId.value = '';
        dom.privateResults.innerHTML = '';
        dom.privateResults.classList.add('d-none');
        dom.privateError.classList.add('d-none');
      });

      renderPublicRooms();
      renderPrivateUsers();
      highlightActiveUser();

      refreshAll().catch(console.error);
      state.refreshInterval = setInterval(() => {
        refreshAll().catch(console.error);
      }, 20000);
    })();
  </script>
@endpush

