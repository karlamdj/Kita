import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                cyan: {
                    50: 'rgb(var(--color-cyan-50) / <alpha-value>)',
                    100: 'rgb(var(--color-cyan-100) / <alpha-value>)',
                    200: 'rgb(var(--color-cyan-200) / <alpha-value>)',
                    300: 'rgb(var(--color-cyan-300) / <alpha-value>)',
                    400: 'rgb(var(--color-cyan-400) / <alpha-value>)',
                    500: 'rgb(var(--color-cyan-500) / <alpha-value>)',
                    600: 'rgb(var(--color-cyan-600) / <alpha-value>)',
                    700: 'rgb(var(--color-cyan-700) / <alpha-value>)',
                    800: 'rgb(var(--color-cyan-800) / <alpha-value>)',
                    900: 'rgb(var(--color-cyan-900) / <alpha-value>)',
                    950: 'rgb(var(--color-cyan-950) / <alpha-value>)',
                },
                blue: {
                    50: 'rgb(var(--color-blue-50) / <alpha-value>)',
                    100: 'rgb(var(--color-blue-100) / <alpha-value>)',
                    200: 'rgb(var(--color-blue-200) / <alpha-value>)',
                    300: 'rgb(var(--color-blue-300) / <alpha-value>)',
                    400: 'rgb(var(--color-blue-400) / <alpha-value>)',
                    500: 'rgb(var(--color-blue-500) / <alpha-value>)',
                    600: 'rgb(var(--color-blue-600) / <alpha-value>)',
                    700: 'rgb(var(--color-blue-700) / <alpha-value>)',
                    800: 'rgb(var(--color-blue-800) / <alpha-value>)',
                    900: 'rgb(var(--color-blue-900) / <alpha-value>)',
                    950: 'rgb(var(--color-blue-950) / <alpha-value>)',
                },
            },
            boxShadow: {
                'cyan-glow-sm': '0 0 8px rgb(var(--color-cyan-500) / 0.4)',
                'cyan-glow-sm-50': '0 0 8px rgb(var(--color-cyan-500) / 0.5)',
                'cyan-glow-15': '0 0 15px rgb(var(--color-cyan-500) / 0.15)',
                'cyan-glow-20': '0 0 15px rgb(var(--color-cyan-500) / 0.2)',
                'cyan-glow-40': '0 0 15px rgb(var(--color-cyan-500) / 0.4)',
            },
        },
    },

    plugins: [forms],
};
