<?php

return [
    ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'match' => 'admin.dashboard', 'permission' => 'dashboard.view', 'icon' => 'grid'],
    ['label' => 'Players', 'route' => 'admin.players.index', 'match' => 'admin.players.*', 'permission' => 'players.view', 'icon' => 'users'],
    ['label' => 'Registrations', 'route' => 'admin.registrations.index', 'match' => 'admin.registrations.*', 'permission' => 'registrations.view', 'icon' => 'how_to_reg'],
    ['label' => 'Payments', 'route' => 'admin.payments.index', 'match' => 'admin.payments.*', 'permission' => 'payments.view', 'icon' => 'payments'],
    ['label' => 'Programs', 'route' => 'admin.programs.index', 'match' => 'admin.programs.*', 'permission' => 'programs.view', 'icon' => 'sports_soccer'],
    ['label' => 'Attendance', 'route' => 'admin.attendance.index', 'match' => 'admin.attendance.*', 'permission' => 'attendance.view', 'icon' => 'fact_check'],
    ['label' => 'Performance', 'route' => 'admin.performance.index', 'match' => 'admin.performance.*', 'permission' => 'performance.view', 'icon' => 'analytics'],
    ['label' => 'Coaches', 'route' => 'admin.coaches.index', 'match' => 'admin.coaches.*', 'permission' => 'coaches.view', 'icon' => 'groups'],
    ['label' => 'Tournaments', 'route' => 'admin.tournaments.index', 'match' => 'admin.tournaments.*', 'permission' => 'tournaments.view', 'icon' => 'emoji_events'],
    ['label' => 'Communications', 'route' => 'admin.announcements.index', 'match' => 'admin.announcements.*', 'permission' => 'announcements.view', 'icon' => 'campaign'],
    ['label' => 'Reports', 'route' => 'admin.analytics.index', 'match' => 'admin.analytics.*', 'permission' => 'analytics.view', 'icon' => 'bar_chart'],
    ['label' => 'Settings', 'route' => 'admin.settings.edit', 'match' => 'admin.settings.*', 'permission' => 'settings.manage', 'icon' => 'settings'],
];
