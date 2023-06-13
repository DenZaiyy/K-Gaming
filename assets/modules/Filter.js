import {Flipper, spring} from "flip-toolkit"

/**
 * @property {HTMLElement} pagination
 * @property {HTMLElement} content
 * @property {HTMLElement} sorting
 * @property {HTMLFormElement} form
 */
export default class Filter {

    /**
     * @param {HTMLElement|null} element
     */
    constructor(element) {
        if (element === null) {
            return
        }
        this.pagination = element.querySelector('.js-filter-pagination')
        this.content = element.querySelector('.js-filter-content')
        this.sorting = element.querySelector('.js-filter-sorting')
        this.form = element.querySelector('.js-filter-form')
        this.bindEvents()
        console.log("test")
    }

    /**
     * Ajoute les comportements aux différents éléments
     */
    bindEvents() {
        const linkClickListeners = e => {
            if (e.target.tagName === 'A') {
                e.preventDefault()
                this.loadUrl(e.target.getAttribute('href'))
            }
        }

        this.sorting.addEventListener('click', linkClickListeners)
        this.pagination.addEventListener('click', linkClickListeners)
        this.form.querySelectorAll('input').forEach(input => {
            input.addEventListener('change', this.loadForm.bind(this))
        })
    }

    async loadForm() {
        const data = new FormData(this.form)
        const url = new URL(this.form.getAttribute('action') || window.location.href)
        const params = new URLSearchParams() //construit les params d'une url dynamiquement

        data.forEach((value, key) => {
            params.append(key, value)
        })

        return this.loadUrl(url.pathname + '?' + params.toString())
    }

    async loadUrl(url) {
        const ajaxUrl = url + '&ajax=1'
        const response = await fetch(ajaxUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        if (response.status >= 200 && response.status < 300) {
            const data = await response.json()
            this.flipContent(data.content)
            this.sorting.innerHTML = data.sorting
            this.pagination.innerHTML = data.pagination
            history.replaceState({}, '', url)
        } else {
            console.error(response)
        }
    }

    /**
     * Remplace les éléments de la grille avec un effet d'animation flip
     * @param {string} content
     */
    flipContent(content) {
        const exitSpring = function (element, index, onComplete) {
            spring({
                config: "wobbly",
                values: {
                    translateY: [0, -20],
                    opacity: [1, 0]
                },
                onUpdate: ({translateY, opacity}) => {
                    element.style.opacity = opacity;
                    element.style.transform = `translateY(${translateY}px)`;
                },
                onComplete
            })
        }

        const flipper = new Flipper({
            element: this.content
        })

        // this.content.children.forEach(element => {
        Array.prototype.forEach.call(this.content.children, (element => {
            flipper.addFlipped({
                element,
                flipId: element.id,
                shouldFlip: false,
                onExit: exitSpring
            })
        }))
        flipper.recordBeforeUpdate()

        this.content.innerHTML = content

        let test = [...this.content.children]

        // this.content.children.forEach(element => {
        test.forEach(element => {
            flipper.addFlipped({
                element,
                flipId: element.id
            })
        })
        flipper.update()
    }
}