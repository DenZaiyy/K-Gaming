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
import './carousel';
import './themeSwitch';

$(document).ready(function () {
	
	// START PLATEFORME CHANGE
	const plateform = document.getElementById("plateform");
	
	if (plateform) {
		plateform.addEventListener("change", function () {
			var idPlateform = plateform.value;
			var url = window.location.pathname;
			var idGame = url.substring(url.lastIndexOf("/") + 1);
			
			window.location.href = "/platform/" + idPlateform + "/game/" + idGame;
		})
	}
	// END PLATEFORME CHANGE
})