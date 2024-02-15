<?php
/**
 * The template for Currencies conversion presentation.
 *
 * This is the template for currencies conversion presentation
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap wdk-wrap">
    <h1 class="wp-heading-inline"><?php echo __('How to change currency?', 'wpdirectorykit'); ?></h1>
    <p class="presentation-desc">Currency is just prefix/suffix on related price field in wpdirectorykit plugin.</p>
    <p class="presentation-desc">Dashboard->Directory Kit->Fields->Edit sale price or rent price and change suffix/prefix</p>

    <br />
    <img class="presentation-image" src="<?php echo WPDIRECTORYKIT_URL; ?>admin/img/change_currency.jpg" />
    <br /><br />
    <a href="https://wpdirectorykit.com/documentation/" class="button button-primary xl" target="_blank"><?php echo __('Please check also other documentation guides', 'wpdirectorykit'); ?></a>
    <br /><br />
    <a href="https://www.youtube.com/channel/UCTO1oMn1NOO8sJVUeaDytIA" class="button button-primary xl" target="_blank"><?php echo __('Or video tutorials on YouTube', 'wpdirectorykit'); ?></a>
    <br />
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