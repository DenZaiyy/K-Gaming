$(document).ready(function () {
	$(".dropdown").each(function () {
		var $dropdown = $(this);
		
		$($dropdown).on("click", function (e) {
			e.preventDefault();
			
			var $dropdownMenu = $('.dropdown-menu', $dropdown);
			var $dropDownBtn = $('.dropBtn', $dropdown);
			var $icon = $dropDownBtn.find(".bi");
			
			if ($icon.hasClass("bi-chevron-down")) {
				$icon.removeClass("bi-chevron-down").addClass("bi-chevron-up");
			} else if ($icon.hasClass("bi-chevron-up")) {
				$icon.removeClass("bi-chevron-up").addClass("bi-chevron-down");
			}
			
			// Collapse other dropdown menus and reset their icons
			$(".dropdown-menu.show").not($dropdownMenu).removeClass("show").slideUp(200, function () {
				$(this).prev(".dropBtn").find(".bi").removeClass("bi-chevron-up").addClass("bi-chevron-down");
			});
			
			$dropdownMenu.slideToggle(200, function() {
				$dropdownMenu.toggleClass("show");
			});
		});
	});
});