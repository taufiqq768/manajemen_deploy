import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],

    safelist: [
        // Status badge classes — generated dynamically in DeployRequest::statusBadge()
        'bg-emerald-500/15', 'text-emerald-700', 'dark:text-emerald-400', 'ring-emerald-500/30',
        'bg-red-500/15',     'text-red-700',     'dark:text-red-400',     'ring-red-500/30',
        'bg-amber-500/15',   'text-amber-700',   'dark:text-amber-400',   'ring-amber-500/30',
        'ring-1',
    ],
};
