const carousel = document.querySelectorAll(".carousel");
if (carousel) {
	let count = 0
	let isDragging = false, prevPageX, prevScrollLeft;
	
	carousel.forEach(car => {
		
		const dragStart = (e) => {
			isDragging = true;
			prevPageX = e.pageX;
			prevScrollLeft = car.scrollLeft;
		}
		
		const dragging = (e) => {
			if (!isDragging) return;
			e.preventDefault();
			let posDiff = e.pageX - prevPageX;
			car.scrollLeft = prevScrollLeft - posDiff;
		}
		
		const dragStop = () => {
			isDragging = false;
		}
		
		car.addEventListener("mousedown", dragStart);
		car.addEventListener("mousemove", dragging);
		car.addEventListener("mouseup", dragStop);
		
		const items = car.querySelectorAll(".carousel-item");
		const buttonsHtml = Array.from(items).map(() => {
			return `<span class="carousel-button"></span>`;
		});
		
		const nbImages = items.length;
		
		function nextSlide() {
			items[count].classList.remove("carousel-item__selected");
			buttons[count].classList.remove("carousel-button__selected");
			count++;
			
			if (count > nbImages - 1) {
				count = 0;
			}
			
			items[count].classList.add("carousel-item__selected");
			buttons[count].classList.add("carousel-button__selected");
		}
		
		function prevSlide() {
			items[count].classList.remove("carousel-item__selected");
			buttons[count].classList.remove("carousel-button__selected");
			count--;
			
			if (count < 0) {
				count = nbImages - 1;
			}
			
			items[count].classList.add("carousel-item__selected");
			buttons[count].classList.add("carousel-button__selected");
		}
		
		document.addEventListener("keydown", (e) => {
			if (e.key === "ArrowLeft") {
				prevSlide()
			} else if (e.key === "ArrowRight") {
				nextSlide()
			}
		})
		
		car.insertAdjacentHTML("beforeend", `
				<div class="carousel-nav w-100">
					${buttonsHtml.join("")}
				</div>
			`);
		
		const buttons = car.querySelectorAll(".carousel-button");
		
		buttons.forEach((button, i) => {
			button.addEventListener("click", () => {
				// unselect all items
				items.forEach(item => item.classList.remove("carousel-item__selected"));
				buttons.forEach(button => button.classList.remove("carousel-button__selected"));
				
				items[i].classList.add("carousel-item__selected");
				button.classList.add("carousel-button__selected");
			})
		})
		
		items[0].classList.add("carousel-item__selected");
		buttons[0].classList.add("carousel-button__selected");
	})
}