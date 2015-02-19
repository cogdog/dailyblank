jQuery(document).ready(function() { 

	jQuery('#wTitle').keyup(function() {
		var cs = jQuery(this).val().length;
		jQuery('#wCount').text(cs);
	});

});
						
