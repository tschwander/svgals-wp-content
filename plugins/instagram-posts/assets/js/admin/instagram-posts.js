(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note that this assume you're going to use jQuery, so it prepares
	 * the $ function reference to be used within the scope of this
	 * function.
	 *
	 * From here, you're able to define handlers for when the DOM is
	 * ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * Or when the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and so on.
	 *
	 * Remember that ideally, we should not attach any more than a single DOM-ready or window-load handler
	 * for any particular page. Though other scripts in WordPress core, other plugins, and other themes may
	 * be doing this, we should try to minimize doing that in our own work.
	 */

	$(()=>{
		var doc = $(document);

		$('[data-show]').each((i,e)=>{
			var elm = $(e);
			var tar = $(elm.data('show'));
			if (!elm.is(':checked')) tar.hide();
		});

		doc.on('change', '[data-show]', (e)=>{
			var elm = $(e.currentTarget);
			var tar = $(elm.data('show'));
			
			if (elm.is(':checked')) tar.show();
			else tar.hide();
		});

		doc.on('click', '[data-xor]', (e)=>{
			var sr = $(e.currentTarget);
			$('[data-xor').each((i,el)=>{
				var elm = $(el);
				if (e.currentTarget != el)
				{
					elm.prop('checked', false);
					elm.trigger('change');
				} 
			})
		});
	});

})( jQuery );
