// placeholder for javascript

// Example strings pulled from plugin: [object] avala_api_script_js_strings

jQuery(function($){

	$.fn.ignore = function(sel){
		return this.clone().find(sel).remove().end();
	};

	var doShowErrors = false; // set to true to show error strings onscreen
	
	if ( $('.gform_wrapper .validation_error').length > 0 ) {
		var $validationBlock = $('.gform_wrapper .validation_error');

		if ( doShowErrors )
			var $validationBlockUl = $validationBlock.append('<ul>');

		$('.gfield.gfield_error').each(function(){
			var thisField = $(this).find('label.gfield_label').ignore("span").text();
			var thisValidation = $(this).find('.gfield_description.validation_message').text();
			var thisInstruction = $(this).find('.instruction.validation_message').text();

			if ( doShowErrors ) {
				if ( thisValidation.length > 0 ) {
					$validationBlockUl.append('<li>' + thisField + ': ' + thisValidation + '</li>');
				}
				if ( thisInstruction.length > 0 ) {
					$validationBlockUl.append('<li>' + thisField + ': ' + thisInstruction + '</li>');
				}
				$(this).find('label.gfield_label').addClass('error_after');
			}
		});
	}
});
