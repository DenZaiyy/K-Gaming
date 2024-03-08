/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import "./styles/style.css";

// start the Stimulus application
import "./bootstrap";
import "./themeSwitch";
import "./rating";
import "./tarteaucitron";
import "./screenWidth";

import noUiSlider from "nouislider";
import "nouislider/dist/nouislider.css";
import Filter from "./modules/Filter";

new Filter(document.querySelector(".js-filter")); // on instancie la classe Filter avec le formulaire de filtre en paramètre (voir modules\Filter.js)

window.addEventListener("load", (e) => {
  const lang = document.querySelector("html").lang; // on récupère la valeur de l'attribut lang de la balise html
  e.preventDefault(); // on empêche le comportement par défaut du navigateur (rechargement de la page)

  // START ALERTS
  const alerts = document.querySelectorAll('[role="alert"]'); // on récupère tous les éléments avec l'attribut role="alert"
  for (const alert of alerts) {
    // pour chaque élément
    setTimeout(function () {
      // on attend 5 secondes
      const bootstrapAlert = bootstrap.Alert.getOrCreateInstance(alert);
      bootstrapAlert.close(); // on ferme l'alerte
    }, 5000);
  }
  // STOP ALERTS

  // START FILTER SLIDER
  const slider = document.getElementById("price-slider"); // on récupère l'élément avec l'id price-slider

  if (slider) {
    // si l'élément existe
    const min = document.getElementById("min"); // on récupère l'élément avec l'id min
    const max = document.getElementById("max"); // on récupère l'élément avec l'id max

    const minValue = Math.floor(parseInt(slider.dataset.min, 10) / 10) * 10; // on récupère la valeur minimale du slider
    const maxValue = Math.ceil(parseInt(slider.dataset.max, 10) / 10) * 10; // on récupère la valeur maximale du slider

    const range = noUiSlider.create(slider, {
      // on crée le slider
      start: [min.value || minValue, max.value || maxValue], // on définit les valeurs de départ
      connect: true, // on relie les deux poignées
      step: 5, // on définit le pas
      range: {
        // on définit les valeurs minimales et maximales
        min: minValue,
        max: maxValue,
      },
    });

    range.on("slide", function (values, handle) {
      // on écoute l'événement slide du slider (quand on bouge une poignée) et on récupère les valeurs et la poignée concernée
      if (handle === 0) {
        // si la poignée concernée est la première
        min.value = Math.round(values[0]); // on arrondit la valeur de la poignée et on l'assigne à l'élément avec l'id min
      }
      if (handle === 1) {
        // si la poignée concernée est la deuxième
        max.value = Math.round(values[1]); // on arrondit la valeur de la poignée et on l'assigne à l'élément avec l'id max
      }
    });

    range.on("end", function (values, handle) {
      // on écoute l'événement end du slider (quand on relâche une poignée) et on récupère les valeurs et la poignée concernée
      min.dispatchEvent(new Event("change")); // on déclenche l'événement change sur l'élément avec l'id min
    });
  }
  // STOP FILTER SLIDER

  // START PLATEFORME CHANGE
  const plateform = document.getElementById("plateform"); // on récupère l'élément avec l'id plateform

  if (plateform) {
    // si l'élément existe
    plateform.addEventListener("change", function (e) {
      // on écoute l'événement change sur l'élément
      let idPlateform = plateform.value; // on récupère la valeur de l'élément
      let category = e.target.options[e.target.selectedIndex].dataset.category; // on récupère la valeur de l'attribut data-category de l'option sélectionnée
      let url = window.location.pathname; // on récupère l'url de la page
      let idGame = url.substring(url.lastIndexOf("/") + 1); // on récupère l'id du jeu dans l'url

      window.location.href =
        "/" + lang + "/platform/" + category + "/" + idPlateform + "/" + idGame; // on redirige vers la page avec l'id de la plateforme et du jeu
    });
  }
  // END PLATEFORME CHANGE

  // START QUANTITY CHANGE IN CART
  const gameCart = document.querySelectorAll("#gameCart"); // on récupère tous les éléments avec l'id gameCart

  gameCart.forEach(function (game) {
    // pour chaque élément
    const id = game.getAttribute("data-id"); // on récupère l'attribut data-id
    const qtt = game.querySelector(".qtt"); // on récupère l'élément avec la classe qtt

    qtt.addEventListener("change", function () {
      // on écoute l'événement change sur l'élément
      window.location.href =
        "/" + lang + "/cart/quantityChange/" + id + "/" + qtt.value; // on redirige vers la page avec l'id du jeu et la quantité
    });
  });
  // END QUANTITY CHANGE IN CART

  // START MENU BURGER
  const closeBtn = document.querySelector(".close-btn"); // on récupère l'élément avec la classe closebtn
  const burgerBtn = document.querySelector(".burger-btn"); // on récupère l'élément avec la classe burgerbtn

  closeBtn.addEventListener("click", () => {
    closeNav();
  });

  burgerBtn.addEventListener("click", () => {
    openNav();
  });
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

  if (showFilters) {
    // si l'élément existe
    showFilters.addEventListener("click", () => {
      // on écoute l'événement click sur l'élément
      if (
        filterContent.classList.contains("toggle-display") &&
        sortingContent.classList.contains("toggle-display")
      ) {
        // si les éléments ont la classe toggle-display
        showFilters.innerHTML = "Masquer les filtres"; // on change le texte de l'élément
      } else {
        // sinon
        showFilters.innerHTML = "Afficher les filtres"; // on change le texte de l'élément
      }

      filterContent.classList.toggle("toggle-display"); // on ajoute ou on enlève la classe toggle-display à l'élément
      sortingContent.classList.toggle("toggle-display"); // on ajoute ou on enlève la classe toggle-display à l'élément
    });
  }
  // END SHOW FILTERS IN RESPONSIVE

  //START ACCORDION MENU BURGER
  let acc = document.getElementsByClassName("accordion"); // on récupère tous les éléments avec la classe accordion
  let i; // on crée une variable i

  for (i = 0; i < acc.length; i++) {
    acc[i].addEventListener("click", function () {
      // on écoute l'événement click sur l'élément
      this.classList.toggle("active"); // on ajoute ou on enlève la classe active à l'élément
      let panel = this.nextElementSibling; // on récupère le prochain élément
      if (panel.style.maxHeight) {
        // si l'élément a une hauteur max
        panel.style.maxHeight = null; // on enlève la hauteur max
      } else {
        // sinon
        panel.style.maxHeight = panel.scrollHeight + "px"; // on ajoute la hauteur max
      }
    });
  }
  //END ACCORDION MENU BURGER

  // START SHOW PASSWORD
  const password = document.getElementById("inputPassword"); // on récupère l'élément avec l'id password
  const eye = document.getElementById("eye"); // on récupère l'élément avec l'id eye

  if (eye) {
    // si l'élément existe
    eye.addEventListener("click", function () {
      // on écoute l'événement click sur l'élément
      this.classList.toggle("fa-eye"); // on ajoute ou on enlève la classe fa-eye à l'élément
      this.classList.toggle("fa-eye-slash"); // on ajoute ou on enlève la classe fa-eye-slash à l'élément
      const type =
        password.getAttribute("type") === "password" ? "text" : "password"; // on récupère le type de l'élément
      password.setAttribute("type", type); // on change le type de l'élément
    });
  }
  // END SHOW PASSWORD

  // START USER ADDRESS SELECT
  const selectAddress = document.getElementById("user_address");
  const editAddress = document.getElementById("edit-user_address");
  const deleteAddress = document.getElementById("delete-user_address");

  if (selectAddress) {
    selectAddress.addEventListener("change", function () {
      if (selectAddress.value !== "default") {
        editAddress.classList.contains("d-none")
          ? editAddress.classList.remove("d-none")
          : null;
        deleteAddress.classList.contains("d-none")
          ? deleteAddress.classList.remove("d-none")
          : null;
        editAddress.setAttribute(
          "href",
          "/profil/edit-address/" + selectAddress.value,
        );
        deleteAddress.setAttribute(
          "href",
          "/profil/delete-address/" + selectAddress.value,
        );
      } else {
        editAddress.removeAttribute("href");
        deleteAddress.removeAttribute("href");
        editAddress.classList.add("d-none");
        deleteAddress.classList.add("d-none");
      }
    });
  }
  // END USER ADDRESS SELECT

  // START SCROLL TO TOP BUTTON
  const goUp = document.querySelector(".goUp"); // on récupère l'élément qui permet de nous ramener en haut de la page

  window.addEventListener("scroll", function () {
    const scrollHeight = window.pageYOffset;

    if (scrollHeight > 500) {
      goUp.classList.add("show-up");
    } else {
      goUp.classList.remove("show-up");
    }
  });
  // END SCROLL TO TOP BUTTON

  // START HERO IMAGE AUTO HEIGHT

  function setHeroImageHeight() {
    const heroImage = document.querySelector(".hero-image");

    if (heroImage) {
      const heroText = document.querySelector(".hero-text");
      const heroTextHeight = heroText.offsetHeight;

      let size = heroTextHeight + 100;
      heroImage.style.height = size + "px";
    }
  }

  setHeroImageHeight();

  window.addEventListener("resize", setHeroImageHeight);
  // END HERO IMAGE AUTO HEIGHT
});
