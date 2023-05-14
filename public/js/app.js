let trans = () => {
	document.documentElement.classList.add("transition");
	window.setTimeout(() => {
		document.documentElement.classList.remove("transition");
	}, 1000)
}

$(document).ready(function () {
	const $checkbox = $("#switch");
	let $theme = localStorage.getItem("data-theme");
	
	document.documentElement.setAttribute("data-theme", $theme);
	
	if($theme == "dark") {
		$checkbox.prop('checked', true)
	} else {
		$checkbox.prop('checked', false)
	}
	
	const $changeThemeToDark = () => {
		document.documentElement.setAttribute("data-theme", "dark");
		localStorage.setItem("data-theme", "dark");
        trans();
	}
	
	const $changeThemeToLight = () => {
		document.documentElement.setAttribute("data-theme", "light");
		trans();
		localStorage.setItem("data-theme", "light");
	}
	
	$checkbox.change(function () {
		if (this.checked) {
			$changeThemeToDark()
			$checkbox.prop('checked', true)
		} else {
			$changeThemeToLight()
			$checkbox.prop('checked', false)
		}
	})
})

/*
$(document).ready(function() {
	$(".dropdown").each(function() {
		var $dropdown = $(this);

		$($dropdown).on("click", function(event) {
			event.stopPropagation();
			var $dropdownMenu = $('.dropdown-menu', $dropdown);
			var $dropDownBtn = $('.bi', $dropdown);

			if ($($dropDownBtn).hasClass("bi-chevron-down")) {
				$($dropDownBtn).removeClass("bi-chevron-down");
				$($dropDownBtn).addClass("bi-chevron-up");
			} else if ($($dropDownBtn).hasClass("bi-chevron-up")) {
				$($dropDownBtn).removeClass("bi-chevron-up");
				$($dropDownBtn).addClass("bi-chevron-down");
			}

			if ($($dropdownMenu).hasClass("show")) {
				$($dropdownMenu).removeClass("show");
			} else {
				$($dropdownMenu).addClass("show");
			}
		});
	});

	// Close dropdown menus on click outside
	$(document).on("click", function() {
		$(".dropdown-menu").removeClass("show");
		$(".bi").removeClass("bi-chevron-up").addClass("bi-chevron-down");
	});
});*/
