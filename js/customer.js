jQuery(function () {
	jQuery(".customer-form").each(function () {
		jQuery(this).submit(function (e) {
			e.preventDefault();
			$this = jQuery(this);
			var data = jQuery(this).serializeArray();
			jQuery.post(customer.ajax_url, data, function (response) {
				if (response.success) {
					alert('Submitted ');

					$this[0].reset();
				} else {
					alert('Error occured: ' + response.data.message);
				}
			});
		});
	});
});




