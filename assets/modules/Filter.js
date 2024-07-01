import { Flipper, spring } from "flip-toolkit";

/**
 * @property {HTMLElement} pagination
 * @property {HTMLElement} content
 * @property {HTMLElement} sorting
 * @property {HTMLFormElement} form
 * @property {number} page
 * @property {boolean} moreNav
 */
export default class Filter {
  //export default permet d'exporter la classe pour pouvoir l'importer dans un autre fichier

  /**
   * @param {HTMLElement|null} element
   */
  constructor(element) {
    //constructeur de la classe
    if (element === null) {
      //si l'élément n'existe pas, on sort de la fonction
      return;
    }
    // On récupère les éléments du DOM
    this.pagination = element.querySelector(".js-filter-pagination");
    this.content = element.querySelector(".js-filter-content");
    this.sorting = element.querySelector(".js-filter-sorting");
    this.form = element.querySelector(".js-filter-form");

    this.page = parseInt(
      new URLSearchParams(window.location.search).get("page") || 1,
    );
    this.moreNav = this.page === 1;

    // On lance la fonction init
    this.bindEvents();
  }

  /**
   * Ajoute les comportements aux différents éléments
   */
  bindEvents() {
    const linkClickListeners = (e) => {
      if (e.target.tagName === "A") {
        e.preventDefault();
        this.loadUrl(e.target.getAttribute("href"));
      }
    };

    this.sorting.addEventListener("click", (e) => {
      linkClickListeners(e);
      this.page = 1;
    });
    if (this.moreNav) {
      this.pagination.innerHTML =
        '<button class ="btn btn-primary">Voir plus</button>';
      this.pagination
        .querySelector("button")
        .addEventListener("click", this.loadMore.bind(this));
    } else {
      this.pagination.addEventListener("click", linkClickListeners);
    }
    this.form.querySelectorAll("input").forEach((input) => {
      input.addEventListener("change", this.loadForm.bind(this));
    });
  }

  //async permet de définir une fonction asynchrone
  async loadMore() {
    // loadMore permet de charger plus de résultats
    const button = this.pagination.querySelector("button"); //on récupère le bouton
    button.setAttribute("disabled", "disabled"); //on désactive le bouton
    this.page++; //on incrémente la page
    const url = new URL(window.location.href); //on récupère l'url
    const params = new URLSearchParams(url.search); //on récupère les paramètres de l'url
    params.set("page", this.page); //on modifie le paramètre page qui indique le numéro de la page récupéré dans l'url
    await this.loadUrl(url.pathname + "?" + params.toString(), true); //on appelle la fonction loadUrl avec l'url modifiée
    button.removeAttribute("disabled"); //on réactive le bouton
  }

  async loadForm() {
    // loadForm permet de charger les données du formulaire
    this.page = 1; //on remet la page à 1
    const data = new FormData(this.form); //on récupère les données du formulaire
    const url = new URL(
      this.form.getAttribute("action") || window.location.href,
    ); //on récupère l'url
    const params = new URLSearchParams(); //construit les params d'une url dynamiquement

    data.forEach((value, key) => {
      //on boucle sur les données du formulaire
      params.append(key, value); //on ajoute les données au params
    });

    return this.loadUrl(url.pathname + "?" + params.toString()); //on appelle la fonction loadUrl avec l'url modifiée
  }

  async loadUrl(url, append = false) {
    // loadUrl permet de charger les données de l'url
    this.showLoader(); //on appelle la fonction showLoader pour afficher le loader pendant le chargement des données

    const params = new URLSearchParams(url.split("?")[1] || ""); //on récupère les paramètres de l'url
    params.set("ajax", 1); //on ajoute le paramètre ajax à l'url

    const response = await fetch(url.split("?")[0] + "?" + params.toString(), {
      //on appelle l'url avec les paramètres
      headers: {
        //on définit les headers
        "X-Requested-With": "XMLHttpRequest", //XMLHttpRequest permet de faire des requêtes ajax en javascript
      },
    });

    // Si la réponse est ok, on récupère les données et on les affiche
    if (response.status >= 200 && response.status < 300) {
      //status: 200 signifie que la requête a réussi, 300 signifie que la requête a été redirigée vers une autre ressource

      const data = await response.json(); //on récupère les données de la réponse
      this.flipContent(data.content, append); //on appelle la fonction flipContent pour afficher les données avec un effet d'animation flip
      this.sorting.innerHTML = data.sorting; //on affiche les données de tri
      if (!this.moreNav) {
        //si on n'est pas sur la page d'accueil
        this.pagination.innerHTML = data.pagination; //on affiche la pagination
      } else if (this.page === data.pages) {
        //si on est sur la dernière page
        this.pagination.style.display = "none"; //on cache la pagination
      } else {
        //sinon
        this.pagination.style.display = null; //on affiche la pagination
      }
      this.updatePrices(data); //on appelle la fonction updatePrices pour mettre à jour les prix
      params.delete("ajax"); //on supprime le paramètre ajax de l'url
      history.replaceState({}, "", url.split("?")[0] + "?" + params.toString()); //on remplace l'url dans l'historique
    } else {
      //sinon on affiche une erreur dans la console
      console.error(response);
    }
    this.hideLoader(); //on appelle la fonction hideLoader pour cacher le loader après le chargement des données
  }

