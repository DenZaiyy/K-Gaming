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
			
			window.location.href = "/platform/" + idPlateform + "/" + idGame; // on redirige vers la page avec l'id de la plateforme et du jeu
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

	// START MENU BURGER
	const closeBtn = document.querySelector(".close-btn"); // on récupère l'élément avec la classe closebtn
	const burgerBtn = document.querySelector(".burger-btn"); // on récupère l'élément avec la classe burgerbtn

	closeBtn.addEventListener('click', () => {
		closeNav();
	})

	burgerBtn.addEventListener('click', () => {
		openNav();
	})
	function openNav() {
		document.getElementById("menu-burger").style.width = "100%";
		document.getElementById("menu-burger").style.opacity = "1";
		document.querySelector("body").style.overflow = "hidden";
	}

	function closeNav() {
		document.getElementById("menu-burger").style.width = "0%";
		document.getElementById("menu-burger").style.opacity = "0";
		document.querySelector("body").style.overflow = "unset";
	}
	// END MENU BURGER

	// START SHOW FILTERS IN RESPONSIVE
	const showFilters = document.querySelector(".toggle-filters"); // on récupère l'élément avec la classe show-filters
	const filterContent = document.querySelector(".filter-content"); // on récupère l'élément avec la classe filter-content
	const sortingContent = document.querySelector(".sorting"); // on récupère l'élément avec la classe sorting-content

	if (showFilters) { // si l'élément existe
		showFilters.addEventListener('click', () => { // on écoute l'événement click sur l'élément
			if (filterContent.classList.contains("toggle-display") && sortingContent.classList.contains("toggle-display")) { // si les éléments ont la classe toggle-display
				showFilters.innerHTML = "Masquer les filtres"; // on change le texte de l'élément
			} else { // sinon
				showFilters.innerHTML = "Afficher les filtres"; // on change le texte de l'élément
			}

			filterContent.classList.toggle("toggle-display"); // on ajoute ou on enlève la classe toggle-display à l'élément
			sortingContent.classList.toggle("toggle-display"); // on ajoute ou on enlève la classe toggle-display à l'élément
		})
	}
	// END SHOW FILTERS IN RESPONSIVE

	// START SCREEN SIZE
	let screenWidth = window.screen.width; // on récupère la largeur de l'écran

	let xHttp = new XMLHttpRequest(); // on crée une nouvelle requête
	xHttp.onreadystatechange = function() { // on écoute l'événement readyStateChange de la requête
		if (this.readyState == 4 && this.status == 200) { // si la requête est terminée et que le statut est 200
			let response = JSON.parse(this.responseText); // on parse la réponse en JSON
		}
	};
	xHttp.open('POST', '/screen-size', true); // on ouvre la requête en POST sur l'URL /screen-size
	xHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded"); // on définit le header de la requête
	xHttp.send("screenWidth=" + screenWidth); // on envoie la requête avec la largeur de l'écran
	// END SCREEN SIZE
})