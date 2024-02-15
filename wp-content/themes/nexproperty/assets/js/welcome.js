jQuery(function ($) {
    $('.nexproperty-install-plugin').on('click', function (event) {
        event.preventDefault();
        var $button = $(this);

        if ($button.hasClass('updating-message')) {
            return;
        }

        wp.updates.installPlugin({
            slug: $button.data('slug')
        });
    });

    $(document).on('click', '.nexproperty-activate-plugin', function (event) {
        event.preventDefault();
        var $button = $(this);
        $button.addClass('updating-message').html( );

        nexproperty_activate_plugin($button);

    });

    $(document).on('wp-plugin-installing', function (event, args) {
        event.preventDefault();

        $('.nexproperty-install-plugin').addClass('updating-message').html(importer_params.installing_text);

    });

    $(document).on('wp-plugin-install-success', function (event, response) {

        event.preventDefault();
        var $button = $('.nexproperty-install-plugin');

        $button.html(importer_params.activating_text);

        setTimeout(function () {
            nexproperty_activate_plugin($button);
        }, 1000);

    });

    function nexproperty_activate_plugin($button) {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'nexproperty_activate_plugin',
                slug: $button.data('slug'),
                file: $button.data('filename'),
                _wpnonce: importer_params.wpnonce,
            },
        }).done(function (result) {
            var result = JSON.parse(result)
            if (result.success && importer_params.success_redirect == '1') {
                window.location.href = importer_params.importer_url;
            } else if (result.success) {
                $button.parent().append('<a href="'+importer_params.tgmpa_link+'" class="button button-primary">'+importer_params.success_import+'</a>')
                $button.remove();
            } else {
                $button.removeClass('updating-message').html(importer_params.error);
            }
        });
    }
});