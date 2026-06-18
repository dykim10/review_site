import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans:       ['Pretendard', ...defaultTheme.fontFamily.sans],
                pretendard: ['Pretendard', ...defaultTheme.fontFamily.sans],
                archivo:    ['Archivo', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'rv-bg':        '#F7F8FA',
                'rv-card':      '#FFFFFF',
                'rv-line':      '#E8EAEE',
                'rv-ink':       '#16181D',
                'rv-ink2':      '#5A6170',
                'rv-ink3':      '#9AA1AE',
                'rv-pink':      '#E80043',
                'rv-pink-soft': '#FFF0F4',
                'rv-blue':      '#2563EB',
                'rv-platinum':  '#5B6470',
                'rv-gold':      '#B8860B',
                'rv-elite':     '#1F7A5A',
                'rv-label':     '#7A6CC4',
            },
        },
    },

    plugins: [forms],
};
