document.addEventListener('DOMContentLoaded', () => {
	const setTime = setInterval(() => {
		if (
			window.prestashop &&
			window.prestashop.component &&
			window.prestashop.component.ChoiceTree
		) {
			clearInterval(setTime)
			new window.prestashop.component.ChoiceTree(
				'#configuration_form_categories'
			)
		}
	}, 500)
})
