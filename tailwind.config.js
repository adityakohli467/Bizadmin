/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./application/**/*.php",
    "./External/application/**/*.php",
    "./theme-assets/**/*.js"
  ],
  safelist: [
    'bg-orange-500',
    'bg-orange-600',
    'hover:bg-orange-600',
    'bg-green-500',
    'bg-green-600',
    'hover:bg-green-600',
    'bg-red-500',
    'bg-red-600',
    'hover:bg-red-600',
    'bg-blue-500',
    'bg-blue-600',
    'hover:bg-blue-600',
    'bg-purple-500',
    'bg-purple-600',
    'hover:bg-purple-600',
    'bg-indigo-500',
    'bg-indigo-600',
    'hover:bg-indigo-600',
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ["Inter", "sans-serif"],
        inter: ["Inter", "sans-serif"]
      },
      colors: {
        // Primary blue shades (from weeklyTimesheet)
        primary: {
          DEFAULT: "#1a2f52",
          50: "#f0f9ff",
          100: "#e0f2fe",
          200: "#bae6fd",
          300: "#7dd3fc",
          400: "#38bdf8",
          500: "#0ea5e9",
          600: "#0284c7",
          700: "#0369a1",
          800: "#075985",
          900: "#0c4a6e"
        },
        // Semantic colors
        success: "#10B981",
        warning: "#F59E0B",
        danger: "#EF4444",
        info: "#3B82F6",
        // Dashboard colors
        teal: "#1D9E75",
        'orange-custom': "#F29A6E",
        neutralgray: "#E7EAF0",
        // Kitchen checklist colors
        accent: "#3b82f6",
        light: "#f8fafc",
        // Onboarding form colors
        navy: {
          DEFAULT: "#1a2f52",
          dark: "#152a45",
          light: "#3d4a6f"
        },
        // Cafe/timesheet colors
        cafe: {
          50: '#fef6ee',
          100: '#fde9d3',
          200: '#fad0a5',
          300: '#f7ae6d',
          400: '#f38333',
          500: '#f06312',
          600: '#e14808',
          700: '#bb3309',
          800: '#95290e',
          900: '#79230f',
        },
        // Additional colors
        magenta: '#B01271',
        // Roster/shift colors
        'orange-primary': '#ff631a',
        'green-primary': '#22b353',
        'sky-primary': '#1e88e5',
        'shift-green': '#e8f5e9',
        'shift-border': '#a5d6a7',
        // Catering colors
        'light-gray': '#F9FAFB',
        background: '#F9FAFB',
        secondary: '#6B7280',
        // Dashboard manager colors
        'teal-dark': '#0F6E56'
      }
    }
  },
  plugins: []
}
