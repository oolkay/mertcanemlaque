<?php

/**
 * The template for Email, notify related did no read messages.
 * Receiver: save search owner
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
        
        <?php foreach ($chats as $key => $chat) : ?>
            <h2 style="margin-top:0;color:#000">
                <?php echo esc_html__('New Messsages from', 'wpdirectorykit').' ' . wmvc_show_data('display_name', $chat['user_from'], '-').'('.$chat['count'].') : <a href="' . esc_url(wdk_dash_url('dash_page=messages&function=chat&id='.wmvc_show_data('idmessage', $chat['inbox'], '-'))) . '">' . esc_html__('Check Chat', 'wpdirectorykit') . '</a>'; ?>
            </h2>
            <p> <?php echo esc_html__('Latest messages', 'wpdirectorykit');?>:</p>
            <table style="border: 1px solid;border-collapse: collapse;width:100%;">
                <thead>
                    <tr>
                        <th style="padding:10px 10px; border: 1px solid;border-collapse: collapse;text-align: left;"><?php echo __('Message', 'wpdirectorykit'); ?></th>
                        <th style="padding:10px 10px; border: 1px solid;border-collapse: collapse;text-align:center"><?php echo __('Date', 'wpdirectorykit'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($chat['messages'] as $key => $message) : if($key>=3) break; ?>
                        <tr>
                            <td style="padding:10px 10px; border: 1px solid;border-collapse: collapse;">
                                <?php echo wmvc_show_data('message', $message, '-'); ?>
                            </td>
                            <td style="padding:10px 10px; border: 1px solid;border-collapse: collapse;text-align: center;">
                                <?php echo wdk_get_date(strtotime($message->date)); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <br>
            <?php endforeach; ?>
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