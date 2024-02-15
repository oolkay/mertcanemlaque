<?php
/**
 * The template for Memberhsip presentation.
 *
 * This is the template for membership presentation
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap wdk-wrap">
    <h1 class="wp-heading-inline"><?php echo __('Purchase Membership Addon from 8.25$/month in yearly subscription for one website and support our work!', 'wpdirectorykit'); ?></h1>
    <p class="presentation-desc">This also help us to make updates, additional features, codes and support even better in future!</p>
    <p class="presentation-desc">Below we providing few screenshots from current main features, if you have any suggestion, or need customization feel free to <a target="_blank" href="https://wpdirectorykit.com/contact.html">contact us</a></p>
    <br />
    <h2 class="wp-heading-inline"><?php echo __('Registration/Login For membership features', 'wpdirectorykit'); ?></h2>
    <a class="image-link" target="_blank" href="https://swit.gumroad.com/l/wp-directory-membership"><img class="presentation-image" src="<?php echo WPDIRECTORYKIT_URL; ?>admin/img/register.jpg" /></a>

    <h2 class="wp-heading-inline"><?php echo __('Agents/Profiles list with search option', 'wpdirectorykit'); ?></h2>
    <a class="image-link" target="_blank" href="https://swit.gumroad.com/l/wp-directory-membership"><img class="presentation-image" src="<?php echo WPDIRECTORYKIT_URL; ?>admin/img/profiles.jpg" /></a>

    <h2 class="wp-heading-inline"><?php echo __('Member can have own profile page with listing list and form', 'wpdirectorykit'); ?></h2>
    <a class="image-link" target="_blank" href="https://swit.gumroad.com/l/wp-directory-membership"><img class="presentation-image" src="<?php echo WPDIRECTORYKIT_URL; ?>admin/img/profile.jpg" /></a>

    <h2 class="wp-heading-inline"><?php echo __('Earn money with subscriptions and Frontend Dashboard', 'wpdirectorykit'); ?></h2>
    <a class="image-link" target="_blank" href="https://swit.gumroad.com/l/wp-directory-membership"><img class="presentation-image" src="<?php echo WPDIRECTORYKIT_URL; ?>admin/img/subscriptions.jpg" /></a>

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
    
    <div class="text-center">
        <a href="https://wpdirectorykit.com/plugins.html" class="button button-primary xl" target="_blank"><?php echo esc_html__('More Plugins', 'wpdirectorykit');?></a>
    </div>
</div>


<script>
jQuery(document).ready(function($) {

})
</script>

<style>

img.presentation-image
{
    max-width:600px;
    max-height:600px;
}

h2
{
    font-size: 25px;
    color: #010b44;
}

p.presentation-desc
{
    padding:5px 0px;
    margin:0px;
    font-size: 18px;
}

a.image-link
{
    display: inline-block;
    border: 3px solid white;
}

a.image-link:hover
{
    border: 3px solid #506690;
}

</style>

<?php $this->view('general/footer', $data); ?>