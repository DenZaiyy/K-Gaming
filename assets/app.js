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
	
	// FIXME: Faire en sorte de pouvoir changer la quantité dans le panier grace au select sans avoir à recharger la page
	// START QUANTITY CHANGE IN CART
	const quantity = document.querySelectorAll("#qtt");
	const cartId = document.querySelectorAll("#gameCart");
	
	quantity.forEach(function (qtt) {
		const idGame = qtt.getAttribute("data-game");
		const idPlatform = qtt.getAttribute("data-platform");
		
		qtt.addEventListener("change", function () {
			window.location.href = "/cart/add/game/" + idGame + "/platform/" + idPlatform;
		})
		
	})
	// END QUANTITY CHANGE IN CART
})