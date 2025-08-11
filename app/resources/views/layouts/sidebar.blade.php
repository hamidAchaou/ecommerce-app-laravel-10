<?php
$menuGroups = [
    [
        'label' => 'General',
        'icon' => 'fas fa-home',
        'items' => [
            [
                'label' => 'Dashboard',
                'route' => 'admin.dashboard',
                'icon' => 'fas fa-tachometer-alt',
                'active' => request()->routeIs('admin.dashboard'),
            ],
        ],
    ],
    [
        'label' => 'Catalog',
        'icon' => 'fas fa-shopping-bag',
        'items' => [
            [
                'label' => 'Products',
                'route' => 'admin.products.index',
                'icon' => 'fas fa-box',
                'active' => request()->routeIs('admin.products.*'),
            ],
            [
                'label' => 'Categories',
                'route' => 'admin.categories.index',
                'icon' => 'fas fa-folder-open',
                'active' => request()->routeIs('admin.categories.*'),
            ],
        ],
    ],
    [
        'label' => 'Commandes',
        'icon' => 'fas fa-shopping-cart',
        'items' => [
            [
                'label' => 'Toutes les Commandes',
                'route' => 'admin.orders.index',
                'icon' => 'fas fa-list',
                'active' => request()->routeIs('admin.orders.*'),
            ],
            [
                'label' => 'Commandes en Attente',
                'route' => 'admin.orders.pending',
                'icon' => 'fas fa-clock',
                'active' => request()->routeIs('admin.orders.pending'),
            ],
        ],
    ],
    [
        'label' => 'Utilisateurs & Accès',
        'icon' => 'fas fa-user-shield',
        'items' => [
            [
                'label' => 'Utilisateurs',
                'route' => 'admin.users.index',
                'icon' => 'fas fa-user',
                'active' => request()->routeIs('admin.users.*'),
            ],
            [
                'label' => 'Rôles',
                'route' => 'admin.roles.index',
                'icon' => 'fas fa-user-tag',
                'active' => request()->routeIs('admin.roles.*'),
            ],
            [
                'label' => 'Permissions',
                'route' => 'admin.permissions.index',
                'icon' => 'fas fa-key',
                'active' => request()->routeIs('admin.permissions.*'),
            ],
        ],
    ],
];
