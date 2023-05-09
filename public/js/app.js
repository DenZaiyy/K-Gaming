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
