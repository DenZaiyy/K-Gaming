window.onload = () => {
    const ratingStar = document.querySelector('.ratingStars') // Récupération de la div contenant les étoiles
    const rating = document.getElementById('rating_note') // Récupération de l'input caché
    const label = document.getElementById('labelRating') // Récupération du label

    if (ratingStar) {
        for (starsTest of ratingStar.children) { // Pour chaque étoile
            starsTest.addEventListener('mouseover', function () { // Lorsque la souris passe sur une étoile
                resetStars(); // On reset les étoiles
                this.style.color = "#f44b3a"; // On change la couleur de l'étoile survolée
                this.classList.add('bi-star-fill'); // On ajoute la classe bi-star-fill
                this.classList.remove('bi-star'); // On retire la classe bi-star

                label.innerHTML = this.dataset.label; // On change le label

                let previousStar = this.previousElementSibling; // On récupère l'étoile précédente

                while (previousStar) { // Tant qu'il y a une étoile précédente
                    previousStar.style.color = "#f44b3a";
                    previousStar.classList.add('bi-star-fill');
                    previousStar.classList.remove('bi-star');
                    previousStar = previousStar.previousElementSibling;
                }
            })

            starsTest.addEventListener('click', function () { // Lorsque l'on clique sur une étoile
                rating.value = this.dataset.value; // On change la valeur de l'input caché
                label.innerHTML = this.dataset.label; // On change le label
            })

            starsTest.addEventListener('mouseout', function () { // Lorsque la souris quitte une étoile
                label.innerHTML = "" // On retire le label
                resetStars(rating.value); // On reset les étoiles avec la valeur de l'input caché
            })
        }
    }

    // Fonction permettant de reset les étoiles à leur état initial
    function resetStars(nb = 0) { // Par défaut, on met 0
        for (starsTest of ratingStar.children) {
            if (starsTest.dataset.value > nb) {
                starsTest.style.color = "black";
                starsTest.classList.add('bi-star');
                starsTest.classList.remove('bi-star-fill');
            } else {
                starsTest.style.color = "#f44b3a";
                starsTest.classList.add('bi-star-fill');
                starsTest.classList.remove('bi-star');
                label.innerHTML = starsTest.dataset.label;
            }
        }
    }
}