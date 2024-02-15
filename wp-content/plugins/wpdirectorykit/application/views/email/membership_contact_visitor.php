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
    <title><?php echo wp_kses_post($subject); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>

<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="padding: 0;width: 100%; background-color: #f7f7f7; -webkit-text-size-adjust: none;">
    <div id="wrapper" dir="ltr" style="max-width: 600px; width:calc(100% - 30px); background-color: #fff; margin: 0 auto;border: 1px solid #dedede;box-shadow: 0 1px 4px rgb(0 0 0 / 10%);">

        <!-- header -->
        <div class="header" style="background-color: #2671cb;padding: 48px 48px;color: #FFF;">
            <h2 style="margin:0;font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; font-size: 30px; font-weight: 300; line-height: 150%; margin: 0; text-align: left; text-shadow: 0 1px 0 #ab79a1; color: #ffffff; background-color: inherit;"">
                <?php echo wp_kses_post($subject); ?>
            </h2>
        </div>

        <!-- Body -->
        <div class=" body" style="padding: 48px 48px;color: #636363; font-size: 14px;font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">

            <?php if (is_string($data)) : ?>
                <?php echo wp_kses_post($data); ?>
            <?php else : ?>
                <?php foreach ($data as $key => $value) : ?>
                    <?php if (!empty($value)) : ?>
                        <?php if (!is_string($value)) continue; ?>
                        <?php if ($key == 'subject') continue; ?>
                        <p>
                            <strong><?php echo esc_html__(ucfirst(str_replace('_', ' ', $key)), 'wpdirectorykit'); ?>:</strong> <?php echo wp_kses_post($value); ?><br />
                        </p>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
            
            <br/>
            <p>
                <strong><?php echo esc_html__('Message Details', 'wpdirectorykit'); ?>:</strong>
            </p>
            <?php if (is_string($message)) : ?>
                <?php echo wp_kses_post($message); ?>
            <?php else : ?>
                <?php foreach ($message as $key => $value) : ?>
                    <?php if (!empty($value)) : ?>
                        <?php if (!is_string($value)) continue; ?>
                        <?php if ($key == 'subject') continue; ?>
                        <p>
                            <strong><?php echo esc_html__(ucfirst(str_replace('_', ' ', $key)), 'wpdirectorykit'); ?>:</strong> <?php echo wp_kses_post($value); ?><br />
                        </p>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
           
            <p>
                <strong><?php echo esc_html__('Profile Contact Details', 'wpdirectorykit'); ?>:</strong>
            </p>
            <ul class="wdk-list" style="margin:0;padding: 0;list-style: none;">
                <?php if(wmvc_show_data('display_name', $user_profile, false, TRUE, TRUE)):?>
                    <li style="margin-bottom: 4px;" class="meta-item"><strong><?php echo esc_html__('Name', 'wpdirectorykit'); ?>:</strong> <?php echo esc_html(wmvc_show_data('display_name', $user_profile, false, TRUE, TRUE));?></li>
                <?php endif;?>
                <?php if(wmvc_show_data('wdk_phone', $user_profile, false, TRUE, TRUE)):?>
                    <li style="margin-bottom: 4px;" class="meta-item"><strong><?php echo esc_html__('Phone', 'wpdirectorykit'); ?>:</strong> <a href="tel:<?php echo esc_attr(wdk_filter_phone(wmvc_show_data('wdk_phone', $user_profile, false, TRUE, TRUE)));?>"><?php echo esc_html(wmvc_show_data('wdk_phone', $user_profile, false, TRUE, TRUE));?></a></li>
                <?php endif;?>
                <?php if(wmvc_show_data('user_email', $user_profile, false, TRUE, TRUE)):?>
                    <li style="margin-bottom: 4px;" class="meta-item"><strong><?php echo esc_html__('Email', 'wpdirectorykit'); ?>:</strong> <a href="mailto:<?php echo esc_attr(wmvc_show_data('user_email', $user_profile, false, TRUE, TRUE));?>"><?php echo esc_html(wmvc_show_data('user_email', $user_profile, false, TRUE, TRUE));?></a></li>
                <?php endif;?>
                <?php if(wmvc_show_data('wdk_address', $user_profile, false, TRUE, TRUE)):?>
                    <li style="margin-bottom: 4px;" class="meta-item"><strong><?php echo esc_html__('Address', 'wpdirectorykit'); ?>:</strong> <?php echo esc_html(wmvc_show_data('wdk_address', $user, false, TRUE, TRUE));?></li>
                <?php endif;?>
                <?php if(wmvc_show_data('wdk_city', $user_profile, false, TRUE, TRUE)):?>
                    <li style="margin-bottom: 4px;" class="meta-item"><strong><?php echo esc_html__('City', 'wpdirectorykit'); ?>:</strong> <?php echo esc_html(wmvc_show_data('wdk_city', $user, false, TRUE, TRUE));?></li>
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