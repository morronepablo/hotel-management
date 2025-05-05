<?php

return [

    'title' => 'Hotel Management',
    'title_prefix' => '',
    'title_postfix' => '',

    'use_ico_only' => false,
    'use_full_favicon' => false,

    'google_fonts' => [
        'allowed' => true,
    ],

    'logo' => '<b>Hotel</b> Management',
    'logo_img' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
    'logo_img_class' => 'brand-image img-circle elevation-3',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt' => 'Admin Logo',

    'auth_logo' => [
        'enabled' => false,
        'img' => [
            'path' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
            'alt' => 'Auth Logo',
            'class' => '',
            'width' => 50,
            'height' => 50,
        ],
    ],

    'preloader' => [
        'enabled' => false,
        'mode' => 'fullscreen',
        'img' => [
            'path' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
            'alt' => 'AdminLTE Preloader Image',
            'effect' => 'animation__shake',
            'width' => 60,
            'height' => 60,
        ],
    ],

    'usermenu_enabled' => true,
    'usermenu_header' => true,
    'usermenu_header_class' => 'bg-primary',
    'usermenu_image' => false,
    'usermenu_desc' => false,
    'usermenu_profile_url' => false,

    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => true,
    'layout_fixed_navbar' => true,
    'layout_fixed_footer' => true,
    'layout_dark_mode' => false,

    'classes_auth_card' => 'card-outline card-primary',
    'classes_auth_header' => '',
    'classes_auth_body' => '',
    'classes_auth_footer' => '',
    'classes_auth_icon' => '',
    'classes_auth_btn' => 'btn-flat btn-primary',

    'classes_body' => 'layout-navbar-fixed',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_wrapper' => '',
    'classes_content_header' => '',
    'classes_content' => '',
    'classes_sidebar' => 'sidebar-dark-primary elevation-4',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-white navbar-light fixed-top',
    'classes_topnav_nav' => 'navbar-expand',
    'classes_topnav_container' => 'container',

    'sidebar_mini' => 'lg',
    'sidebar_collapse' => false,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => false,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,

    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    'use_route_url' => false,
    'dashboard_url' => 'dashboard',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'password/reset',
    'password_email_url' => 'password/email',
    'profile_url' => false,

    'enabled_laravel_mix' => false,
    'laravel_mix_css_path' => 'css/app.css',
    'laravel_mix_js_path' => 'js/app.js',

    'menu' => [
        [
            'text' => 'Reserva',
            'url' => 'reservas/calendario',
            'icon' => 'fas fa-calendar-alt',
            'can' => 'ver-reservas',
        ],
        [
            'text' => 'Entradas',
            'icon' => 'fas fa-sign-in-alt',
            'submenu' => [
                [
                    'text' => 'Panel de Control',
                    'url' => 'entradas/panel-control',
                    'can' => 'ver-panel-control',
                ],
                [
                    'text' => 'Recepción',
                    'url' => 'entradas/panel-control',
                    'active' => ['entradas/recepcion/*'],
                    'can' => 'ver-recepcion',
                ],
                [
                    'text' => 'Listado de Registros',
                    'url' => 'entradas/registros',
                    'can' => 'ver-registros',
                ],
                [
                    'text' => 'Renovaciones',
                    'url' => 'entradas/renovaciones',
                    'can' => 'ver-renovaciones',
                ],
                [
                    'text' => 'Clientes',
                    'url' => 'clientes',
                    'can' => 'ver-clientes',
                ],
            ],
        ],
        [
            'text' => 'Consumo/Servicio',
            'icon' => 'fas fa-concierge-bell',
            'submenu' => [
                [
                    'text' => 'Consumo',
                    'url' => 'consumo',
                    'route' => 'consumo.index', // Ruta principal del índice de consumos
                    'active' => ['consumo', 'consumo/*'], // Patrón para activar este elemento
                ],
                [
                    'text' => 'Servicio',
                    'url' => 'servicio-consumo', // Cambiamos de 'consumo-servicio/servicio' a 'servicio-consumo'
                    'route' => 'servicio-consumo.index', // Apuntamos a la ruta correcta
                    'active' => ['servicio-consumo', 'servicio-consumo/*'], // Patrón para activar este elemento
                    'can' => 'ver-consumo-servicio',
                ],
            ],
        ],
        [
            'text' => 'Compras',
            'url' => 'compras',
            'icon' => 'fas fa-shopping-cart',
            'can' => 'ver-compras',
        ],
        [
            'text' => 'Salidas',
            'url' => 'salidas',
            'icon' => 'fas fa-sign-out-alt',
            'can' => 'ver-salidas',
            'submenu' => [
                [
                    'text' => 'Verificación de Salidas',
                    'url' => 'entradas/salidas',
                    'route' => 'salidas.index',
                    'active' => ['salidas', 'salidas/*'],
                    'can' => 'ver-salidas',
                ],
            ],
        ],
        [
            'text' => 'Caja',
            'icon' => 'fas fa-cash-register',
            'submenu' => [
                [
                    'text' => 'Listado Arqueos',
                    'url' => 'caja/arqueos',
                    'icon' => 'fas fa-fw fa-list',
                    'can' => 'ver-caja',
                ],
                [
                    'text' => 'Pagos',
                    'url' => 'caja/pagos',
                    'can' => 'ver-pagos',
                ],
            ],
        ],
        [
            'text' => 'Reportes',
            'url' => 'reportes',
            'icon' => 'fas fa-chart-bar',
            'can' => 'ver-reportes',
        ],
        [
            'text' => 'Mantenimiento',
            'icon' => 'fas fa-tools',
            'submenu' => [
                [
                    'text' => 'Niveles',
                    'url' => 'mantenimiento/nivel',
                    'icon' => 'fas fa-layer-group',
                    'can' => 'ver-niveles',
                ],
                [
                    'text' => 'Tipo de Habitación',
                    'url' => 'mantenimiento/tipo_habitacion',
                    'icon' => 'fas fa-bed',
                    'can' => 'ver-tipos-habitacion',
                ],
                [
                    'text' => 'Habitación',
                    'url' => 'mantenimiento/habitacion',
                    'icon' => 'fas fa-door-open',
                    'can' => 'ver-habitaciones',
                ],
            ],
        ],
        [
            'text' => 'Almacén',
            'icon' => 'fas fa-warehouse',
            'submenu' => [
                [
                    'text' => 'Servicio',
                    'url' => 'almacen/servicios',
                    'can' => 'ver-servicios',
                ],
                [
                    'text' => 'Productos',
                    'url' => 'almacen/productos',
                    'can' => 'ver-productos',
                ],
                [
                    'text' => 'Categorías',
                    'url' => 'almacen/categorias',
                    'can' => 'ver-categorias',
                ],
            ],
        ],
        [
            'text' => 'Acceso',
            'icon' => 'fas fa-key',
            'submenu' => [
                [
                    'text' => 'Usuarios',
                    'url' => 'acceso/usuarios',
                    'can' => 'ver-usuarios',
                ],
                [
                    'text' => 'Personal',
                    'url' => 'acceso/personal',
                    'can' => 'ver-personal',
                ],
            ],
        ],
        [
            'text' => 'Seguridad',
            'icon' => 'fas fa-shield-alt',
            'submenu' => [
                [
                    'text' => 'Roles',
                    'url' => 'roles',
                    'icon' => 'fas fa-users-cog',
                    'can' => 'ver-roles',
                ],
                [
                    'text' => 'Permisos',
                    'url' => 'permisos',
                    'icon' => 'fas fa-lock',
                    'can' => 'ver-permisos',
                ],
            ],
        ],
        [
            'text' => 'Configuración',
            'icon' => 'fas fa-cogs',
            'submenu' => [
                [
                    'text' => 'Ajustes',
                    'url' => 'configuracion/datos_hotel',
                    'can' => 'ver-configuracion',
                ],
                [
                    'text' => 'Tipo Documento',
                    'url' => 'configuracion/tipo_documento',
                    'can' => 'ver-tipo-documento',
                ],
                [
                    'text' => 'U. Medida',
                    'url' => 'configuracion/unidad_medida',
                    'can' => 'ver-unidad-medida',
                ],
            ],
        ],
    ],

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],

    'plugins' => [
        'FontAwesome' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css'
                ],
            ],
        ],
        'Datatables' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.11.3/css/dataTables.bootstrap4.min.css',
                ],
            ],
        ],
        'Select2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css',
                ],
            ],
        ],
        'Chartjs' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                ],
            ],
        ],
        'Sweetalert2' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => '//cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => '//cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css',
                ],
            ],
        ],
        'Pace' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
                ],
            ],
        ],
        'Toastr' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js',
                ],
            ],
        ],
    ],

    'iframe' => [
        'default_tab' => [
            'url' => null,
            'title' => null,
        ],
        'buttons' => [
            'close' => true,
            'close_all' => true,
            'close_all_other' => true,
            'scroll_left' => true,
            'scroll_right' => true,
            'fullscreen' => true,
        ],
        'options' => [
            'loading_screen' => 1000,
            'auto_show_new_tab' => true,
            'use_navbar_items' => true,
        ],
    ],

    'livewire' => false,
];
