const checkbox = document.getElementById("switch"); // checkbox element
let theme = localStorage.getItem("data-theme"); // get theme from local storage

document.addEventListener("DOMContentLoaded", () => {
  // on page load
  !theme ? (theme = "light") : theme; // if theme is not set, set theme to light
  document.documentElement.setAttribute("data-theme", theme); // set theme attribute to theme
});

let trans = () => {
  // transition function
  document.documentElement.classList.add("transition"); // add transition class to html element
  window.setTimeout(() => {
    // wait 1 second
    document.documentElement.classList.remove("transition"); // remove transition class from html element
  }, 1000);
};

const changeThemeToDark = () => {
  // change theme to dark
  document.documentElement.setAttribute("data-theme", "dark"); // set theme attribute to dark
  localStorage.setItem("data-theme", "dark"); // set theme in local storage to dark
  trans(); // call transition function
};

const changeThemeToLight = () => {
  // change theme to light
  document.documentElement.setAttribute("data-theme", "light"); // set theme attribute to light
  localStorage.setItem("data-theme", "light"); // set theme in local storage to light
  trans(); // call transition function
};

if (checkbox) {
  // if checkbox exists
  checkbox.addEventListener("change", () => {
    // on checkbox change
    checkbox.checked ? changeThemeToDark() : changeThemeToLight(); // if checkbox is checked, change theme to dark, else change theme to light
  });
}
