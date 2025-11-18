@extends('layouts.admin')

@section('title', __('Чат'))

@push('styles')
  <link rel="stylesheet" href="{{ asset('vendor/vuexy/vendor/css/pages/app-chat.css') }}">
  <style>
    .chat-message-unread .chat-message-text {
      background-color: rgba(255, 193, 7, 0.1) !important;
      border: 1px solid rgba(255, 193, 7, 0.3) !important;
    }

    .message-hover-container {
      position: relative;
    }

    .message-time-tooltip {
      position: absolute;
      bottom: -20px;
      left: 0;
      background-color: rgba(0, 0, 0, 0.8);
      color: white;
      padding: 2px 6px;
      border-radius: 4px;
      font-size: 11px;
      white-space: nowrap;
      opacity: 0 !important;
      visibility: hidden !important;
      pointer-events: none;
      transition: opacity 0.2s, visibility 0.2s;
      z-index: 10;
    }

    .chat-message-right .message-time-tooltip {
      left: auto;
      right: 0;
    }

    .message-hover-container:hover .message-time-tooltip {
      opacity: 1 !important;
      visibility: visible !important;
    }

    /* Force hide all message timestamps by default */
    .chat-message .text-body-secondary small,
    .chat-message small.text-body-secondary {
      display: none !important;
    }

    /* Only show timestamps in tooltips on hover */
    .message-hover-container:hover .message-time-tooltip small {
      display: block !important;
    }
  </style>
@endpush

