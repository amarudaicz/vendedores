/** @type {import('tailwindcss').Config} */
import { addDynamicIconSelectors } from "@iconify/tailwind";

export const content = ["./src/**/*.{html,ts}"];
export const theme = {
  extend: {
    colors: {
      primary: "#4f46e5",
    },
    keyframes: {
      fadein: {
        "0%": { opacity: 0 },
        "100%": { opacity: 1 },
      },
      slideInLeft: {
        "0%": { transform: "translateX(-100%)" },
        "100%": { transform: "translateX(0)" },
      },
      slideOutLeft: {
        "0%": { transform: "translateX(0)" },
        "100%": { transform: "translateX(-100%)" },
      },
      scaleIn: {
        "0%": { transform: "scale(0.1)"},
        "100%": { transform: "scale(1)" },
      },
    },
    animation: {
      fadeIn: "fadein 0.5s ease-in-out",
      slideInLeft: "slideInLeft 0.3s ease-out forwards",
      slideOutLeft: "slideOutLeft 0.3s ease-out forwards",
      scaleIn: "scaleIn 0.4s ease-in-out forwards",
    },
  },
};

export const plugins = [addDynamicIconSelectors()];
