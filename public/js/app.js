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

