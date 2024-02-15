<?php
/**
 * The template for Element Agent Widget.
 * This is the template that elementor element, avatar, meta, contacts
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>



<div class="wdk-element wdk-element-listing-agent" id="wdk_el_<?php echo esc_html($id_element);?>">
    <div class="wdk-row">
        <?php if(isset($listing_agency) && !empty($listing_agency)):?>
        <div class="wdk-col">
            <div class="wdk-listing-agent <?php echo esc_attr(wmvc_show_data('layout', $settings));?>">
                <div class="agent-thumbnail">
                    <?php if(!empty(wmvc_show_data('profile_url', $listing_agency))) :?>
                        <a href="<?php echo esc_url(wmvc_show_data('profile_url', $listing_agency));?>"><img src="<?php echo esc_url(wmvc_show_data('avatar', $listing_agency));?>" alt=""></a>
                    <?php else:?>
                        <img src="<?php echo esc_url(wmvc_show_data('avatar', $listing_agency));?>" alt="">
                    <?php endif;?>
                </div>
                <div class="agent-cont">
                    <h3 class="title <?php if(!empty(wmvc_show_data('profile_url', $listing_agency['userdata']))) :?> link <?php endif;?>">
                        <?php if(!empty(wmvc_show_data('profile_url', $listing_agency['userdata']))) :?>
                            <a href="<?php echo esc_url(wmvc_show_data('profile_url', $listing_agency['userdata']));?>"><?php echo esc_html(wmvc_show_data('display_name', $listing_agency['userdata']));?></a>
                        <?php else:?>
                            <?php echo esc_html(wmvc_show_data('display_name', $listing_agency['userdata']));?>
                        <?php endif;?>
                    </h3>
                    <?php

                    $used_fields = array();
                    
                    if(!empty(wmvc_show_data('meta_fields_list', $settings)))
                    foreach (wmvc_show_data('meta_fields_list', $settings) as $meta):?>
                        <?php
                            if(isset($used_fields [$meta['meta_field']])) continue;
                            $used_fields [$meta['meta_field']] = true;
                        ?>
                        <?php
                            $value = wmvc_show_data($meta['meta_field'], $listing_agency['userdata']);
                            if(empty($value)) continue;
                        ?>
                        <?php if(filter_var($value, FILTER_VALIDATE_EMAIL) !== FALSE):?>
                            <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="mailto:<?php echo esc_attr($value);?>"><i class="far fa-envelope"></i><?php echo esc_html($value);?></a></div>
                        <?php elseif(strpos($meta['meta_field'],'youtube') !== FALSE):?>
                            <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="<?php echo esc_url($value);?>"><i class="fa fa-youtube"></i><?php echo esc_html($value);?></a></div>
                        <?php elseif(strpos($meta['meta_field'],'facebook') !== FALSE):?>
                            <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="<?php echo esc_url($value);?>"><i class="fa fa-facebook"></i><?php echo esc_html($value);?></a></div>
                        <?php elseif(strpos($meta['meta_field'],'linkedin') !== FALSE):?>
                            <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="<?php echo esc_url($value);?>"><i class="fa fa-linkedin"></i><?php echo esc_html($value);?></a></div>
                        <?php elseif(strpos($meta['meta_field'],'twitter') !== FALSE):?>
                            <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="<?php echo esc_url($value);?>"><i class="fa fa-twitter"></i><?php echo esc_html($value);?></a></div>
                        <?php elseif(strpos($meta['meta_field'],'telegram') !== FALSE):?>
                            <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="<?php echo esc_url($value);?>"><i class="fa fa-telegram"></i><?php echo esc_html($value);?></a></div>
                        <?php elseif(strpos($meta['meta_field'],'whatsapp') !== FALSE):?>
                            <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="//wa.me/<?php echo esc_attr(wdk_filter_phone($value));?>"><i class="fa fa-whatsapp"></i><?php echo esc_html($value);?></a></div>
                        <?php elseif(strpos($meta['meta_field'],'viber') !== FALSE):?>
                            <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="viber://chat?number=<?php echo esc_attr(wdk_filter_phone($value));?>"><i class="fab fa-viber"></i><?php echo esc_html($value);?></a></div>
                        <?php elseif(filter_var($value, FILTER_VALIDATE_URL) !== FALSE):?>
                            <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="<?php echo esc_url($value);?>"><i class="fa fa-anchor"></i><?php echo esc_html($value);?></a></div>
                        <?php elseif(strpos($meta['meta_field'],'phone') !== FALSE || wdk_is_phone($value)):?>
                            <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="tel:<?php echo esc_attr(wdk_filter_phone($value));?>"><i class="far fa-phone"></i><?php echo esc_html($value);?></a></div>
                        <?php else:?>
                            <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><?php echo esc_html($value);?></div>
                        <?php endif;?>
                    <?php endforeach;?>
                </div>
            </div>
        </div>
        <?php endif;?>
        <?php if(isset($userdata) && !empty($userdata)):?>
        <div class="wdk-col">
            <div class="wdk-listing-agent <?php echo esc_attr(wmvc_show_data('layout', $settings));?>">
                <?php
                    $profile_url = '';

                    if(function_exists('wdk_generate_profile_permalink') && function_exists('run_wdk_membership'))
                        $profile_url = wdk_generate_profile_permalink($userdata);

                ?>

                <div class="agent-thumbnail">
                    <?php if(!empty($profile_url)) :?>
                        <a href="<?php echo esc_url($profile_url);?>"><img src="<?php echo esc_url(get_avatar_url( $user_id));?>" alt=""></a>
                    <?php else:?>
                        <img src="<?php echo esc_url(get_avatar_url( $user_id));?>" alt="">
                    <?php endif;?>
                </div>
                <div class="agent-cont">
                    <h3 class="title <?php if(!empty($profile_url)) :?> link <?php endif;?>">
                        <?php if(!empty($profile_url)) :?>
                            <a href="<?php echo esc_url($profile_url);?>"><?php echo esc_html(wmvc_show_data('display_name', $userdata));?></a>
                        <?php else:?>
                            <?php echo esc_html(wmvc_show_data('display_name', $userdata));?>
                        <?php endif;?>
                    </h3>
                    <?php
                    $used_fields = array();
                    if(!empty(wmvc_show_data('meta_fields_list', $settings)))
                        foreach (wmvc_show_data('meta_fields_list', $settings) as $meta):?>
                            <?php
                                if(isset($used_fields [$meta['meta_field']])) continue;
                                $used_fields [$meta['meta_field']] = true;
                            ?>
                            <?php
                                $value = wmvc_show_data($meta['meta_field'], $userdata);
                                if(empty($value)) continue;
                            ?>
                            <?php if(filter_var($value, FILTER_VALIDATE_EMAIL) !== FALSE):?>
                                <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="mailto:<?php echo esc_attr($value);?>"><i class="far fa-envelope"></i><?php echo esc_html($value);?></a></div>
                            <?php elseif(strpos($meta['meta_field'],'youtube') !== FALSE):?>
                                <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="<?php echo esc_url($value);?>"><i class="fa fa-youtube"></i><?php echo esc_html($value);?></a></div>
                            <?php elseif(strpos($meta['meta_field'],'facebook') !== FALSE):?>
                                <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="<?php echo esc_url($value);?>"><i class="fa fa-facebook"></i><?php echo esc_html($value);?></a></div>
                            <?php elseif(strpos($meta['meta_field'],'linkedin') !== FALSE):?>
                                <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="<?php echo esc_url($value);?>"><i class="fa fa-linkedin"></i><?php echo esc_html($value);?></a></div>
                            <?php elseif(strpos($meta['meta_field'],'twitter') !== FALSE):?>
                                <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="<?php echo esc_url($value);?>"><i class="fa fa-twitter"></i><?php echo esc_html($value);?></a></div>
                            <?php elseif(strpos($meta['meta_field'],'telegram') !== FALSE):?>
                                <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="<?php echo esc_url($value);?>"><i class="fa fa-telegram"></i><?php echo esc_html($value);?></a></div>
                            <?php elseif(strpos($meta['meta_field'],'whatsapp') !== FALSE):?>
                                <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="//wa.me/<?php echo esc_attr(wdk_filter_phone($value));?>"><i class="fa fa-whatsapp"></i><?php echo esc_html($value);?></a></div>
                            <?php elseif(strpos($meta['meta_field'],'viber') !== FALSE):?>
                                <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="viber://chat?number=<?php echo esc_attr(wdk_filter_phone($value));?>"><i class="fab fa-viber"></i><?php echo esc_html($value);?></a></div>
                            <?php elseif(filter_var($value, FILTER_VALIDATE_URL) !== FALSE):?>
                                <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="<?php echo esc_url($value);?>"><i class="fa fa-anchor"></i><?php echo esc_html($value);?></a></div>
                            <?php elseif(strpos($meta['meta_field'],'phone') !== FALSE || wdk_is_phone($value)):?>
                                <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="tel:<?php echo esc_attr(wdk_filter_phone($value));?>"><i class="far fa-phone"></i><?php echo esc_html($value);?></a></div>
                            <?php else:?>
                                <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><?php echo esc_html($value);?></div>
                            <?php endif;?>
                    <?php endforeach;?>
                </div>
            </div>
        </div>
        <?php endif;?>
        <?php if(isset($listing_alt_agents) && !empty($listing_alt_agents))foreach ($listing_alt_agents as $key => $agent):?>
            <?php
            $profile_url = '';
            $userdata =  get_userdata(wmvc_show_data('user_id',$agent, false, TRUE, TRUE));
            
            if(!$userdata) {
                continue;
            }

            if(function_exists('wdk_generate_profile_permalink') && function_exists('run_wdk_membership'))
                $profile_url = wdk_generate_profile_permalink($userdata);

            ?>
            <div class="wdk-col">
                <div class="wdk-listing-agent <?php echo esc_attr(wmvc_show_data('layout', $settings));?>">
                    <div class="agent-thumbnail">
                        <?php if(!empty($profile_url)) :?>
                            <a href="<?php echo esc_url($profile_url);?>"><img src="<?php echo esc_url(get_avatar_url( wmvc_show_data('user_id',$agent, false, TRUE, TRUE)));?>" alt=""></a>
                        <?php else:?>
                            <img src="<?php echo esc_url(get_avatar_url(wmvc_show_data('user_id',$agent, false, TRUE, TRUE)));?>" alt="">
                        <?php endif;?>
                    </div>
                    <div class="agent-cont">
                        <h3 class="title <?php if(!empty($profile_url)) :?> link <?php endif;?>">
                            <?php if(!empty($profile_url)) :?>
                                <a href="<?php echo esc_url($profile_url);?>"><?php echo esc_html(wmvc_show_data('display_name', $userdata));?></a>
                            <?php else:?>
                                <?php echo esc_html(wmvc_show_data('display_name', $userdata));?>
                            <?php endif;?>
                        </h3>
                        <?php

                        $used_fields = array();

                        if(!empty(wmvc_show_data('meta_fields_list', $settings)))
                            foreach (wmvc_show_data('meta_fields_list', $settings) as $meta):?>
                                <?php
                                    if(isset($used_fields [$meta['meta_field']])) continue;
                                    $used_fields [$meta['meta_field']] = true;
                                ?>
                                <?php
                                    $value = wmvc_show_data($meta['meta_field'], $userdata);
                                    if(empty($value)) continue;
                                ?>
                                <?php if(filter_var($value, FILTER_VALIDATE_EMAIL) !== FALSE):?>
                                    <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="mailto:<?php echo esc_attr($value);?>"><i class="far fa-envelope"></i><?php echo esc_html($value);?></a></div>
                                <?php elseif(strpos($meta['meta_field'],'youtube') !== FALSE):?>
                                    <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="<?php echo esc_url($value);?>"><i class="fa fa-youtube"></i><?php echo esc_html($value);?></a></div>
                                <?php elseif(strpos($meta['meta_field'],'facebook') !== FALSE):?>
                                    <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="<?php echo esc_url($value);?>"><i class="fa fa-facebook"></i><?php echo esc_html($value);?></a></div>
                                <?php elseif(strpos($meta['meta_field'],'linkedin') !== FALSE):?>
                                    <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="<?php echo esc_url($value);?>"><i class="fa fa-linkedin"></i><?php echo esc_html($value);?></a></div>
                                <?php elseif(strpos($meta['meta_field'],'twitter') !== FALSE):?>
                                    <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="<?php echo esc_url($value);?>"><i class="fa fa-twitter"></i><?php echo esc_html($value);?></a></div>
                                <?php elseif(strpos($meta['meta_field'],'telegram') !== FALSE):?>
                                    <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="<?php echo esc_url($value);?>"><i class="fa fa-telegram"></i><?php echo esc_html($value);?></a></div>
                                <?php elseif(strpos($meta['meta_field'],'whatsapp') !== FALSE):?>
                                    <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="//wa.me/<?php echo esc_attr(wdk_filter_phone($value));?>"><i class="fa fa-whatsapp"></i><?php echo esc_html($value);?></a></div>
                                <?php elseif(strpos($meta['meta_field'],'viber') !== FALSE):?>
                                    <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="viber://chat?number=<?php echo esc_attr(wdk_filter_phone($value));?>"><i class="fab fa-viber"></i><?php echo esc_html($value);?></a></div>
                                <?php elseif(filter_var($value, FILTER_VALIDATE_URL) !== FALSE):?>
                                    <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="<?php echo esc_url($value);?>"><i class="fa fa-anchor"></i><?php echo esc_html($value);?></a></div>
                                <?php elseif(strpos($meta['meta_field'],'phone') !== FALSE || wdk_is_phone($value)):?>
                                    <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="tel:<?php echo esc_attr(wdk_filter_phone($value));?>"><i class="far fa-phone"></i><?php echo esc_html($value);?></a></div>
                                <?php else:?>
                                    <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><?php echo esc_html($value);?></div>
                                <?php endif;?>
                        <?php endforeach;?>
                    </div>
                </div>
            </div>
        <?php endforeach;?>
    </div>
</div>