@section('content')
  @php
    /** @var \App\Models\User $currentUser */
    $currentUser = auth()->user();
  @endphp
  <div class="app-chat card overflow-hidden">
    <div class="row g-0">
      <!-- Sidebar Left -->
      <div class="col app-chat-sidebar-left app-sidebar overflow-hidden" id="app-chat-sidebar-left">
        <div
          class="chat-sidebar-left-user sidebar-header d-flex flex-column justify-content-center align-items-center flex-wrap px-6 pt-12">
          <div class="avatar avatar-xl  chat-sidebar-avatar">
            <img src="{{ $currentUser->avatar_url }}" alt="Avatar" class="rounded-circle" />
            </div>
          <h5 class="mt-4 mb-0">{{ $currentUser->name }}</h5>
          <span>{{ __('Администратор') }}</span>
          <i
            class="icon-base ti tabler-x icon-lg cursor-pointer close-sidebar"
            data-bs-toggle="sidebar"
            data-overlay
            data-target="#app-chat-sidebar-left"></i>
            </div>
        <div class="sidebar-body px-6 pb-6">
          <div class="my-6">
            <div class="maxLength-wrapper">
              <label for="chat-sidebar-left-user-about" class="text-uppercase text-body-secondary mb-1"
                >{{ __('О себе') }}</label
              >
              <textarea
                id="chat-sidebar-left-user-about"
                class="form-control chat-sidebar-left-user-about maxLength-example"
                rows="3"
                maxlength="120">
{{ __('Привет! Я администратор системы чатов.') }}</textarea
              >
              <small id="textarea-maxlength-info"></small>
          </div>
          </div>
          <div class="my-6">
            <p class="text-uppercase text-body-secondary mb-1">{{ __('Статус') }}</p>
            <div class="d-grid gap-2 pt-2 text-heading ms-2">
              <div class="form-check form-check-success">
                <input
                  name="chat-user-status"
                  class="form-check-input"
                  type="radio"
                  value="active"
                  id="user-active"
                  checked />
                <label class="form-check-label" for="user-active">{{ __('Онлайн') }}</label>
        </div>
              <div class="form-check form-check-warning">
                <input
                  name="chat-user-status"
                  class="form-check-input"
                  type="radio"
                  value="away"
                  id="user-away" />
                <label class="form-check-label" for="user-away">{{ __('Отошел') }}</label>
          </div>
              <div class="form-check form-check-danger">
                <input
                  name="chat-user-status"
                  class="form-check-input"
                  type="radio"
                  value="busy"
                  id="user-busy" />
                <label class="form-check-label" for="user-busy">{{ __('Не беспокоить') }}</label>
        </div>
              <div class="form-check form-check-secondary">
                <input
                  name="chat-user-status"
                  class="form-check-input"
                  type="radio"
                  value="offline"
                  id="user-offline" />
                <label class="form-check-label" for="user-offline">{{ __('Оффлайн') }}</label>
              </div>
            </div>
          </div>
          <div class="my-6">
            <p class="text-uppercase text-body-secondary mb-1">{{ __('Настройки') }}</p>
            <ul class="list-unstyled d-grid gap-4 ms-2 pt-2 text-heading">
              <li class="d-flex justify-content-between align-items-center">
                <div>
                  <i class="icon-base ti tabler-lock icon-md me-1"></i>
                  <span class="align-middle">{{ __('Двухфакторная аутентификация') }}</span>
                </div>
                <div class="form-check form-switch mb-0 me-1">
                  <input type="checkbox" class="form-check-input" checked />
                </div>
            </li>
              <li class="d-flex justify-content-between align-items-center">
                <div>
                  <i class="icon-base ti tabler-bell icon-md me-1"></i>
                  <span class="align-middle">{{ __('Уведомления') }}</span>
                </div>
                <div class="form-check form-switch mb-0 me-1">
                  <input type="checkbox" class="form-check-input" />
                </div>
              </li>
              <li>
                <i class="icon-base ti tabler-user-plus icon-md me-1"></i>
                <span class="align-middle">{{ __('Пригласить друзей') }}</span>
              </li>
              <li>
                <i class="icon-base ti tabler-trash icon-md me-1"></i>
                <span class="align-middle">{{ __('Удалить аккаунт') }}</span>
            </li>
          </ul>
          </div>
          <div class="d-flex mt-6">
            <button
              class="btn btn-primary w-100"
              data-bs-toggle="sidebar"
              data-overlay
              data-target="#app-chat-sidebar-left">
              {{ __('Выйти') }}<i class="icon-base ti tabler-logout icon-16px ms-2"></i>
            </button>
          </div>
        </div>
      </div>
      <!-- /Sidebar Left-->

      <!-- Chat & Contacts -->
      <div
        class="col app-chat-contacts app-sidebar flex-grow-0 overflow-hidden border-end"
        id="app-chat-contacts">
        <div class="sidebar-header h-px-75 px-5 border-bottom d-flex align-items-center">
          <div class="d-flex align-items-center me-6 me-lg-0">
            <div
              class="flex-shrink-0 avatar  me-4"
              data-bs-toggle="sidebar"
              data-overlay="app-overlay-ex"
              data-target="#app-chat-sidebar-left">
              <img
                class="user-avatar rounded-circle cursor-pointer"
                src="{{ $currentUser->avatar_url }}"
                alt="Avatar" />
            </div>
            <div class="flex-grow-1 input-group input-group-merge">
              <span class="input-group-text" id="basic-addon-search31"
                ><i class="icon-base ti tabler-search icon-xs"></i
              ></span>
              <input
                type="text"
                class="form-control chat-search-input"
                placeholder="{{ __('Поиск...') }}"
                aria-label="{{ __('Поиск...') }}"
                aria-describedby="basic-addon-search31" />
            </div>
          </div>
          <i
            class="icon-base ti tabler-x icon-lg cursor-pointer position-absolute top-50 end-0 translate-middle d-lg-none d-block"
            data-overlay
            data-bs-toggle="sidebar"
            data-target="#app-chat-contacts"></i>
        </div>
        <div class="sidebar-body">
          <!-- Chats -->
          <ul class="list-unstyled chat-contact-list py-2 mb-0" id="chat-list">
            <li class="chat-contact-list-item chat-contact-list-item-title mt-0">
              <h5 class="text-primary mb-0">{{ __('Чаты') }}</h5>
            </li>
            <li class="chat-contact-list-item chat-list-item-0 d-none">
              <h6 class="text-body-secondary mb-0">{{ __('Чатов не найдено') }}</h6>
            </li>
          </ul>
          <!-- Contacts -->
          <ul class="list-unstyled chat-contact-list mb-0 py-2" id="contact-list">
            <li class="chat-contact-list-item chat-contact-list-item-title mt-0">
              <h5 class="text-primary mb-0">{{ __('Контакты') }}</h5>
            </li>
            <li class="chat-contact-list-item contact-list-item-0 d-none">
              <h6 class="text-body-secondary mb-0">{{ __('Контактов не найдено') }}</h6>
            </li>
          </ul>
        </div>
      </div>
      <!-- /Chat contacts -->

      <!-- Chat conversation -->
      <div
        class="col app-chat-conversation d-flex align-items-center justify-content-center flex-column"
        id="app-chat-conversation">
        <div class="bg-label-primary p-8 rounded-circle">
          <i class="icon-base ti tabler-message-2 icon-50px"></i>
          </div>
        <p class="my-4">{{ __('Выберите контакт для начала разговора') }}</p>
        <button class="btn btn-primary app-chat-conversation-btn" id="app-chat-conversation-btn">
          {{ __('Выбрать контакт') }}
            </button>
          </div>
      <!-- /Chat conversation -->

      <!-- Chat History -->
      <div class="col app-chat-history d-none" id="app-chat-history">
        <div class="chat-history-wrapper">
          <div class="chat-history-header border-bottom">
            <div class="d-flex justify-content-between align-items-center">
              <div class="d-flex overflow-hidden align-items-center">
                <i
                  class="icon-base ti tabler-menu-2 icon-lg cursor-pointer d-lg-none d-block me-4"
                  data-bs-toggle="sidebar"
                  data-overlay
                  data-target="#app-chat-contacts"></i>
                <div class="flex-shrink-0 avatar ">
                  <img
                    src="{{ asset('vendor/vuexy/assets/img/avatars/4.png') }}"
                    alt="Avatar"
                    class="rounded-circle"
                    data-bs-toggle="sidebar"
                    data-overlay
                    data-target="#app-chat-sidebar-right" />
              </div>
                <div class="chat-contact-info flex-grow-1 ms-4">
                  <h6 class="m-0 fw-normal">{{ __('Загрузка...') }}</h6>
                  <small class="user-status text-body">{{ __('Статус') }}</small>
              </div>
            </div>
              <div class="d-flex align-items-center">
                <button
                  class="btn btn-text-secondary cursor-pointer d-sm-inline-flex d-none me-1 btn-icon rounded-pill"
                  id="manual-refresh-btn"
                  title="{{ __('Обновить сообщения') }}">
                  <i class="icon-base ti tabler-refresh icon-22px"></i>
              </button>
                <span
                  class="btn btn-text-secondary cursor-pointer d-sm-inline-flex d-none me-1 btn-icon rounded-pill">
                  <i class="icon-base ti tabler-phone icon-22px"></i>
                </span>
                <span
                  class="btn btn-text-secondary cursor-pointer d-sm-inline-flex d-none me-1 btn-icon rounded-pill">
                  <i class="icon-base ti tabler-video icon-22px"></i>
                </span>
                <span
                  class="btn btn-text-secondary cursor-pointer d-sm-inline-flex d-none me-1 btn-icon rounded-pill">
                  <i class="icon-base ti tabler-search icon-22px"></i>
                </span>
                <div class="dropdown">
                  <button
                    class="btn btn-icon btn-text-secondary text-secondary rounded-pill dropdown-toggle hide-arrow"
                    data-bs-toggle="dropdown"
                    aria-expanded="true"
                    id="chat-header-actions">
                    <i class="icon-base ti tabler-dots-vertical icon-22px"></i>
                  </button>
                  <div class="dropdown-menu dropdown-menu-end" aria-labelledby="chat-header-actions">
                    <div id="room-management-menu" style="display: none;">
                      <a class="dropdown-item" href="javascript:void(0);" id="rename-room-btn">{{ __('Переименовать чат') }}</a>
                      <a class="dropdown-item" href="javascript:void(0);" id="clear-room-btn">{{ __('Очистить все сообщения') }}</a>
                      <a class="dropdown-item text-danger" href="javascript:void(0);" id="delete-room-btn">{{ __('Удалить чат') }}</a>
                      <div class="dropdown-divider"></div>
                    </div>
                    <a class="dropdown-item" href="javascript:void(0);">{{ __('Посмотреть контакт') }}</a>
                    <a class="dropdown-item" href="javascript:void(0);">{{ __('Отключить уведомления') }}</a>
                    <a class="dropdown-item" href="javascript:void(0);">{{ __('Заблокировать контакт') }}</a>
                    <a class="dropdown-item" href="javascript:void(0);">{{ __('Пожаловаться') }}</a>
            </div>
          </div>
            </div>
          </div>
          </div>
          <div class="chat-history-body">
            <ul class="list-unstyled chat-history" id="chat-message-list"></ul>
          </div>
          <!-- Chat message form -->
          <div class="chat-history-footer shadow-xs">
            <form class="form-send-message d-flex justify-content-between align-items-center" id="chat-message-form">
              @csrf
              <input
                class="form-control message-input border-0 me-4 shadow-none"
                placeholder="{{ __('Введите сообщение...') }}" />
              <div class="message-actions d-flex align-items-center">
                <span class="btn btn-text-secondary btn-icon rounded-pill cursor-pointer speech-to-text">
                  <i class="speech-to-text icon-base ti tabler-microphone icon-22px text-heading"></i>
                </span>
                <label for="attach-doc" class="form-label mb-0">
                  <span class="btn btn-text-secondary btn-icon rounded-pill cursor-pointer mx-1">
                    <i class="icon-base ti tabler-paperclip icon-22px text-heading"></i>
                  </span>
                  <input type="file" id="attach-doc" hidden />
                </label>
                <button class="btn btn-primary d-flex send-msg-btn">
                  <span class="align-middle d-md-inline-block d-none">{{ __('Отправить') }}</span>
                  <i class="icon-base ti tabler-send icon-16px ms-md-2 ms-0"></i>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
      <!-- /Chat History -->

      <!-- Sidebar Right -->
      <div class="col app-chat-sidebar-right app-sidebar overflow-hidden" id="app-chat-sidebar-right">
        <div
          class="sidebar-header d-flex flex-column justify-content-center align-items-center flex-wrap px-6 pt-12">
          <div class="avatar avatar-xl  chat-sidebar-avatar">
            <img src="{{ asset('vendor/vuexy/assets/img/avatars/4.png') }}" alt="Avatar" class="rounded-circle" />
    </div>
          <h5 class="mt-4 mb-0">{{ __('Загрузка...') }}</h5>
          <span>{{ __('Статус') }}</span>
          <i
            class="icon-base ti tabler-x icon-lg cursor-pointer close-sidebar d-block"
            data-bs-toggle="sidebar"
            data-overlay
            data-target="#app-chat-sidebar-right"></i>
        </div>
        <div class="sidebar-body p-6 pt-0">
          <div class="my-6">
            <p class="text-uppercase mb-1 text-body-secondary">{{ __('О себе') }}</p>
            <p class="mb-0">
              {{ __('Информация о пользователе загружается...') }}
            </p>
          </div>
          <div class="my-6">
            <p class="text-uppercase mb-1 text-body-secondary">{{ __('Личная информация') }}</p>
            <ul class="list-unstyled d-grid gap-4 mb-0 ms-2 py-2 text-heading">
              <li class="d-flex align-items-center">
                <i class="icon-base ti tabler-mail icon-md"></i>
                <span class="align-middle ms-2">{{ __('Email') }}</span>
              </li>
              <li class="d-flex align-items-center">
                <i class="icon-base ti tabler-phone-call icon-md"></i>
                <span class="align-middle ms-2">{{ __('Телефон') }}</span>
              </li>
              <li class="d-flex align-items-center">
                <i class="icon-base ti tabler-clock icon-md"></i>
                <span class="align-middle ms-2">{{ __('Рабочее время') }}</span>
              </li>
            </ul>
          </div>
          <div class="my-6">
            <p class="text-uppercase text-body-secondary mb-1">{{ __('Опции') }}</p>
            <ul class="list-unstyled d-grid gap-4 ms-2 py-2 text-heading">
              <li class="cursor-pointer d-flex align-items-center">
                <i class="icon-base ti tabler-bookmark icon-md"></i>
                <span class="align-middle ms-2">{{ __('Добавить метку') }}</span>
              </li>
              <li class="cursor-pointer d-flex align-items-center">
                <i class="icon-base ti tabler-star icon-md"></i>
                <span class="align-middle ms-2">{{ __('Важный контакт') }}</span>
              </li>
              <li class="cursor-pointer d-flex align-items-center">
                <i class="icon-base ti tabler-photo icon-md"></i>
                <span class="align-middle ms-2">{{ __('Общие медиа') }}</span>
              </li>
              <li class="cursor-pointer d-flex align-items-center">
                <i class="icon-base ti tabler-ban icon-md"></i>
                <span class="align-middle ms-2">{{ __('Заблокировать контакт') }}</span>
              </li>
            </ul>
          </div>
          <div class="d-flex mt-6">
            <button
              class="btn btn-danger w-100"
              data-bs-toggle="sidebar"
              data-overlay
              data-target="#app-chat-sidebar-right">
              {{ __('Удалить контакт') }}<i class="icon-base ti tabler-trash icon-16px ms-2"></i>
            </button>
          </div>
        </div>
      </div>
      <!-- /Sidebar Right -->

      <div class="app-overlay"></div>
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

  <!-- Переименование чата -->
  <div class="modal fade" id="renameRoomModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <form class="modal-content" id="rename-room-form">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">{{ __('Переименовать чат') }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Закрыть') }}"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="rename-room-name" class="form-label">{{ __('Новое название') }}</label>
            <input type="text" class="form-control" id="rename-room-name" name="name" maxlength="120" required>
            <div class="invalid-feedback" id="rename-room-error"></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-text-secondary" data-bs-dismiss="modal">{{ __('Отмена') }}</button>
          <button type="submit" class="btn btn-primary">
            <i class="icon-base ti tabler-edit me-1"></i>{{ __('Переименовать') }}
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Подтверждение очистки чата -->
  <div class="modal fade" id="clearRoomModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ __('Очистить чат') }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Закрыть') }}"></button>
        </div>
        <div class="modal-body">
          <p>{{ __('Вы уверены, что хотите удалить все сообщения из этого чата? Это действие нельзя отменить.') }}</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-text-secondary" data-bs-dismiss="modal">{{ __('Отмена') }}</button>
          <button type="button" class="btn btn-danger" id="confirm-clear-room">
            <i class="icon-base ti tabler-trash me-1"></i>{{ __('Очистить') }}
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Подтверждение удаления чата -->
  <div class="modal fade" id="deleteRoomModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ __('Удалить чат') }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Закрыть') }}"></button>
        </div>
        <div class="modal-body">
          <p>{{ __('Вы уверены, что хотите удалить этот чат? Все сообщения будут потеряны. Это действие нельзя отменить.') }}</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-text-secondary" data-bs-dismiss="modal">{{ __('Отмена') }}</button>
          <button type="button" class="btn btn-danger" id="confirm-delete-room">
            <i class="icon-base ti tabler-trash me-1"></i>{{ __('Удалить') }}
          </button>
        </div>
      </div>
    </div>
  </div>

@endsection

@push('scripts')
  <script>
    /**
     * App Chat - адаптированный для Laravel
     */
    'use strict';

    // Initialize with server data
    const initialRooms = @json($initialRooms ?? []);
    const initialPrivateUsers = @json($initialPrivateUsers ?? []);

    document.addEventListener('DOMContentLoaded', () => {
      // DOM Elements
      const elements = {
        chatContactsBody: document.querySelector('.app-chat-contacts .sidebar-body'),
        chatHistoryBody: document.querySelector('.chat-history-body'),
        chatSidebarLeftBody: document.querySelector('.app-chat-sidebar-left .sidebar-body'),
        chatSidebarRightBody: document.querySelector('.app-chat-sidebar-right .sidebar-body'),
        chatUserStatus: [...document.querySelectorAll("input[name='chat-user-status']")],
        chatSidebarLeftUserAbout: document.getElementById('chat-sidebar-left-user-about'),
        formSendMessage: document.querySelector('.form-send-message'),
        messageInput: document.querySelector('.message-input'),
        searchInput: document.querySelector('.chat-search-input'),
        chatContactListItems: [...document.querySelectorAll('.chat-contact-list-item:not(.chat-contact-list-item-title)')],
        textareaInfo: document.getElementById('textarea-maxlength-info'),
        conversationButton: document.getElementById('app-chat-conversation-btn'),
        chatHistoryHeader: document.querySelector(".chat-history-header [data-target='#app-chat-contacts']"),
        speechToText: $('.speech-to-text'),
        appChatConversation: document.getElementById('app-chat-conversation'),
        appChatHistory: document.getElementById('app-chat-history')
      };

      const userStatusClasses = {
        active: 'avatar-online',
        offline: 'avatar-offline',
        away: 'avatar-away',
        busy: 'avatar-busy'
      };

      // Laravel routes
      const routes = {
        rooms: @json(route('admin.chat.rooms.index')),
        createRoom: @json(route('admin.chat.rooms.store')),
        updateRoom: (roomId) => @json(route('admin.chat.rooms.update', ['room' => '__room__'])).replace('__room__', roomId),
        deleteRoom: (roomId) => @json(route('admin.chat.rooms.delete', ['room' => '__room__'])).replace('__room__', roomId),
        clearRoomMessages: (roomId) => @json(route('admin.chat.rooms.messages.clear', ['room' => '__room__'])).replace('__room__', roomId),
        roomMessages: (roomId) => @json(route('admin.chat.rooms.messages.index', ['room' => '__room__'])).replace('__room__', roomId),
        sendMessage: (roomId) => @json(route('admin.chat.rooms.messages.store', ['room' => '__room__'])).replace('__room__', roomId),
        markRoomRead: (roomId) => @json(route('admin.chat.rooms.read', ['room' => '__room__'])).replace('__room__', roomId),
        users: @json(route('admin.chat.users.index')),
      };

      const currentUserId = {{ (int) $currentUser->id }};
      let currentRoomId = null;
      let currentContactId = null;
      let currentRoom = null;
      let chatContacts = [];
      let chatRooms = [];
      let chatMessages = [];
      let messageRefreshInterval = null;
      let contactRefreshInterval = null;
      let lastMessageTimestamp = null;
      let lastReadAt = null;

      /**
       * Initialize PerfectScrollbar on provided elements.
       * @param {HTMLElement[]} elements - List of elements to initialize.
       */
      const initPerfectScrollbar = elements => {
        elements.forEach(el => {
          if (el) {
            new PerfectScrollbar(el, {
              wheelPropagation: false,
              suppressScrollX: true
            });
          }
        });
      };

      /**
       * Scroll chat history to the bottom.
       */
      const scrollToBottom = () => elements.chatHistoryBody?.scrollTo(0, elements.chatHistoryBody.scrollHeight);

      /**
       * Update user status avatar classes.
       * @param {string} status - Status key from userStatusClasses.
       */
      const updateUserStatus = status => {
        const leftSidebarAvatar = document.querySelector('.chat-sidebar-left-user .avatar');
        const contactsAvatar = document.querySelector('.app-chat-contacts .avatar');

        [leftSidebarAvatar, contactsAvatar].forEach(avatar => {
          if (avatar) avatar.className = avatar.className.replace(/avatar-\w+/, userStatusClasses[status]);
        });
      };

      // Handle textarea max length count.
      function handleMaxLengthCount(inputElement, infoElement, maxLength) {
        const currentLength = inputElement.value.length;
        const remaining = maxLength - currentLength;

        infoElement.className = 'maxLength label-success';

        if (remaining >= 0) {
          infoElement.textContent = `${currentLength}/${maxLength}`;
        }
        if (remaining <= 0) {
          infoElement.textContent = `${currentLength}/${maxLength}`;
          infoElement.classList.remove('label-success');
          infoElement.classList.add('label-danger');
        }
      }

      /**
       * Switch to chat conversation view.
       */
      const switchToChatConversation = () => {
        elements.appChatConversation.classList.replace('d-flex', 'd-none');
        elements.appChatHistory.classList.replace('d-none', 'd-block');
      };

      /**
       * Filter chat contacts by search input.
       * @param {string} selector - CSS selector for chat/contact list items.
       * @param {string} searchValue - Search input value.
       * @param {string} placeholderSelector - Selector for placeholder element.
       */
      const filterChatContacts = (selector, searchValue, placeholderSelector) => {
        const items = document.querySelectorAll(`${selector}:not(.chat-contact-list-item-title)`);
        let visibleCount = 0;

        items.forEach(item => {
          const isVisible = item.textContent.toLowerCase().includes(searchValue);
          item.classList.toggle('d-flex', isVisible);
          item.classList.toggle('d-none', !isVisible);
          if (isVisible) visibleCount++;
        });

        document.querySelector(placeholderSelector)?.classList.toggle('d-none', visibleCount > 0);
      };

      /**
       * Initialize speech-to-text functionality.
       */
      const initSpeechToText = () => {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (!SpeechRecognition || elements.speechToText.length === 0) return;

        const recognition = new SpeechRecognition();
        let listening = false;

        elements.speechToText.on('click', function () {
          if (!listening) recognition.start();
          recognition.onspeechstart = () => (listening = true);
          recognition.onresult = event => {
            $(this).closest('.form-send-message').find('.message-input').val(event.results[0][0].transcript);
          };
          recognition.onspeechend = () => (listening = false);
          recognition.onerror = () => (listening = false);
        });
      };

      /**
       * Find or create private room between current user and contact.
       * @param {number} contactId - Contact ID.
       * @returns {Promise<Object|null>} Room object or null.
       */
      const findOrCreatePrivateRoom = async (contactId) => {
        try {
          const response = await fetch(routes.createRoom, {
            method: 'POST',
          headers: {
            'Accept': 'application/json',
              'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? ''
          },
          credentials: 'same-origin',
            body: JSON.stringify({
              type: 'private',
              participant_id: contactId
            })
          });

          if (response.ok) {
            const result = await response.json();
            return result.data;
          } else {
            console.error('Failed to create/find private room:', response.status, response.statusText);
            return null;
          }
        } catch (error) {
          console.error('Error creating private room', error);
          return null;
        }
      };

      /**
       * Show indicator for new messages.
       * @param {number} count - Number of new messages.
       */
      const showNewMessageIndicator = (count) => {
        // Create or update notification
        let indicator = document.getElementById('new-message-indicator');
        if (!indicator) {
          indicator = document.createElement('div');
          indicator.id = 'new-message-indicator';
          indicator.className = 'alert alert-info alert-dismissible fade show position-fixed';
          indicator.style.cssText = 'top: 20px; right: 20px; z-index: 1050; min-width: 300px;';
          indicator.innerHTML = `
            <i class="icon-base ti tabler-bell me-2"></i>
            <span id="new-message-text"></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          `;
          document.body.appendChild(indicator);
        }

        const textElement = document.getElementById('new-message-text');
        if (textElement) {
          const messageText = count === 1 
            ? '{{ __("Получено новое сообщение!") }}'
            : `{{ __('Получено :count новых сообщений!', ['count' => '']) }}`.replace(':count', count);
          textElement.textContent = messageText;
        }

        // Auto-hide after 3 seconds
        setTimeout(() => {
          if (indicator && indicator.parentNode) {
            indicator.remove();
          }
        }, 3000);
      };

      /**
       * Start auto-refreshing messages for current room.
       */
      const startMessageAutoRefresh = () => {
        stopMessageAutoRefresh(); // Stop any existing interval

        if (currentRoomId) {
          console.log('🚀 STARTING MESSAGE AUTO-REFRESH for room:', currentRoomId);
          messageRefreshInterval = setInterval(async () => {
            console.log('🔄 AUTO-REFRESH INTERVAL TRIGGERED at', new Date().toLocaleTimeString());
            try {
              await refreshMessages();
            } catch (error) {
              console.error('❌ Error in auto-refresh:', error);
            }
          }, 3000); // Refresh every 3 seconds

          console.log('✅ Message auto-refresh started, interval ID:', messageRefreshInterval);
        } else {
          console.warn('❌ Cannot start auto-refresh: no currentRoomId, current value:', currentRoomId);
        }
      };

      /**
       * Stop auto-refreshing messages.
       */
      const stopMessageAutoRefresh = () => {
        if (messageRefreshInterval) {
          clearInterval(messageRefreshInterval);
          messageRefreshInterval = null;
        }
      };

      /**
       * Start auto-refreshing contacts list.
       */
      const startContactAutoRefresh = () => {
        stopContactAutoRefresh(); // Stop any existing interval

        contactRefreshInterval = setInterval(async () => {
          await refreshContacts();
        }, 10000); // Refresh every 10 seconds
      };

      /**
       * Stop auto-refreshing contacts.
       */
      const stopContactAutoRefresh = () => {
        if (contactRefreshInterval) {
          clearInterval(contactRefreshInterval);
          contactRefreshInterval = null;
        }
      };

      /**
       * Refresh contacts and rooms list.
       */
      const refreshContacts = async () => {
        try {
          console.log('Refreshing contacts and rooms...');

          // Загружаем контакты и чаты одновременно
          const [contactsResponse, roomsResponse] = await Promise.all([
            fetch(routes.users, {
              headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? ''
              },
              credentials: 'same-origin'
            }),
            fetch(routes.rooms, {
              headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? ''
              },
              credentials: 'same-origin'
            })
          ]);

          if (contactsResponse.ok) {
            const contactsData = await contactsResponse.json();
            const newContacts = contactsData.data || [];
            console.log('Received contacts:', newContacts.length);
            chatContacts = newContacts;
          } else {
            console.error('Failed to refresh contacts, status:', contactsResponse.status);
          }

          if (roomsResponse.ok) {
            const roomsData = await roomsResponse.json();
            const newRooms = roomsData.data || [];
            console.log('Received rooms:', newRooms.length);
            chatRooms = newRooms;
          } else {
            console.error('Failed to refresh rooms, status:', roomsResponse.status);
          }

          renderChatContacts();
          console.log('Contacts and rooms updated and re-rendered');
        } catch (error) {
          console.error('Error refreshing contacts and rooms:', error);
        }
      };

      /**
       * Mark current room as read (reset unread counter).
       */
      const markRoomAsRead = async (roomId) => {
        try {
          console.log('👁️ MARKING ROOM AS READ:', roomId);
          const response = await fetch(routes.markRoomRead(roomId), {
            method: 'POST',
            headers: {
              'Accept': 'application/json',
              'X-Requested-With': 'XMLHttpRequest',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? ''
            },
            credentials: 'same-origin'
          });

          if (response.ok) {
            console.log('✅ Room marked as read successfully');
            // Обновляем список контактов чтобы сбросить счетчик непрочитанных
            await refreshContacts();
          } else {
            console.error('❌ Failed to mark room as read:', response.status);
          }
        } catch (error) {
          console.error('❌ Error marking room as read:', error);
        }
      };

      /**
       * Refresh messages in current room, checking for new ones.
       */
      const refreshMessages = async () => {
        console.log('🔄 REFRESH MESSAGES CALLED - currentRoomId:', currentRoomId, 'time:', new Date().toLocaleTimeString());

        if (!currentRoomId) {
          console.warn('❌ No current room ID for refresh');
          return;
        }

        // Show loading indicator on refresh button
        const refreshBtn = document.getElementById('manual-refresh-btn');
        if (refreshBtn) {
          refreshBtn.classList.add('animate-spin');
        }

        try {
          console.log('🔄 Fetching messages from server for room:', currentRoomId);
          const url = routes.roomMessages(currentRoomId);
          console.log('🔄 Request URL:', url);

          const response = await fetch(url, {
            headers: {
              'Accept': 'application/json',
              'X-Requested-With': 'XMLHttpRequest',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? ''
            },
            credentials: 'same-origin'
          });

          console.log('Response status:', response.status);

          if (response.ok) {
            const data = await response.json();
            const allMessages = data.data || [];
            console.log('Received messages:', allMessages.length, 'current messages:', chatMessages.length);

            if (allMessages.length > chatMessages.length) {
              const newMessageCount = allMessages.length - chatMessages.length;
              console.log('Found', newMessageCount, 'new messages');

              // Replace all messages with fresh data from server
              chatMessages = allMessages;
              renderChatMessages();
              scrollToBottom();

              // Show new message indicator
              showNewMessageIndicator(newMessageCount);

              console.log('Messages updated, total:', chatMessages.length);
            } else {
              console.log('No new messages found');
            }
          } else {
            console.error('Failed to refresh messages, status:', response.status);
            const errorText = await response.text();
            console.error('Error response:', errorText);
          }
        } catch (error) {
          console.error('Error refreshing messages:', error);
        } finally {
          // Remove loading indicator
          if (refreshBtn) {
            refreshBtn.classList.remove('animate-spin');
          }
        }
      };

      /**
       * Load chat contacts and rooms from API.
       */
      const loadChatContacts = async () => {
        try {
          // Загружаем контакты и чаты одновременно
          const [contactsResponse, roomsResponse] = await Promise.all([
            fetch(routes.users, {
              headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? ''
              },
              credentials: 'same-origin'
            }),
            fetch(routes.rooms, {
              headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? ''
              },
              credentials: 'same-origin'
            })
          ]);

          if (contactsResponse.ok) {
            const contactsData = await contactsResponse.json();
            chatContacts = contactsData.data || [];
            console.log('Loaded contacts:', chatContacts);
          } else {
            console.error('Failed to load contacts:', contactsResponse.status, contactsResponse.statusText);
          }

          if (roomsResponse.ok) {
            const roomsData = await roomsResponse.json();
            chatRooms = roomsData.data || [];
            console.log('Loaded rooms:', chatRooms);
          } else {
            console.error('Failed to load rooms:', roomsResponse.status, roomsResponse.statusText);
          }

          renderChatContacts();

          // Автоматически выбрать первую публичную комнату, если есть
          const publicRooms = chatRooms.filter(room => room.type === 'public');
          console.log('Available public rooms:', publicRooms);
          if (publicRooms.length > 0 && !currentRoomId) {
            console.log('Auto-selecting first public room:', publicRooms[0]);
            // Используем requestAnimationFrame вместо setTimeout для лучшей синхронизации с DOM
            requestAnimationFrame(() => {
              if (publicRooms[0] && typeof publicRooms[0].id !== 'undefined') {
                selectRoom(publicRooms[0]);
              }
            });
          }
        } catch (error) {
          console.error('Failed to load contacts and rooms', error);
        }
      };

      /**
       * Render chat contacts list.
       */
      const renderChatContacts = () => {
        const chatList = document.getElementById('chat-list');
        const contactList = document.getElementById('contact-list');

        // Clear existing items except titles
        chatList.querySelectorAll('li:not(.chat-contact-list-item-title)').forEach(item => item.remove());
        contactList.querySelectorAll('li:not(.chat-contact-list-item-title)').forEach(item => item.remove());

        // Show/hide empty state
        const contactEmpty = document.getElementById('contact-list-item-0');
        if (contactEmpty) {
          contactEmpty.style.display = chatContacts.length === 0 ? 'block' : 'none';
        }

        console.log('Rendering contacts:', chatContacts.length, 'contacts');
        console.log('Rendering rooms:', chatRooms.length, 'rooms');

        // Add create public room button back after clearing the list
        const createRoomBtn = document.createElement('li');
        createRoomBtn.className = 'chat-contact-list-item mb-1';
        createRoomBtn.id = 'create-public-room-btn';
        createRoomBtn.innerHTML = `
          <a class="d-flex align-items-center">
            <div class="flex-shrink-0 avatar ">
              <span class="avatar-initial rounded-circle bg-label-success">
                <i class="icon-base ti tabler-plus icon-20px"></i>
              </span>
            </div>
            <div class="chat-contact-info flex-grow-1 ms-4">
              <h6 class="chat-contact-name text-truncate m-0 fw-normal">{{ __('Создать публичный чат') }}</h6>
              <small class="chat-contact-status text-truncate">{{ __('Нажмите для создания') }}</small>
            </div>
          </a>
        `;
        createRoomBtn.addEventListener('click', () => {
          const modal = new bootstrap.Modal(document.getElementById('createPublicRoomModal'));
          modal.show();
        });
        chatList.appendChild(createRoomBtn);

        // Render public rooms first
        chatRooms.forEach(room => {
          if (room.type === 'public') {
            const roomItem = document.createElement('li');
            roomItem.className = 'chat-contact-list-item';
            roomItem.innerHTML = `
              <a class="d-flex align-items-center">
                <div class="flex-shrink-0 avatar ">
                  <span class="avatar-initial rounded-circle bg-label-info">${(room.name || 'Ч').charAt(0).toUpperCase()}</span>
                </div>
                <div class="chat-contact-info flex-grow-1 ms-4">
                  <h6 class="chat-contact-name text-truncate m-0 fw-normal">${room.name || '{{ __("Публичный чат") }}'}</h6>
                  <small class="chat-contact-status text-truncate">
                    ${room.messages_count > 0
                      ? `${room.messages_count} {{ __("сообщений") }}`
                      : '{{ __("Новый чат") }}'
                    }
                  </small>
                </div>
              </a>
            `;

            roomItem.addEventListener('click', (e) => selectRoom(room, e));
            chatList.appendChild(roomItem);
          }
        });

        // Contacts are now pre-sorted by the API (active chats first)
        // Render contacts in the order received from server
        chatContacts.forEach(contact => {
          const contactItem = document.createElement('li');
          contactItem.className = 'chat-contact-list-item';
          contactItem.innerHTML = `
            <a class="d-flex align-items-center">
              <div class="flex-shrink-0 avatar avatar-${contact.status || 'offline'}">
                ${contact.avatar_url
                  ? `<img src="${contact.avatar_url}" alt="Avatar" class="rounded-circle" />`
                  : `<span class="avatar-initial rounded-circle bg-label-primary">${(contact.name || 'U').charAt(0).toUpperCase()}</span>`
                }
              </div>
              <div class="chat-contact-info flex-grow-1 ms-4">
                <h6 class="chat-contact-name text-truncate m-0 fw-normal">${contact.name}</h6>
                <small class="chat-contact-status text-truncate">
                  ${contact.unread_count > 0
                    ? `<span class="badge bg-danger rounded-pill">${contact.unread_count}</span> ${contact.last_message_text || ''}`
                    : contact.last_message_text || contact.role || '{{ __("Пользователь") }}'
                  }
                </small>
                </div>
            </a>
          `;

          contactItem.addEventListener('click', (e) => selectContact(contact, e));
          contactList.appendChild(contactItem);
        });
      };

      /**
       * Select a room to start conversation.
       * @param {Object} room - Room object.
       */
      const selectRoom = async (room, event) => {
        console.log('🏠 SELECTING ROOM:', room.name, 'ID:', room.id, 'Type:', room.type);
        if (!room || !room.id) {
          console.error('❌ Invalid room object:', room);
          return;
        }
        currentRoomId = room.id;
        currentContactId = null;
        currentRoom = room;
        document.querySelectorAll('.chat-contact-list-item').forEach(item => item.classList.remove('active'));
        if (event && event.currentTarget) {
          event.currentTarget.classList.add('active');
        }

        // Update chat header
        const chatName = document.querySelector('.chat-history-header .chat-contact-info h6');
        const chatStatus = document.querySelector('.chat-history-header .chat-contact-info small');
        const chatAvatar = document.querySelector('.chat-history-header .avatar');

        if (chatName) chatName.textContent = room.name || '{{ __("Публичный чат") }}';
        if (chatStatus) chatStatus.textContent = room.type === 'public' ? '{{ __("Публичный чат") }}' : '{{ __("Приватный чат") }}';
        if (chatAvatar) {
          chatAvatar.innerHTML = `<span class="avatar-initial rounded-circle bg-label-info">${(room.name || 'Ч').charAt(0).toUpperCase()}</span>`;
        }

        switchToChatConversation();

        // Stop any existing auto-refresh
        stopMessageAutoRefresh();

        // Показываем меню управления чатом для публичных чатов
        const roomManagementMenu = document.getElementById('room-management-menu');
        if (roomManagementMenu) {
          roomManagementMenu.style.display = room.type === 'public' ? 'block' : 'none';
        }

        console.log('Loading messages for room:', room.id);
        await loadChatMessages(room.id);
      };

      /**
       * Select a contact to start conversation.
       * @param {Object} contact - Contact object.
       */
      const selectContact = async (contact, event) => {
        console.log('👆 SELECTING CONTACT:', contact.name, 'ID:', contact.id);
        currentContactId = contact.id;
        document.querySelectorAll('.chat-contact-list-item').forEach(item => item.classList.remove('active'));
        if (event && event.currentTarget) {
          event.currentTarget.classList.add('active');
        }

        // Update chat header
        const chatName = document.querySelector('.chat-history-header .chat-contact-info h6');
        const chatStatus = document.querySelector('.chat-history-header .chat-contact-info small');
        const chatAvatar = document.querySelector('.chat-history-header .avatar');

        if (chatName) chatName.textContent = contact.name;
        if (chatStatus) chatStatus.textContent = contact.role || contact.email || '{{ __("Пользователь") }}';
        if (chatAvatar) {
          chatAvatar.innerHTML = contact.avatar_url
            ? `<img src="${contact.avatar_url}" alt="Avatar" class="rounded-circle" />`
            : `<span class="avatar-initial rounded-circle bg-label-primary">${(contact.name || 'U').charAt(0).toUpperCase()}</span>`;
        }

        switchToChatConversation();

        // Stop any existing auto-refresh
        stopMessageAutoRefresh();

        // Скрываем меню управления чатом для приватных чатов
        const roomManagementMenu = document.getElementById('room-management-menu');
        if (roomManagementMenu) {
          roomManagementMenu.style.display = 'none';
        }

        // Find or create private room
        console.log('Finding or creating private room for contact:', contact.id);
        const room = await findOrCreatePrivateRoom(contact.id);
        if (room) {
          currentRoomId = room.id;
          currentRoom = room;
          console.log('Room created/found:', room, 'currentRoomId set to:', currentRoomId);
          await loadChatMessages(room.id);
        } else {
          console.error('Failed to create or find private room');
        }
      };

      /**
       * Load chat messages for selected room.
       * @param {number} roomId - Room ID.
       */
      const loadChatMessages = async (roomId) => {
        console.log('📨 Loading messages for roomId:', roomId, 'Type:', typeof roomId);
        if (!roomId) {
          console.error('❌ No roomId provided to loadChatMessages');
          return;
        }
        // Убедимся, что roomId - число
        const numericRoomId = parseInt(roomId, 10);
        if (isNaN(numericRoomId) || numericRoomId <= 0) {
          console.error('❌ Invalid roomId:', roomId);
          return;
        }
        try {
          const response = await fetch(routes.roomMessages(numericRoomId), {
            headers: {
              'Accept': 'application/json',
              'X-Requested-With': 'XMLHttpRequest',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? ''
            },
            credentials: 'same-origin'
          });

          if (response.ok) {
            const data = await response.json();
            chatMessages = data.data || [];
            lastReadAt = data.meta?.last_read_at || null;
            renderChatMessages();

            // Set last message timestamp for auto-refresh
            if (chatMessages.length > 0) {
              const latestMessage = chatMessages[chatMessages.length - 1];
              if (latestMessage && latestMessage.created_at) {
                lastMessageTimestamp = latestMessage.created_at;
                console.log('Set lastMessageTimestamp to:', lastMessageTimestamp);
              }
            } else {
              lastMessageTimestamp = null;
              console.log('No messages loaded, lastMessageTimestamp set to null');
            }

            console.log('Set lastReadAt to:', lastReadAt);

            // Отмечаем чат как прочитанный
            await markRoomAsRead(numericRoomId);

            // Start auto-refresh
            console.log('🎯 ABOUT TO START AUTO-REFRESH - currentRoomId:', currentRoomId, 'currentContactId:', currentContactId);
            startMessageAutoRefresh();
            console.log('🎯 AUTO-REFRESH FUNCTION CALLED');
        } else {
            console.error('Failed to load messages:', response.status, response.statusText);
            // Clear messages if failed to load
            chatMessages = [];
            renderChatMessages();
          }
        } catch (error) {
          console.error('Failed to load messages', error);
          chatMessages = [];
          renderChatMessages();
        }
      };

      /**
       * Render chat messages.
       */
      const renderChatMessages = () => {
        const messageList = document.getElementById('chat-message-list');
        messageList.innerHTML = '';

        console.log('Rendering messages:', chatMessages);

        chatMessages.forEach(message => {
          console.log('📨 MESSAGE DEBUG:', {
            message_id: message.id,
            body: message.body,
            is_mine: message.is_mine,
            user_id: message.user?.id,
            user_name: message.user?.name,
            current_user_id: currentUserId,
            is_current_user_message: message.user?.id === currentUserId,
            final_decision: (message.user?.id === currentUserId || message.is_mine)
          });

          const isCurrentUserMessage = message.user?.id === currentUserId || message.is_mine;

          // Проверяем, является ли сообщение непрочитанным
          const isUnread = lastReadAt && message.created_at && new Date(message.created_at) > new Date(lastReadAt) && !isCurrentUserMessage;

          const messageItem = document.createElement('li');
          messageItem.className = `chat-message ${isCurrentUserMessage ? 'chat-message-right' : ''} ${isUnread ? 'chat-message-unread' : ''}`;

          const avatarMarkup = isCurrentUserMessage ? `
            <div class="user-avatar flex-shrink-0 ms-4">
              <div class="avatar avatar-sm">
                <img src="{{ $currentUser->avatar_url }}" alt="Avatar" class="rounded-circle" />
              </div>
            </div>
          ` : `
            <div class="user-avatar flex-shrink-0 me-4">
              <div class="avatar avatar-sm">
                ${message.user?.avatar_url
                  ? `<img src="${message.user.avatar_url}" alt="Avatar" class="rounded-circle" />`
                  : `<span class="avatar-initial rounded-circle bg-label-primary">${(message.user?.name || 'U').charAt(0).toUpperCase()}</span>`
                }
              </div>
            </div>
          `;

          const messageContent = isCurrentUserMessage ? `
            <div class="d-flex overflow-hidden">
              <div class="chat-message-wrapper flex-grow-1">
                <div class="chat-message-text message-hover-container">
                  <p class="mb-0">${message.body}</p>
                  <div class="message-time-tooltip">
                    <small class="text-body-secondary">${message.created_at_human}</small>
                  </div>
                </div>
              </div>
              ${avatarMarkup}
            </div>
          ` : `
            <div class="d-flex overflow-hidden">
              ${avatarMarkup}
              <div class="chat-message-wrapper flex-grow-1">
                <div class="chat-message-text message-hover-container">
                  <p class="mb-0">${message.body}</p>
                  <div class="message-time-tooltip">
                    <small class="text-body-secondary">${message.created_at_human}</small>
                  </div>
                </div>
              </div>
            </div>
          `;

          messageItem.innerHTML = messageContent;
          messageList.appendChild(messageItem);
        });

        scrollToBottom();
      };

      // Initialize PerfectScrollbar
      initPerfectScrollbar([
        elements.chatContactsBody,
        elements.chatHistoryBody,
        elements.chatSidebarLeftBody,
        elements.chatSidebarRightBody
      ]);

      // Scroll to the bottom of the chat history
      scrollToBottom();

      // Attach user status change event
      elements.chatUserStatus.forEach(statusInput => {
        statusInput.addEventListener('click', () => updateUserStatus(statusInput.value));
      });

      // Handle max length for textarea
      const maxLength = parseInt(elements.chatSidebarLeftUserAbout.getAttribute('maxlength'), 10);
      handleMaxLengthCount(elements.chatSidebarLeftUserAbout, elements.textareaInfo, maxLength);

      elements.chatSidebarLeftUserAbout.addEventListener('input', () => {
        handleMaxLengthCount(elements.chatSidebarLeftUserAbout, elements.textareaInfo, maxLength);
      });

      // Attach chat conversation switch event
      elements.conversationButton?.addEventListener('click', () => {
        // Show contacts sidebar
        const contactsSidebar = document.querySelector('.app-chat-contacts');
        if (contactsSidebar) {
          contactsSidebar.classList.remove('d-none');
        }
      });

      // Attach chat contact selection event
      elements.chatContactListItems.forEach(item => {
        item.addEventListener('click', () => {
          elements.chatContactListItems.forEach(contact => contact.classList.remove('active'));
          item.classList.add('active');
          switchToChatConversation();
        });
      });

      // Attach chat search filter event
      elements.searchInput?.addEventListener(
        'keyup',
        debounce(e => {
          const searchValue = e.target.value.toLowerCase();
          filterChatContacts('#chat-list li', searchValue, '.chat-list-item-0');
          filterChatContacts('#contact-list li', searchValue, '.contact-list-item-0');
        }, 300)
      );

      // Attach message send event
      elements.formSendMessage?.addEventListener('submit', async e => {
        e.preventDefault();
        const message = elements.messageInput.value.trim();
        console.log('Attempting to send message:', { message, currentRoomId, currentContactId });
        if (message && currentRoomId) {
          try {
            console.log('Sending message to room:', currentRoomId);
            const response = await fetch(routes.sendMessage(currentRoomId), {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
              'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? ''
              },
              credentials: 'same-origin',
              body: JSON.stringify({ message })
            });

            if (response.ok) {
              const result = await response.json();
              console.log('Message sent successfully:', result.data);
              // Add message to chat
              chatMessages.push(result.data);
              renderChatMessages();
              elements.messageInput.value = '';

              // Update last message timestamp and read time after sending
              if (result.data && result.data.created_at) {
                lastMessageTimestamp = result.data.created_at;
                lastReadAt = result.data.created_at; // Сообщение прочитано сразу после отправки
                console.log('Updated lastMessageTimestamp after sending:', lastMessageTimestamp);
                console.log('Updated lastReadAt after sending:', lastReadAt);
              }
            } else {
              console.error('Failed to send message:', response.status, response.statusText);
              const errorData = await response.json().catch(() => ({}));
              console.error('Error details:', errorData);
            }
        } catch (error) {
            console.error('Error sending message', error);
          }
        } else {
          console.warn('Cannot send message: no message text or no active room');
        }
      });

      // Attach manual refresh button event
      const manualRefreshBtn = document.getElementById('manual-refresh-btn');
      if (manualRefreshBtn) {
        manualRefreshBtn.addEventListener('click', async () => {
          console.log('Manual refresh triggered');
          if (currentRoomId) {
            await loadChatMessages(currentRoomId);
          }
        });
      }

      // Attach create public room form event
      const createPublicRoomForm = document.getElementById('create-public-room-form');
      if (createPublicRoomForm) {
        createPublicRoomForm.addEventListener('submit', async (e) => {
          e.preventDefault();
          const formData = new FormData(createPublicRoomForm);
          const roomName = formData.get('name')?.trim();

          if (!roomName) {
            document.getElementById('public-room-error').textContent = '{{ __("Введите название чата") }}';
            document.getElementById('public-room-error').classList.add('d-block');
          return;
        }

        try {
            const formData = new FormData();
            formData.append('type', 'public');
            formData.append('name', roomName);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '');

            const response = await fetch(routes.createRoom, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
              },
              credentials: 'same-origin',
              body: formData
            });

            if (response.ok) {
              const result = await response.json();
              // Close modal
              const modal = bootstrap.Modal.getInstance(document.getElementById('createPublicRoomModal'));
              modal?.hide();

              // Reset form
              createPublicRoomForm.reset();
              document.getElementById('public-room-error').classList.remove('d-block');

              // Add room to chat list
              const chatList = document.getElementById('chat-list');
              const roomItem = document.createElement('li');
              roomItem.className = 'chat-contact-list-item';
              roomItem.innerHTML = `
                <a class="d-flex align-items-center">
                  <div class="flex-shrink-0 avatar ">
                    <span class="avatar-initial rounded-circle bg-label-primary">${roomName.charAt(0).toUpperCase()}</span>
                  </div>
                  <div class="chat-contact-info flex-grow-1 ms-4">
                    <h6 class="chat-contact-name text-truncate m-0 fw-normal">${roomName}</h6>
                    <small class="chat-contact-status text-truncate">{{ __("Новый публичный чат") }}</small>
                  </div>
                </a>
              `;
              chatList.appendChild(roomItem);

              // Show success message
              console.log('Public room created successfully');
            } else {
              const error = await response.json();
              document.getElementById('public-room-error').textContent = error.message || '{{ __("Ошибка создания чата") }}';
              document.getElementById('public-room-error').classList.add('d-block');
            }
        } catch (error) {
            console.error('Failed to create public room', error);
            document.getElementById('public-room-error').textContent = '{{ __("Ошибка сети") }}';
            document.getElementById('public-room-error').classList.add('d-block');
          }
        });
      }

      // Обработчики управления чатом
      const renameRoomBtn = document.getElementById('rename-room-btn');
      const clearRoomBtn = document.getElementById('clear-room-btn');
      const deleteRoomBtn = document.getElementById('delete-room-btn');

      if (renameRoomBtn) {
        renameRoomBtn.addEventListener('click', () => {
          if (!currentRoom) return;

          const input = document.getElementById('rename-room-name');
          if (input) input.value = currentRoom.name || '';

          const modal = new bootstrap.Modal(document.getElementById('renameRoomModal'));
          modal.show();
        });
      }

      if (clearRoomBtn) {
        clearRoomBtn.addEventListener('click', () => {
          if (!currentRoom) return;

          const modal = new bootstrap.Modal(document.getElementById('clearRoomModal'));
          modal.show();
        });
      }

      if (deleteRoomBtn) {
        deleteRoomBtn.addEventListener('click', () => {
          if (!currentRoom) return;

          const modal = new bootstrap.Modal(document.getElementById('deleteRoomModal'));
          modal.show();
        });
      }

      // Обработчик формы переименования чата
      const renameRoomForm = document.getElementById('rename-room-form');
      if (renameRoomForm) {
        renameRoomForm.addEventListener('submit', async (e) => {
          e.preventDefault();
          if (!currentRoomId) return;

          const formData = new FormData(renameRoomForm);
          const newName = formData.get('name')?.trim();

          if (!newName) {
            document.getElementById('rename-room-error').textContent = '{{ __("Введите название чата") }}';
            document.getElementById('rename-room-error').classList.add('d-block');
            return;
          }

          try {
            const response = await fetch(routes.updateRoom(currentRoomId), {
              method: 'PUT',
              headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? ''
              },
              credentials: 'same-origin',
              body: JSON.stringify({ name: newName })
            });

            if (response.ok) {
              const result = await response.json();

              // Обновляем текущую комнату
              currentRoom = result.data;

              // Обновляем UI
              const chatName = document.querySelector('.chat-history-header .chat-contact-info h6');
              if (chatName) chatName.textContent = currentRoom.name || '{{ __("Публичный чат") }}';

              // Закрываем модал
              const modal = bootstrap.Modal.getInstance(document.getElementById('renameRoomModal'));
              modal?.hide();

              // Обновляем список чатов
              await refreshContacts();

              console.log('Room renamed successfully');
            } else {
              const error = await response.json();
              document.getElementById('rename-room-error').textContent = error.message || '{{ __("Ошибка переименования") }}';
              document.getElementById('rename-room-error').classList.add('d-block');
            }
          } catch (error) {
            console.error('Failed to rename room', error);
            document.getElementById('rename-room-error').textContent = '{{ __("Ошибка сети") }}';
            document.getElementById('rename-room-error').classList.add('d-block');
          }
        });
      }

      // Обработчик подтверждения очистки чата
      const confirmClearRoomBtn = document.getElementById('confirm-clear-room');
      if (confirmClearRoomBtn) {
        confirmClearRoomBtn.addEventListener('click', async () => {
          if (!currentRoomId) return;

          try {
            const response = await fetch(routes.clearRoomMessages(currentRoomId), {
              method: 'DELETE',
              headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? ''
              },
              credentials: 'same-origin'
            });

            if (response.ok) {
              // Закрываем модал
              const modal = bootstrap.Modal.getInstance(document.getElementById('clearRoomModal'));
              modal?.hide();

              // Перезагружаем сообщения
              await loadChatMessages(currentRoomId);

              console.log('Room messages cleared successfully');
            } else {
              console.error('Failed to clear room messages');
            }
          } catch (error) {
            console.error('Failed to clear room messages', error);
          }
        });
      }

      // Обработчик подтверждения удаления чата
      const confirmDeleteRoomBtn = document.getElementById('confirm-delete-room');
      if (confirmDeleteRoomBtn) {
        confirmDeleteRoomBtn.addEventListener('click', async () => {
          if (!currentRoomId) return;

          try {
            const response = await fetch(routes.deleteRoom(currentRoomId), {
              method: 'DELETE',
              headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? ''
              },
              credentials: 'same-origin'
            });

            if (response.ok) {
              // Закрываем модал
              const modal = bootstrap.Modal.getInstance(document.getElementById('deleteRoomModal'));
              modal?.hide();

              // Возвращаемся к списку контактов
              switchToChatConversation();

              // Очищаем текущую комнату
              currentRoomId = null;
              currentContactId = null;
              currentRoom = null;

              // Обновляем список чатов
              await refreshContacts();

              console.log('Room deleted successfully');
            } else {
              console.error('Failed to delete room');
            }
          } catch (error) {
            console.error('Failed to delete room', error);
          }
        });
      }

      // Fix overlay issue for chat sidebar
      elements.chatHistoryHeader?.addEventListener('click', () => {
        document.querySelector('.app-chat-sidebar-left .close-sidebar')?.removeAttribute('data-overlay');
        // Stop auto-refresh when going back to contacts
        stopMessageAutoRefresh();
        currentRoomId = null;
        currentContactId = null;
        currentRoom = null;
        chatMessages = [];
      });

      // Stop auto-refresh when page unloads
      window.addEventListener('beforeunload', () => {
        stopMessageAutoRefresh();
        stopContactAutoRefresh();
      });

      // Initialize with server data
      chatRooms = initialRooms;
      chatContacts = initialPrivateUsers;

      // Ensure message time tooltips are hidden by default
      const style = document.createElement('style');
      style.textContent = `
        .message-time-tooltip {
          opacity: 0 !important;
          visibility: hidden !important;
        }
        .message-hover-container:hover .message-time-tooltip {
          opacity: 1 !important;
          visibility: visible !important;
        }
      `;
      document.head.appendChild(style);

      // Load initial data (will refresh from server)
      loadChatContacts();

      // Start auto-refreshing contacts
      startContactAutoRefresh();

      // Initialize speech-to-text
      initSpeechToText();
    });

    /**
     * Debounce utility function.
     * @param {Function} func - Function to debounce.
     * @param {number} wait - Delay in milliseconds.
     */
    function debounce(func, wait) {
      let timeout;
      return (...args) => {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
      };
    }
  </script>
@endpush

