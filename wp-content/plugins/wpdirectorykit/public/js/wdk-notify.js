const wdk_log_notify = (text, type, popup_place) => {
	var $ = jQuery;
	if (!$('.wdk_log_notify-box').length) $('body').append('<div class="wdk_log_notify-box"></div>')
	if (typeof text == "undefined") var text = 'Undefined text';
	if (typeof type == "undefined") var type = 'success';
	if (typeof popup_place == "undefined") var popup_place = $('.wdk_log_notify-box');
	var el_class = '';
	var el_timer = 5000;
	switch (type) {
		case "success":
			el_class = "success";
			break
		case "error":
			el_class = "error";
			break
		case "loading":
			el_class = "loading";
			el_timer = 2000;
			break
		default:
			el_class = "success";
			break
	}

	/* notify */
	var html = '';
	html = '<div class="wdk_log_notify ' + el_class + '">\n\
				' + text + '\n\
		</div>';
	var notification = $(html).appendTo(popup_place).delay(100).queue(function() {
			$(this).addClass('show')
			setTimeout(function() {
				notification.removeClass('show')
				setTimeout(function() {
					notification.remove();
				}, 1000);
			}, el_timer);
		})
		/* end notify */
}
