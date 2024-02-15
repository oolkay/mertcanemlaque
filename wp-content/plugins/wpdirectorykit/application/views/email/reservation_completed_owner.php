<?php

/**
 * The template for Email, notify about send on approve reservation.
 * Receiver: owner of reservation
 * This is the template that email layout
 *
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php echo esc_html__('Reservation Completed', 'wpdirectorykit'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>

<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="padding: 0;width: 100%; background-color: #f7f7f7; -webkit-text-size-adjust: none;">
    <div id="wrapper" dir="ltr" style="max-width: 600px; width:calc(100% - 30px); background-color: #fff; margin: 0 auto;border: 1px solid #dedede;box-shadow: 0 1px 4px rgb(0 0 0 / 10%);">

        <!-- header -->
        <div class="header" style="background-color: #2671cb;padding: 48px 48px;color: #FFF;">
            <h2 style="margin:0;font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; font-size: 30px; font-weight: 300; line-height: 150%; margin: 0; text-align: left; text-shadow: 0 1px 0 #ab79a1; color: #ffffff; background-color: inherit;"">
            <?php echo esc_html__('Reservation Completed', 'wpdirectorykit'); ?>
          </h2>
        </div>

        <!-- Body -->
        <div class=" body" style="padding: 48px 48px;color: #636363; font-size: 14px;font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
                <h2 style="margin-top:0">
                    <?php echo esc_html__('Hi', 'wpdirectorykit'); ?> <?php echo wdk_show_data('display_name', $user_owner); ?>,
                </h2>
                <p>
                    <?php echo sprintf(esc_html__('Visitor %1$s Reservation Completed for listing', 'wpdirectorykit'), wdk_show_data('display_name', $user_client)); ?> <a href="<?php echo esc_url(get_permalink($listing));?>"><?php echo esc_html(wmvc_show_data('post_title', $listing, '',TRUE,TRUE)); ?></a>
                </p>
                <?php if(wmvc_show_data('post_id', $listing, false)):?>
                <p>
                    <a href="<?php echo esc_url(get_permalink($listing));?>">
                        <img src="<?php echo esc_url(wdk_image_src($listing, 'thumb'));?>" alt="<?php echo esc_attr(wdk_field_value('post_title', $listing)) ;?>" style="display: block;max-width: 250px;max-height: 250px;">
                    </a>
                </p>
                <?php endif;?>
                <?php if(function_exists('wdk_dash_url')):?>
                    <p>
                        <a href="<?php echo esc_url(wdk_dash_url('dash_page=booking-reservations&function=edit&id='.$reservation_id));?>"><?php echo esc_html__('Edit Reservation', 'wpdirectorykit'); ?></a>
                    </p>
                <?php endif; ?>
                <br>
                
                <p>
                <strong><?php echo esc_html__('Mail Details', 'wpdirectorykit'); ?>: </strong>
                </p>
                <?php if(wmvc_show_data('address', $listing, false, TRUE, TRUE)):?>
                <p>
                    <strong><?php echo esc_html__('Address', 'wpdirectorykit'); ?>:</strong> <a href="<?php echo esc_url(get_permalink($listing)); ?>"><?php echo esc_html(wmvc_show_data('address', $listing, '', TRUE, TRUE)); ?></a><br />
                </p>
                <?php endif; ?> 
                <?php if(wmvc_show_data('lng', $listing, false, TRUE, TRUE) && wmvc_show_data('lat', $listing, false, TRUE, TRUE)):?>
                <p>
                    <a target="_blank" href="<?php echo esc_url('https://maps.google.com/maps?daddr='.wmvc_show_data('address', $listing, '', TRUE, TRUE).'@'.wmvc_show_data('lat', $listing, '', TRUE, TRUE).','.wmvc_show_data('lng', $listing, '', TRUE, TRUE)); ?>">
                        <strong><?php echo esc_html__('Route to destination', 'wpdirectorykit'); ?></strong> 
                    </a>
                    <br />
                    <?php echo esc_html(wmvc_show_data('lat', $listing, '', TRUE, TRUE)); ?>,<?php echo esc_html(wmvc_show_data('lng', $listing, '', TRUE, TRUE)); ?>
                </p>
                <?php endif; ?> 
                <?php if (is_string($data)) : ?>
                    <?php echo wp_kses_post($data); ?>
                <?php else : ?>
                    <?php foreach ($data as $key => $value) : ?>
                        <?php if (!empty($value)) : ?>
                            <p>
                                <strong><?php echo esc_html__(ucfirst(str_replace('_', ' ', $key)), 'wpdirectorykit'); ?>:</strong> <?php echo wp_kses_post($value); ?><br />
                            </p>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
                <?php if (!empty($notes)) : ?>
                <p style="
                            position: relative;
                            padding: .75rem 1.25rem;line-height: 1.7;
                            margin-bottom: 1rem;
                            border: 1px solid transparent;
                            border-top-color: transparent;
                            border-right-color: transparent;
                            border-bottom-color: transparent;
                            border-left-color: transparent;
                            border-radius: .25rem;
                            width: 100%;
                            color: #0c5460;
                            background-color: #d1ecf1;
                            border-color: #bee5eb;
                        "
                >
                    <strong><?php echo esc_html__('Notes', 'wpdirectorykit'); ?>:</strong> <?php echo wp_kses_post($notes); ?>
                </p>
            <?php endif; ?>
                <p>
                    <strong><?php echo esc_html__('Visitor Contact Details', 'wpdirectorykit'); ?>:</strong>
                </p>
                <ul class="wdk-list" style="margin:0;padding: 0;list-style: none;">
                    <?php if(wmvc_show_data('display_name', $user_client, false, TRUE, TRUE)):?>
                        <li style="margin-bottom: 4px;" class="meta-item"><strong><?php echo esc_html__('Name', 'wpdirectorykit'); ?>:</strong> <?php echo esc_html(wmvc_show_data('display_name', $user_client, false, TRUE, TRUE));?></li>
                    <?php endif;?>
                    <?php if(wmvc_show_data('wdk_phone', $user_client, false, TRUE, TRUE)):?>
                        <li style="margin-bottom: 4px;" class="meta-item"><strong><?php echo esc_html__('Phone', 'wpdirectorykit'); ?>:</strong> <a href="tel:<?php echo esc_attr(wdk_filter_phone(wmvc_show_data('wdk_phone', $user_client, false, TRUE, TRUE)));?>"><?php echo esc_html(wmvc_show_data('wdk_phone', $user_client, false, TRUE, TRUE));?></a></li>
                    <?php endif;?>
                    <?php if(wmvc_show_data('user_email', $user_client, false, TRUE, TRUE)):?>
                        <li style="margin-bottom: 4px;" class="meta-item"><strong><?php echo esc_html__('Email', 'wpdirectorykit'); ?>:</strong> <a href="mailto:<?php echo esc_attr(wmvc_show_data('user_email', $user_client, false, TRUE, TRUE));?>"><?php echo esc_html(wmvc_show_data('user_email', $user_client, false, TRUE, TRUE));?></a></li>
                    <?php endif;?>
                    <?php if(wmvc_show_data('wdk_address', $user_client, false, TRUE, TRUE)):?>
                        <li style="margin-bottom: 4px;" class="meta-item"><strong><?php echo esc_html__('Address', 'wpdirectorykit'); ?>:</strong> <?php echo esc_html(wmvc_show_data('wdk_address', $user_client, false, TRUE, TRUE));?></li>
                    <?php endif;?>
                    <?php if(wmvc_show_data('wdk_city', $user_client, false, TRUE, TRUE)):?>
                        <li style="margin-bottom: 4px;" class="meta-item"><strong><?php echo esc_html__('City', 'wpdirectorykit'); ?>:</strong> <?php echo esc_html(wmvc_show_data('wdk_city', $user_client, false, TRUE, TRUE));?></li>
                    <?php endif;?>
                </ul>
        </div>

        <!-- Footer -->
        <div class="footer" style="padding: 25px 48px;color: #4e5254; font-weight: 500;font-size: 14px;line-height: 1.6;font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;border-top: 1px solid #eee;">
            <?php echo esc_html__('Thanks', 'wpdirectorykit'); ?>, </br>
            <?php echo esc_html__('Best regards', 'wpdirectorykit'); ?>, </br>
            <?php echo esc_html(get_bloginfo('name')); ?> </br>
        </div>
    </div>
</body>

</html>