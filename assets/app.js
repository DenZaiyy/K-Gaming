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
import './themeSwitch';
import './rating';

window.addEventListener('load', (e) => {
	e.preventDefault()

	// START ALERTS
	const alerts = document.querySelectorAll('[class*="alert-"]')
	for (const alert of alerts) {
		setTimeout( function() {
			const bootstrapAlert = bootstrap.Alert.getOrCreateInstance(alert);
			bootstrapAlert.close();
		}, 2000);
	}
	// STOP ALERTS

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
	
	// START QUANTITY CHANGE IN CART
	const gameCart= document.querySelectorAll("#gameCart");

	gameCart.forEach(function (game) {
		const id = game.getAttribute("data-id");
		const qtt = game.querySelector(".qtt");

		qtt.addEventListener("change", function () {
			window.location.href = "/cart/quantityChange/" + id + "/" + qtt.value;
		})
	})
	// END QUANTITY CHANGE IN CART
})