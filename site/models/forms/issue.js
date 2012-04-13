window.addEvent('domready', function() {
	document.formvalidator.setHandler('catid',
		function (value) {
			regex=/^[1-9]+[0-9]*$/;		//only positive greater than 0
			return regex.test(value);
	});
});