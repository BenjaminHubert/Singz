(function() {
	[].slice.call(document.querySelectorAll('.menu')).forEach(function(menu) {
		var menuItems = menu.querySelectorAll('.menu__link'),
			setCurrent = function(ev) {

				var item = ev.target.parentNode; // li

				// return if already current
				if (classie.has(item, 'menu__item--current')) {
					return false;
				}
				// remove current
				classie.remove(menu.querySelector('.menu__item--current'), 'menu__item--current');
				// set current
				classie.add(item, 'menu__item--current');
			};

		[].slice.call(menuItems).forEach(function(el) {
			el.addEventListener('click', setCurrent);
		});
	});
})(window);