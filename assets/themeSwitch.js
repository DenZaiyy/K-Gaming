const checkbox = document.getElementById("switch");
let theme = localStorage.getItem("data-theme");


document.addEventListener("DOMContentLoaded", () => {
	!theme ? theme = "light" : theme
	document.documentElement.setAttribute("data-theme", theme);
})

let trans = () => {
	document.documentElement.classList.add("transition");
	window.setTimeout(() => {
		document.documentElement.classList.remove("transition");
	}, 1000)
}

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

if(checkbox)
{
	checkbox.addEventListener("change", () => {
		checkbox.checked ? changeThemeToDark() : changeThemeToLight();
	})
}
