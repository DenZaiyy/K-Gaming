const checkbox = document.getElementById("switch");
let theme = localStorage.getItem("data-theme");


document.addEventListener("DOMContentLoaded", () => {
	!theme ? theme = "light" : null
	document.documentElement.setAttribute("data-theme", theme);
})

let trans = () => {
	document.documentElement.classList.add("transition");
	window.setTimeout(() => {
		document.documentElement.classList.remove("transition");
	}, 1000)
}

checkbox.checked = theme === "dark";

const changeThemeToDark = () => {
	document.documentElement.setAttribute("data-theme", "dark");
	localStorage.setItem("data-theme", "dark");
	trans();
}

const changeThemeToLight = () => {
	document.documentElement.setAttribute("data-theme", "light");
	localStorage.setItem("data-theme", "light");
	trans();
}

checkbox.addEventListener("change", () => {
	checkbox.checked ? changeThemeToDark() : changeThemeToLight();
})