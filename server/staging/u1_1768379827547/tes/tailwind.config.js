export default {
  // Tailwind v3+ tidak lagi menggunakan properti purge; gunakan content.
  darkMode: 'class', // Mengaktifkan dark mode berbasis class .dark pada <html>
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],
  theme: {
    extend: {
      colors: {
        'v4-d-background': '#1a1a1a',
        'v4-d-surface': '#2d2d2d',
        'v4-d-border': '#374151',
        'v4-d-text': '#e5e7eb'
      }
    },
  },
  variants: {
    extend: {},
  },
  plugins: [],
}
