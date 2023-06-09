window.onload = () => {
    const ratingStar = document.querySelector('.ratingStars')
    const rating = document.getElementById('rating_note')
    const label = document.getElementById('labelRating')

    if (ratingStar) {
        for (starsTest of ratingStar.children) {
            starsTest.addEventListener('mouseover', function () {
                resetStars();
                this.style.color = "#f44b3a";
                this.classList.add('bi-star-fill');
                this.classList.remove('bi-star');

                label.innerHTML = this.dataset.label;

                let previousStar = this.previousElementSibling;

                while (previousStar) {
                    previousStar.style.color = "#f44b3a";
                    previousStar.classList.add('bi-star-fill');
                    previousStar.classList.remove('bi-star');
                    previousStar = previousStar.previousElementSibling;
                }
            })

            starsTest.addEventListener('click', function () {
                rating.value = this.dataset.value;
                label.innerHTML = this.dataset.label;
            })

            starsTest.addEventListener('mouseout', function () {
                label.innerHTML = ""
                resetStars(rating.value);
            })
        }
    }

    function resetStars(nb = 0) {
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