window.addEvent("load",function(){

	var fluid0 = $('jform_params_fluidWidth0');
	var fluid1 = $('jform_params_fluidWidth1');
	
	if (fluid1.checked  ) {
		$('jform_params_modulewidth').setProperty('disabled', 'disabled');
		$('jform_params_minwidth').removeProperty('disabled');
	} else 
		$('jform_params_minwidth').setProperty('disabled', 'disabled'); 
	
	fluid1.addEvent('click',function(event) {
		console.log(fluid1, 'fluid1 clicked');
		$('jform_params_modulewidth').setProperty('disabled', 'disabled');
		$('jform_params_minwidth').removeProperty('disabled');
	});
	
	fluid0.addEvent('click',function(event) {
		console.log(fluid0, 'fluid0 clicked');
		$('jform_params_modulewidth').removeProperty('disabled');
		$('jform_params_minwidth').setProperty('disabled', 'disabled');
	});
	
	
	// other form operations
	//$$('.experimental').each(function(el){el.getParent().innerHTML = el.getParent().innerHTML + "<span class=\"unit\">Experimental!</span>"});
	$$('.input-pixels').each(function(el){el.getParent().innerHTML = el.getParent().innerHTML + "<span class=\"unit\"> px</span>"});
	$$('.input-percents').each(function(el){el.getParent().innerHTML = el.getParent().innerHTML + "<span class=\"unit\"> %</span>"});
	$$('.input-minutes').each(function(el){el.getParent().innerHTML = el.getParent().innerHTML + "<span class=\"unit\"> minutes</span>"});
	$$('.input-ms').each(function(el){el.getParent().innerHTML = el.getParent().innerHTML + "<span class=\"unit\"> ms</span>"});
	
});

// encode chars
function htmlspecialchars(string) {
    string = string.toString();
    string = string.replace(/&/g, '[ampersand]').replace(/</g, '[leftbracket]').replace(/>/g, '[rightbracket]');
    return string;
}
// decode chars
function htmlspecialchars_decode(string) {
	string = string.toString();
	string = string.replace(/\[ampersand\]/g, '&').replace(/\[leftbracket\]/g, '<').replace(/\[rightbracket\]/g, '>');
	return string;
}