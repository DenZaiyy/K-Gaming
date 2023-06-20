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

import noUiSlider from 'nouislider';
import 'nouislider/dist/nouislider.css';
import Filter from './modules/Filter'

new Filter(document.querySelector('.js-filter')); // on instancie la classe Filter avec le formulaire de filtre en paramètre (voir modules\Filter.js)

window.addEventListener('load', (e) => {
	e.preventDefault() // on empêche le comportement par défaut du navigateur (rechargement de la page)

	// START ALERTS
	const alerts = document.querySelectorAll('[role="alert"]') // on récupère tous les éléments avec l'attribut role="alert"
	for (const alert of alerts) { // pour chaque élément
		setTimeout( function() { // on attend 5 secondes
			const bootstrapAlert = bootstrap.Alert.getOrCreateInstance(alert);
			bootstrapAlert.close(); // on ferme l'alerte
		}, 5000);
	}
	// STOP ALERTS

	// START FILTER SLIDER
	const slider = document.getElementById('price-slider'); // on récupère l'élément avec l'id price-slider

	if(slider) // si l'élément existe
	{
		const min = document.getElementById('min'); // on récupère l'élément avec l'id min
		const max = document.getElementById('max'); // on récupère l'élément avec l'id max

		const minValue = Math.floor(parseInt(slider.dataset.min, 10) / 10) * 10; // on récupère la valeur minimale du slider
		const maxValue = Math.ceil(parseInt(slider.dataset.max, 10) / 10) * 10; // on récupère la valeur maximale du slider

		const range = noUiSlider.create(slider, { // on crée le slider
			start: [min.value || minValue, max.value || maxValue], // on définit les valeurs de départ
			connect: true, // on relie les deux poignées
			step: 5, // on définit le pas
			range: { // on définit les valeurs minimales et maximales
				'min': minValue,
				'max': maxValue
			}
		});

		range.on('slide', function(values, handle) { // on écoute l'événement slide du slider (quand on bouge une poignée) et on récupère les valeurs et la poignée concernée
			if(handle === 0) { // si la poignée concernée est la première
				min.value = Math.round(values[0]); // on arrondit la valeur de la poignée et on l'assigne à l'élément avec l'id min
			}
			if(handle === 1) { // si la poignée concernée est la deuxième
				max.value = Math.round(values[1]); // on arrondit la valeur de la poignée et on l'assigne à l'élément avec l'id max
			}
		})

		range.on('end', function(values, handle) { // on écoute l'événement end du slider (quand on relâche une poignée) et on récupère les valeurs et la poignée concernée
			min.dispatchEvent(new Event('change')); // on déclenche l'événement change sur l'élément avec l'id min
		})
	}
	// STOP FILTER SLIDER

	// START PLATEFORME CHANGE
	const plateform = document.getElementById("plateform"); // on récupère l'élément avec l'id plateform
	
	if (plateform) { // si l'élément existe
		plateform.addEventListener("change", function () { // on écoute l'événement change sur l'élément
			var idPlateform = plateform.value; // on récupère la valeur de l'élément
			var url = window.location.pathname; // on récupère l'url de la page
			var idGame = url.substring(url.lastIndexOf("/") + 1); // on récupère l'id du jeu dans l'url
			
			window.location.href = "/platform/" + idPlateform + "/game/" + idGame; // on redirige vers la page avec l'id de la plateforme et du jeu
		})
	}
	// END PLATEFORME CHANGE
	
	// START QUANTITY CHANGE IN CART
	const gameCart= document.querySelectorAll("#gameCart"); // on récupère tous les éléments avec l'id gameCart

	gameCart.forEach(function (game) { // pour chaque élément
		const id = game.getAttribute("data-id"); // on récupère l'attribut data-id
		const qtt = game.querySelector(".qtt"); // on récupère l'élément avec la classe qtt

		qtt.addEventListener("change", function () { // on écoute l'événement change sur l'élément
			window.location.href = "/cart/quantityChange/" + id + "/" + qtt.value; // on redirige vers la page avec l'id du jeu et la quantité
		})
	})
	// END QUANTITY CHANGE IN CART
})