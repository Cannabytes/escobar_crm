<?php

return [
    'page_title' => 'Ledger',
    'page_subtitle' => 'Keep wallets, networks and currencies aligned for the team.',

    'actions' => [
        'create' => 'Add wallet',
        'edit' => 'Edit',
        'save' => 'Save',
        'cancel' => 'Cancel',
        'close' => 'Close',
        'delete' => 'Delete',
    ],

    'flash_created' => 'Ledger record created.',
    'flash_updated' => 'Ledger record updated.',
    'flash_deleted' => 'Ledger record deleted.',

    'validation_error_title' => 'Please fix the validation errors below.',

    'filters' => [
        'search_label' => 'Search by wallet, network or currency',
        'search_placeholder' => 'Start typing wallet, network or currency',
        'network_label' => 'Network',
        'network_all' => 'All networks',
        'currency_label' => 'Currency',
        'currency_all' => 'All currencies',
        'status_label' => 'Status',
        'status_all' => 'All statuses',
        'apply' => 'Apply filters',
        'reset' => 'Reset',
    ],

    'table' => [
        'wallet' => 'Wallet',
        'network' => 'Network',
        'currency' => 'Currency',
        'status' => 'Status',
        'updated_at' => 'Updated at',
        'actions' => 'Actions',
    ],

    'statuses' => [
        'active' => 'Active',
        'inactive' => 'Inactive',
    ],

    'empty' => [
        'title' => 'No wallet entries yet',
        'description' => 'Start by adding the first wallet record.',
    ],

    'records_count' => '{0} No records yet|{1} 1 record|[2,*] :count records',

    'create' => [
        'title' => 'Add wallet',
    ],

    'edit' => [
        'title' => 'Edit wallet ":wallet"',
    ],

    'fields' => [
        'wallet' => 'Wallet / Address',
        'network' => 'Network',
        'currency' => 'Currency',
        'status' => 'Status',
    ],

    'placeholders' => [
        'wallet' => 'Enter wallet or account identifier',
        'network' => 'Example: TRON, ERC-20, BEP-20',
        'currency' => 'Example: USDT, BTC',
    ],

    'permissions' => [
        'view' => 'Can view ledger',
        'manage' => 'Can fully manage ledger',
    ],

    'delete_confirm_title' => 'Delete wallet?',
    'delete_confirm_text' => 'This will permanently remove the wallet ":wallet". Continue?',
    'delete_fallback' => 'Delete wallet ":wallet"?',
];

