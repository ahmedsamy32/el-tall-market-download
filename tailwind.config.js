module.exports = {
  content: [
    "./*.php",
    "./includes/**/*.php",
    "./admin/**/*.php",
    "./assets/js/**/*.js"
  ],
  darkMode: "class",
  theme: {
    extend: {
      fontFamily: {
        arabic: ["Cairo", "system-ui", "sans-serif"]
      },
      boxShadow: {
        glow: "0 20px 60px rgba(15, 23, 42, 0.18)"
      }
    }
  }
};
