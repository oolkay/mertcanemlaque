<?php
/**
 * The template for Settings.
 *
 * This is the template that edit form settings
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap wdk-wrap">
    <h1 class="wp-heading-inline"><?php echo __('WDK Demo Import', 'wpdirectorykit'); ?></h1>
    <br /><br />
    <div class="wdk-body">
        <div class="postbox" style="display: block;">
            <div class="postbox-header">
                <h3><?php echo __('Demo data for theme:', 'wpdirectorykit'); ?> <?php echo esc_html($current_theme->get( 'Name' )); ?></h3>
            </div>
            <div class="inside">

                <div class="wdk_theme-about">
                    <div class="wdk_theme-about-screenshots">
                        <div class="screenshot"><img src="<?php echo esc_url($current_theme->get_screenshot()); ?>" alt="<?php echo __('Theme screenshot', 'wpdirectorykit'); ?>"></div>
                    </div>

                    <div class="wdk_theme-about-info">
                        <div class="top-content">
                            <div class="theme-title">
                                <h2 class="theme-name"><?php echo esc_html($current_theme->get( 'Name' )); ?></h2>
                                <span class="theme-version"><?php echo __('Version:', 'wpdirectorykit'); ?> <?php echo esc_html($current_theme->get( 'Version' )); ?></span>
                            </div>
                            <p class="theme-author">
                                <?php echo __('By:', 'wpdirectorykit'); ?> <a href="<?php echo esc_html($current_theme->get( 'AuthorURI' )); ?>"><?php echo esc_html($current_theme->get( 'Author' )); ?></a>
                            </p>

                            <p class="theme-description"><?php echo esc_html($current_theme->get( 'Description' )); ?></p>
                        </div>
                    </div>
                </div>


            </div>
        </div>
        <p class="wdk_button-container">
            <a href="<?php echo admin_url('tools.php?page=wdk_demo_import&function=step_1');?>" class="wdk_button button button-hero button-primary"><?php echo __('Import Demo Data - Step 1','wpdirectorykit'); ?></a>
        </p>
    </div>
    <br/>
    <div class="alert alert-info" role="alert"><a href="<?php echo esc_html($current_theme->get( 'ThemeURI' )); ?>" target="_blank"><?php echo __('Theme Data will be downloaded from API:','wpdirectorykit'); ?> <?php echo esc_html($current_theme->get( 'AuthorURI' )); ?> <?php echo __('On any trouble contact us via website contact page.','wpdirectorykit'); ?></a></div>
</div>

<style>


.wdk_theme-about
{
    display: flex;
}

.wdk_theme-about .wdk_theme-about-screenshots
{
    display: flex;
    flex: 11;
    margin-right: 15px;
}

.wdk_theme-about .wdk_theme-about-info
{
    display: flex;
    flex: 10;
}



.wdk_theme-about-screenshots .screenshot
{
    box-sizing: border-box;
}

.wdk_theme-about-screenshots .screenshot img {

    border: 1px solid #CCD0D4;
    -webkit-filter: drop-shadow(0px 1px 2px rgba(0, 0, 0, 0.07));
    filter: drop-shadow(0px 1px 2px rgba(0, 0, 0, 0.07));
    width:100%;
}

.wdk_theme-about-info .theme-description {
  font-size: 16px;
  line-height: 24px;
  color: #555555;
  margin: 0 0 20px;
}

</style>

<?php $this->view('general/footer', $data); ?>