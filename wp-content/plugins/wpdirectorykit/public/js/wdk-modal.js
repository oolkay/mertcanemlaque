/*

html example:

<a href="#" "
	class="button button-primary">
	Open
</a>


<div class="wdk-modal wdk-fade" id="noticeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-notice">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo esc_html__('How Add Link Calendar Into Google?', 'wpdirectorykit'); ?>
                </h5>
                <button type="button" class="close" data-wdk-dismiss="modal" aria-hidden="true">
                    <span class="dashicons dashicons-no-alt"></span>
                </button>
            </div>
            <div class="modal-body">
              <div class="instruction">
                  <h4><strong><?php echo esc_html__('1. Open Settings On Google Calendar', 'wpdirectorykit'); ?></strong>
                  </h4>
                  <br>
                  <img src="<?php echo esc_url(WDK_BOOKING_URL . 'public/img/google_calendar_guide.jpg'); ?>"
                      alt="Thumbnail Image">
              </div>
              <br>
              <div class="instruction">
                  <h4><strong><?php echo esc_html__('2. Copy Past Event Link', 'wpdirectorykit'); ?></strong></h4>
                  <p class="description">
                      <textarea row="5" style="width:100%"
                          class="wp-editor-area"><?php echo get_admin_url() . "admin.php?page=wdk-bookings-calendar&function=export_icl_calendar&hash=" . substr(md5(wmvc_show_data('idcalendar', $db_data, '-') . 'ms1f5c06b3b3e34'), 0, 10); ?></textarea>
                  </p>
              </div>
        </div>
        <div class="modal-footer justify-content-center">
            <button type="button" class="button button-secondary"
                data-wdk-dismiss="modal"><?php echo esc_html__('Close', 'wpdirectorykit'); ?></button>
        </div>
    </div>
</div>


*/


const wdk_log_modal = () => {
	var $ = jQuery; 

	$('*[data-wdk-toggle="modal"][data-wdk-target]:not(.init)').on('click', function(e){
		e.preventDefault();
		e.stopPropagation();
		$('.wdk-modal'+$(this).attr('data-wdk-target')).show().toggleClass("active");
		$("body").addClass("wdk-overlay-bgg");
	}).addClass('init');

	$("html").on("click", function (e) {
        if ($(e.target).closest('.modal-content').length) return;
		if ($(e.target).closest('.select2-container').length) return;
		if ($(e.target).closest('.daterangepicker').length) return;
		
		
		$('.wdk-modal').hide().removeClass("active");
		$("body").removeClass("wdk-overlay-bgg");
	});

	$('*[data-wdk-dismiss="modal"]:not(.init)').on('click', function(e){
		e.preventDefault();

		if($(this).attr('data-wdk-target')) {
			$('.wdk-modal'+$(this).attr('data-wdk-target')).hide().removeClass("active");
		} else {
			$(this).closest('.wdk-modal').hide().removeClass("active");
		}

		$("body").removeClass("wdk-overlay-bgg");
	}).addClass('init');

    $('.wdk-modal:not(.init)').each(function () { 
        if($(this).hasClass('nodetach')) {
            $(this).addClass('init');
        } else {
            $('body').append($(this).detach().addClass('init'));
        }

    })
}

jQuery(document).on('ready', function(){
	wdk_log_modal();
});