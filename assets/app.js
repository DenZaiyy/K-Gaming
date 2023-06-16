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

new Filter(document.querySelector('.js-filter'));

window.addEventListener('load', (e) => {
	e.preventDefault()

	// START ALERTS
	const alerts = document.querySelectorAll('[role="alert"]')
	for (const alert of alerts) {
		setTimeout( function() {
			const bootstrapAlert = bootstrap.Alert.getOrCreateInstance(alert);
			bootstrapAlert.close();
		}, 5000);
	}
	// STOP ALERTS

	// START FILTER SLIDER
	const slider = document.getElementById('price-slider');

	if(slider)
	{
		const min = document.getElementById('min');
		const max = document.getElementById('max');

		const minValue = Math.floor(parseInt(slider.dataset.min, 10) / 10) * 10;
		const maxValue = Math.ceil(parseInt(slider.dataset.max, 10) / 10) * 10;

		const range = noUiSlider.create(slider, {
			start: [min.value || minValue, max.value || maxValue],
			connect: true,
			step: 5,
			range: {
				'min': minValue,
				'max': maxValue
			}
		});

		range.on('slide', function(values, handle) {
			if(handle === 0) {
				min.value = Math.round(values[0]);
			}
			if(handle === 1) {
				max.value = Math.round(values[1]);
			}
		})

		range.on('end', function(values, handle) {
			min.dispatchEvent(new Event('change'));
		})
	}
	// STOP FILTER SLIDER

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