  /**
   * Remplace les éléments de la grille avec un effet d'animation flip
   * @param {string} content
   */
  flipContent(content, append = false) {
    // flipContent permet de remplacer les éléments de la grille avec un effet d'animation flip et d'ajouter les nouveaux éléments à la suite
    const springConfig = "gentle"; //on définit la config de l'animation
    const exitSpring = function (element, index, onComplete) {
      //on définit l'animation de sortie
      spring({
        //on appelle la fonction spring de la librairie spring
        config: "stiff", //on définit la config de l'animation
        values: {
          //on définit les valeurs de l'animation
          translateY: [0, -20],
          opacity: [1, 0],
        },
        onUpdate: ({ translateY, opacity }) => {
          //on définit les valeurs de l'animation
          element.style.opacity = opacity; //on définit l'opacité de l'élément
          element.style.transform = `translateY(${translateY}px)`; //on définit la translation de l'élément
        },
        onComplete, //on appelle la fonction onComplete
      });
    };
    const appearSpring = function (element, index) {
      //on définit l'animation d'apparition
      spring({
        //on appelle la fonction spring de la librairie spring
        config: "stiff",
        values: {
          translateY: [20, 0],
          opacity: [0, 1],
        },
        onUpdate: ({ translateY, opacity }) => {
          element.style.opacity = opacity;
          element.style.transform = `translateY(${translateY}px)`;
        },
        delay: index * 15, //on définit le délai de l'animation
      });
    };

    const flipper = new Flipper({
      //on appelle la fonction Flipper de la librairie flipjs
      element: this.content, //on définit l'élément sur lequel on applique l'animation
    });

    // this.content.children.forEach(element => {
    Array.prototype.forEach.call(this.content.children, (element) => {
      //on boucle sur les éléments de la grille pour appliquer l'animation de sortie sur les éléments existants avant de les supprimer de la grille et d'ajouter les nouveaux éléments
      flipper.addFlipped({
        element,
        spring: springConfig,
        flipId: element.id,
        shouldFlip: false,
        onExit: exitSpring,
      });
    });
    flipper.recordBeforeUpdate(); //on enregistre l'état de la grille avant l'animation

    if (append) {
      //si on veut ajouter les nouveaux éléments à la suite
      this.content.innerHTML += content; //on ajoute les nouveaux éléments à la suite
    } else {
      this.content.innerHTML = content; //sinon on remplace les éléments existants par les nouveaux éléments
    }

    let test = [...this.content.children]; //on récupère les nouveaux éléments

    // this.content.children.forEach(element => {
    test.forEach((element) => {
      //on boucle sur les nouveaux éléments pour appliquer l'animation d'apparition
      flipper.addFlipped({
        element,
        spring: springConfig,
        flipId: element.id,
        onAppear: appearSpring,
      });
    });
    flipper.update(); //on met à jour l'état de la grille après l'animation
  }

  showLoader() {
    //showLoader permet d'afficher le loader pendant le chargement des données de la requête ajax
    this.form.classList.add("is-loading"); //on ajoute la classe is-loading au formulaire
    const loader = this.form.querySelector(".js-loading"); //on récupère le loader

    if (loader === null) {
      //si le loader n'existe pas, on sort de la fonction
      return;
    }

    loader.setAttribute("aria-hidden", "false"); //on affiche le loader
    loader.style.display = null; //on affiche le loader
  }

  hideLoader() {
    //hideLoader permet de cacher le loader après le chargement des données de la requête ajax
    this.form.classList.remove("is-loading"); //on supprime la classe is-loading du formulaire
    const loader = this.form.querySelector(".js-loading");

    if (loader === null) {
      return;
    }

    loader.setAttribute("aria-hidden", "true"); //on cache le loader
    loader.style.display = "none"; //on cache le loader
  }

  updatePrices({ min, max }) {
    //updatePrices permet de mettre à jour les prix dans le slider après le chargement des données de la requête ajax pour que le slider s'adapte aux nouveaux prix des produits affichés
    const slider = document.getElementById("price-slider"); //on récupère le slider

    if (slider === null) {
      return;
    }

    slider.noUiSlider.updateOptions({
      //on met à jour les options du slider
      range: {
        min: [min], //on définit la valeur minimale du slider
        max: [max], //on définit la valeur maximale du slider
      },
    });
  }
}
