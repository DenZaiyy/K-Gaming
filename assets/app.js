/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/style.css';

// start the Stimulus application
import './bootstrap';


let trans = () => {
	document.documentElement.classList.add("transition");
	window.setTimeout(() => {
		document.documentElement.classList.remove("transition");
	}, 1000)
}

$(document).ready(function () {
	// START THEME SWITCHER
	const $checkbox = $("#switch");
	let $theme = localStorage.getItem("data-theme");
	
	document.documentElement.setAttribute("data-theme", $theme);
	
	if ($theme === "dark") {
		$checkbox.prop('checked', true)
	} else {
		$checkbox.prop('checked', false)
	}
	
	const $changeThemeToDark = () => {
		document.documentElement.setAttribute("data-theme", "dark");
		localStorage.setItem("data-theme", "dark");
		trans();
	}
	
	const $changeThemeToLight = () => {
		document.documentElement.setAttribute("data-theme", "light");
		trans();
		localStorage.setItem("data-theme", "light");
	}
	
	$checkbox.change(function () {
		if (this.checked) {
			$changeThemeToDark()
			$checkbox.prop('checked', true)
		} else {
			$changeThemeToLight()
			$checkbox.prop('checked', false)
		}
	})
	// END THEME SWITCHER
})