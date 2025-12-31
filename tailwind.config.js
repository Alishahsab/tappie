/** @type {import('tailwindcss').Config} */ 
module.exports = { 
  content: [ 
    './**/*.php', 
    './*.php', 
    './**/*.html', 
    './**/*.js', 
  ], 
  theme: { 
   extend: {
                        colors: {
                            primary: "#008FA5",
                            secondary: "#242424",
                            muted: "#666568",
                            bglight: "#F9F9F9",
                            bordergray: "#E9E9F0",
                        },
                        fontFamily: {
                            poppins: ["Poppins", "sans-serif"],
                        
        syne: ['Syne'],
    
                        },
                        fontFamily: {
        sans: ['Poppins', 'sans-serif'],
        poppins: ['Poppins', 'sans-serif'],
      },
                    },
  }, 
  plugins: [], 
} 
