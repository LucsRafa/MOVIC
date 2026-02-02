/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './index.html',
    './src/**/*.{vue,js,ts,jsx,tsx}'
  ],
  theme: {
    extend: {
      colors: {
        ink: '#0b0b0b',
        mist: '#f4f1ec',
        ember: '#f0552b',
        ocean: '#0b4d4a'
      }
    }
  },
  plugins: []
}
