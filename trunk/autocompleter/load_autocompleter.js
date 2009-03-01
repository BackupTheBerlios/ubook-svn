document.addEvent('domready', function() {
 
	/**
	 * Simple example, backend returns a list of <li> elements,
	 * processed by Autocompleter.Request.HTML.
	 */
	var inputWord = $('search');
 
	new Autocompleter.Request.HTML(inputWord, 'autocompleter/keywords.php', {
		'indicatorClass': 'autocompleter-loading' // class added to the input during request
	});
 
});