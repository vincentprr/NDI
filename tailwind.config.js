/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./dist/**/*.{html,js}"],
  theme: {
    extend: {
      colors: {
        "sis-green-dark": "#109138",
      },

      dropShadow: {
        "clean": [
          "0 5px 2px rgb(0 0 0 / 0.05)",
          "0 5px 4px rgb(0 0 0 / 0.1)"
        ],
        "clean-xl": [
          "0 5px 2px rgb(0 0 0 / 0.05)",
          "0 5px 4px rgb(0 0 0 / 0.2)"
        ]
      },
    },
  },
  plugins: [],
}
