<?php
/**
 * The template for Subscription / Addons.
 *
 * This is the template that table, addons, packages
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap wdk-wrap">
    <h1 class="wp-heading-inline"><?php echo __('Subscription', 'wpdirectorykit'); ?></h1>
    <div class="wdk-pricing">
        <div class="wdk-sec-header">
            <h2 class="wdk-sec-title">
                <span class="mark up"><?php echo esc_html__('SAVE MONEY!', 'wpdirectorykit'); ?></span>
                <?php echo esc_html__('Get all', 'wpdirectorykit'); ?>
                <span class="mark"><?php echo esc_html__('addons and premium', 'wpdirectorykit'); ?></span>
                <?php echo esc_html__('support from 8.25$/month in yearly subscription', 'wpdirectorykit'); ?>
            </h2>
        </div>
        <div class="wdk-row">
            <div class="wdk-col-md-4">
                <div class="wdk-pac">
                    <div class="header">
                        <h4><?php echo esc_html__('Personal Monthly', 'wpdirectorykit'); ?></h4>
                        <div class="pricing-value"><span class="price"><span class="before">$</span>19<span class="after"></span></span></div>
                        <p><?php echo esc_html__('Include all functionalities for single website installation', 'wpdirectorykit'); ?></p>
                    </div>
                    <ul class="list-items">
                        <li class="item"><?php echo esc_html__('All Our Themes', 'wpdirectorykit'); ?></li>
                        <li class="item"><?php echo esc_html__('All Addons', 'wpdirectorykit'); ?></li>
                        <li class="item"><?php echo esc_html__('All Upcoming Addons', 'wpdirectorykit'); ?></li>
                        <li class="item"><?php echo esc_html__('1 month Chat Support (Telegram)', 'wpdirectorykit'); ?></li>
                        <li class="item"><?php echo esc_html__('1 month Updates', 'wpdirectorykit'); ?></li>
                    </ul>
                    <div class="wdk-pac-footer"><a target="_blank" href="https://swit.gumroad.com/l/wp-directory-kit-addons?monthly=true" class="btn btn-outline-secondary"><?php echo esc_html__('Buy now', 'wpdirectorykit'); ?></a></div>
                </div>
            </div>
            <div class="wdk-col-md-4">
                <div class="wdk-pac featured">
                    <div class="header">
                        <h4><?php echo esc_html__('Personal 3 Months', 'wpdirectorykit'); ?></h4>
                        <div class="pricing-value"><span class="price"><span class="before">$</span>49<span class="after"></span></span></div>
                        <p><?php echo esc_html__('Include all functionalities for single website installation', 'wpdirectorykit'); ?></p>
                    </div>
                    <ul class="list-items">
                        <li class="item"><?php echo esc_html__('All Our Themes', 'wpdirectorykit'); ?></li>
                        <li class="item"><?php echo esc_html__('All Addons', 'wpdirectorykit'); ?></li>
                        <li class="item"><?php echo esc_html__('All Upcoming Addons', 'wpdirectorykit'); ?></li>
                        <li class="item"><?php echo esc_html__('3 months Chat Support (Telegram)', 'wpdirectorykit'); ?></li>
                        <li class="item"><?php echo esc_html__('3 months Updates', 'wpdirectorykit'); ?></li>
                    </ul>
                    <div class="wdk-pac-footer"><a target="_blank" href="https://swit.gumroad.com/l/wp-directory-kit-addons?quarterly=true" class="btn btn-primary"><?php echo esc_html__('Buy now', 'wpdirectorykit'); ?></a></div>
                </div>
            </div>
            <div class="wdk-col-md-4">
                <div class="wdk-pac">
                    <div class="header">
                        <h4><?php echo esc_html__('Personal Yearly', 'wpdirectorykit'); ?></h4>
                        <div class="pricing-value"><span class="price"><span class="before">$</span>99<span class="after"></span></span></div>
                        <p><?php echo esc_html__('Include all functionalities for single website installation', 'wpdirectorykit'); ?></p>
                    </div>
                    <ul class="list-items">
                        <li class="item"><?php echo esc_html__('All Our Themes', 'wpdirectorykit'); ?></li>
                        <li class="item"><?php echo esc_html__('All Addons', 'wpdirectorykit'); ?></li>
                        <li class="item"><?php echo esc_html__('All Upcoming Addons', 'wpdirectorykit'); ?></li>
                        <li class="item"><?php echo esc_html__('1 year Chat Support (Telegram)', 'wpdirectorykit'); ?></li>
                        <li class="item"><?php echo esc_html__('1 year Updates', 'wpdirectorykit'); ?></li>
                    </ul>
                    <div class="wdk-pac-footer"><a target="_blank" href="https://swit.gumroad.com/l/wp-directory-kit-addons?yearly=true" class="btn btn-outline-secondary"><?php echo esc_html__('Buy now', 'wpdirectorykit'); ?></a></div>
                </div>
            </div>
        </div>
    </div>

    <h1 class="wp-heading-inline"><?php echo __('Addons', 'wpdirectorykit'); ?></h1>
    <br style="clear:both"/>
    <br style="clear:both"/>
    <div class="wp-list-table widefat plugin-install">
        <div id="the-list">
            <?php foreach ($addons as $addon):?>
            <div class="plugin-card plugin-card-classic-editor">
                <div class="plugin-card-top">
                    <div class="name column-name">
                        <h3>
                            <a target="_blank" href="<?php echo esc_url(wmvc_show_data('link', $addon));?>" class="open-plugin-details-modal">
                                <?php echo esc_html(wmvc_show_data('title', $addon));?>
                                <img style="object-fit: contain;object-position: top;" src="<?php echo esc_url(wmvc_show_data('thumbnail', $addon));?>" class="plugin-icon" alt="<?php echo esc_html(wmvc_show_data('title', $addon));?>">
                            </a>
                        </h3>
                    </div>
                    <div class="action-links">
                        <ul class="plugin-action-buttons">
                            <li>
                                <?php if(wmvc_show_data('is_activated_slug', $addon, false) && function_exists(wmvc_show_data('is_activated_slug', $addon))):?>
                                    <button type="button" class="button button-disabled" disabled="disabled"><?php echo esc_html__('Active', 'wpdirectorykit'); ?></button>
                                <?php elseif(wmvc_show_data('is_exists_slug', $addon, false) && file_exists(WP_PLUGIN_DIR.'/'.wmvc_show_data('is_exists_slug', $addon))):?>
                                    <?php
                                    $activate_url = add_query_arg(
                                        array(
                                            '_wpnonce' => wp_create_nonce( 'activate-plugin_' . wmvc_show_data('is_exists_slug', $addon, false) ),
                                            'action'   => 'activate',
                                            'plugin'   => wmvc_show_data('is_exists_slug', $addon, false),
                                        ),
                                        network_admin_url( 'plugins.php' )
                                    );
                                    ?>
                                    <a class="button activate-now" href="<?php echo esc_url($activate_url);?>"><?php echo esc_html__('Activate', 'wpdirectorykit'); ?></a>
                                <?php else:?>
                                    <?php if(file_exists(get_stylesheet_directory() .'/addons/'.substr(basename(wmvc_show_data('is_exists_slug', $addon)), 0, -4).'.zip')):?>
                                        <a target="_blank" class="install-now button btn-danger" data-slug="classic-editor" href="<?php echo esc_url(wdk_get_tgmpa_link());?>" title="<?php echo esc_html(wmvc_show_data('title', $addon));?>"><?php echo esc_html__('Activate', 'wpdirectorykit'); ?></a>
                                    <?php elseif(stripos(wmvc_show_data('link', $addon), 'sweet-energy-efficiency') === FALSE):?>
                                        <a target="_blank" class="install-now button btn-danger" data-slug="classic-editor" href="<?php echo esc_url(wmvc_show_data('link', $addon));?>" title="<?php echo esc_html(wmvc_show_data('title', $addon));?>"><?php echo esc_html__('Buy Now', 'wpdirectorykit'); ?></a>
                                    <?php else:?>
                                        <a target="_blank" class="install-now button btn-info" data-slug="classic-editor" href="<?php echo esc_url(wmvc_show_data('link', $addon));?>" title="<?php echo esc_html(wmvc_show_data('title', $addon));?>"><?php echo esc_html__('Download Free', 'wpdirectorykit'); ?></a>
                                    <?php endif;?>
                                <?php endif;?>
                            </li>
                            <li><a target="_blank" href="<?php echo esc_url(wmvc_show_data('link_info', $addon));?>" class="open-plugin-details-modal"><?php echo esc_html__('More Details', 'wpdirectorykit'); ?></a></li>
                        </ul>				
                    </div>
                    <div class="desc column-description">
                        <p><?php echo esc_html(wmvc_show_data('description', $addon));?></p>
                        <p class="authors"><cite><?php echo esc_html__('By', 'wpdirectorykit'); ?> <a target="_blank" href="https://wpdirectorykit.com/"><?php echo esc_html__('WP Directory Kit', 'wpdirectorykit'); ?></a></cite></p>
                    </div>
                </div>
            </div>
            <?php endforeach;?>
            <br style="clear:both"/>
        </div>
        <br style="clear:both"/>
        <div class="text-center">
            <a href="https://wpdirectorykit.com/plugins.html" class="button button-primary xl" target="_blank"><?php echo esc_html__('More Plugins', 'wpdirectorykit');?></a>
        </div>
   

    </div>
</div>


<script>
jQuery(document).ready(function($) {

})
</script>

<?php $this->view('general/footer', $data); ?>