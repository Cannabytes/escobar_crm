<?php

return [
    'page_title' => 'Ledger',
    'page_subtitle' => 'Храните кошельки, сети и валюты в одном месте для команды.',

    'actions' => [
        'create' => 'Добавить кошелёк',
        'edit' => 'Редактировать',
        'save' => 'Сохранить',
        'cancel' => 'Отмена',
        'close' => 'Закрыть',
        'delete' => 'Удалить',
    ],

    'flash_created' => 'Запись добавлена.',
    'flash_updated' => 'Запись обновлена.',
    'flash_deleted' => 'Запись удалена.',

    'validation_error_title' => 'Проверьте форму и исправьте ошибки.',

    'filters' => [
        'search_label' => 'Поиск по кошельку, сети или валюте',
        'search_placeholder' => 'Начните вводить кошелёк, сеть или валюту',
        'network_label' => 'Сеть',
        'network_all' => 'Все сети',
        'currency_label' => 'Валюта',
        'currency_all' => 'Все валюты',
        'status_label' => 'Статус',
        'status_all' => 'Все статусы',
        'apply' => 'Применить',
        'reset' => 'Сбросить',
    ],

    'table' => [
        'wallet' => 'Кошелёк',
        'network' => 'Сеть',
        'currency' => 'Валюта',
        'status' => 'Статус',
        'updated_at' => 'Обновлено',
        'actions' => 'Действия',
    ],

    'statuses' => [
        'active' => 'Активен',
        'inactive' => 'Неактивен',
    ],

    'empty' => [
        'title' => 'Пока нет записей',
        'description' => 'Добавьте первую запись, чтобы коллеги видели данные.',
    ],

    'records_count' => '{0} Нет записей|{1} :count запись|{2,3,4} :count записи|[5,*] :count записей',

    'create' => [
        'title' => 'Добавить кошелёк',
    ],

    'edit' => [
        'title' => 'Редактирование кошелька ":wallet"',
    ],

    'fields' => [
        'wallet' => 'Кошелёк / адрес',
        'network' => 'Сеть',
        'currency' => 'Валюта',
        'status' => 'Статус',
    ],

    'placeholders' => [
        'wallet' => 'Укажите кошелёк или номер счёта',
        'network' => 'Например: TRON, ERC-20, BEP-20',
        'currency' => 'Например: USDT, BTC',
    ],

    'permissions' => [
        'view' => 'Может просматривать Ledger',
        'manage' => 'Может полностью управлять Ledger',
    ],

    'delete_confirm_title' => 'Удалить кошелёк?',
    'delete_confirm_text' => 'Запись ":wallet" будет удалена безвозвратно. Продолжить?',
    'delete_fallback' => 'Удалить запись ":wallet"?',
];

