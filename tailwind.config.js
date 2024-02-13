/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      container: {
        center: true,
      },
      colors: {
        primary: "#16a34a",
        secondary: "#71717a",
        tertiary: "#F2994A",
        dark: "#0f172a",
        gray1: "#333333",
        gray2: "#4F4F4F",
        gray3: "#828282",
        gray4: "#BDBDBD",
        gray5: "#E0E0E0",
        gray6: "#F2F2F2",
      },
      animation: {
        'spin-slow': 'spin 3s linear infinite',
        'ping-slow': 'ping 3s linear infinite',
        'bounce-slow': 'bounce 3s linear infinite',
      }
    },
  },
  plugins: [],
}