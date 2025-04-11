function import_json( action, page, total ) {
	var data = {
		action: action,
		security: wprm_admin.nonce,
		page: page,
	};

	jQuery.post(wprm_admin.ajax_url, data, function(out) {
		if (out.success) {
			page++;
			update_progress_bar( page, total );

			if ( page < total ) {
				import_json( action, page, total );
			} else {
				jQuery('#wprm-tools-finished').show();
			}
		} else {
			// alert( 'Something went wrong. Please contact support.' );
		}
	}, 'json');
}

function update_progress_bar( page, total ) {
	var percentage = page / total * 100;
	jQuery('#wprm-tools-progress-bar').css('width', percentage + '%');
};

jQuery(document).ready(function($) {
	// Import recipes.
	if( typeof window.wprm_import_json === 'object' && wprm_import_json.hasOwnProperty( 'pages' ) ) {
		import_json( 'wprm_import_json', 0, parseInt( wprm_import_json.pages ) );
	}

	// Import taxonomy terms.
	if( typeof window.wprm_import_taxonomies === 'object' && wprm_import_taxonomies.hasOwnProperty( 'pages' ) ) {
		import_json( 'wprm_import_taxonomies', 0, parseInt( wprm_import_taxonomies.pages ) );
	}

	// Import from Paprika.
	if( typeof window.wprm_import_paprika === 'object' && wprm_import_paprika.hasOwnProperty( 'pages' ) ) {
		import_json( 'wprm_import_paprika', 0, parseInt( wprm_import_paprika.pages ) );
	}
});
