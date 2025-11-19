import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import daisyui from 'daisyui';

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
        sans: ['Figtree', ...defaultTheme.fontFamily.sans],
      },
    },
  },
  safelist: [
    // Teal utilities used in dashboard cards
    'text-teal-600',
    'text-teal-700',
    'border-teal-100',
    'bg-teal-50',
    'from-teal-400',
    'to-teal-500',
    'shadow-teal-500/20',
    // Pink utilities used in dashboard cards
    'text-pink-600',
    'text-pink-700',
    'border-pink-100',
    'bg-pink-50',
    'from-pink-400',
    'to-pink-500',
    'shadow-pink-500/20',
  ],
  plugins: [forms, daisyui],
};
