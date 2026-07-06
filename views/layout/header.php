<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'PTE Management System', ENT_QUOTES, 'UTF-8') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont/dist/tabler-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <style>
        #sidebar { transition: transform 220ms cubic-bezier(0.16, 1, 0.3, 1); }
        #sidebar-backdrop { transition: opacity 220ms cubic-bezier(0.16, 1, 0.3, 1); }
        @media (prefers-reduced-motion: reduce) {
            #sidebar, #sidebar-backdrop { transition: none; }
        }

        #toast-viewport {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 50;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            width: 100%;
            max-width: 22rem;
            pointer-events: none;
        }
        .toast {
            pointer-events: auto;
            transform: translateX(120%);
            opacity: 0;
            transition: transform 260ms cubic-bezier(0.16, 1, 0.3, 1), opacity 220ms ease-out;
        }
        .toast.toast-visible {
            transform: translateX(0);
            opacity: 1;
        }
        .toast.toast-leaving {
            transform: translateX(120%);
            opacity: 0;
        }
        @media (prefers-reduced-motion: reduce) {
            .toast { transition: opacity 150ms ease-out; transform: none; }
            .toast.toast-leaving { transform: none; }
        }
        @media (max-width: 640px) {
            #toast-viewport { left: 1rem; right: 1rem; max-width: none; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800">

<div id="toast-viewport" aria-live="polite" aria-atomic="true"></div>
