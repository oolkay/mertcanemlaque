<?php

/**
 * The template for Element Agent Widget.
 * This is the template that elementor element, avatar, meta, contacts
 *
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

?>



<div class="wdk-element wdk-element-listing-agent" id="wdk_el_<?php echo esc_html($id_element); ?>">
    <div class="wdk-row">
        <?php if ($settings['listing_agency_disabled'] != 'yes') : ?>
            <div class="wdk-col">
                <div class="wdk-listing-agent <?php echo esc_attr(wmvc_show_data('layout', $settings)); ?>">
                    <div class="agent-thumbnail">
                        <a href="#"><img src="<?php echo esc_url(wdk_placeholder_image_src());?>" alt=""></a>
                    </div>
                    <div class="agent-cont">
                        <h3 class="title link">
                            <a href="#"><?php echo esc_html__('Agency Real','wpdirectorykit');?></a>
                        </h3>
                        <?php
                        if(!empty(wmvc_show_data('meta_fields_list', $settings)))
                        foreach (wmvc_show_data('meta_fields_list', $settings) as $meta):?>
                            <?php if(filter_var($meta['meta_field'], FILTER_VALIDATE_EMAIL) !== FALSE || strpos($meta['meta_field'],'email') !== FALSE):?>
                                <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="mailto:agent1@wpdirectorykit.com"><i class="far fa-envelope"></i>agency@example.com</a></div>
                            <?php elseif(strpos($meta['meta_field'],'youtube') !== FALSE):?>
                                <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="#"><i class="fa fa-youtube"></i><?php echo esc_html__('link youtube','wdk-membership');?></a></div>
                            <?php elseif(strpos($meta['meta_field'],'facebook') !== FALSE):?>
                                <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="#"><i class="fa fa-facebook"></i><?php echo esc_html__('link facebook','wdk-membership');?></a></div>
                            <?php elseif(strpos($meta['meta_field'],'linkedin') !== FALSE):?>
                                <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="#"><i class="fa fa-linkedin"></i><?php echo esc_html__('link linkedin','wdk-membership');?></a></div>
                            <?php elseif(strpos($meta['meta_field'],'twitter') !== FALSE):?>
                                <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="#"><i class="fa fa-twitter"></i><?php echo esc_html__('link twitter','wdk-membership');?></a></div>
                            <?php elseif(strpos($meta['meta_field'],'telegram') !== FALSE):?>
                                <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="#"><i class="fa fa-telegram"></i><?php echo esc_html__('link telegram','wdk-membership');?></a></div>
                            <?php elseif(strpos($meta['meta_field'],'whatsapp') !== FALSE):?>
                                <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="#"><i class="fa fa-whatsapp"></i><?php echo esc_html__('link whatsapp','wdk-membership');?> </a></div>
                            <?php elseif(strpos($meta['meta_field'],'viber') !== FALSE):?>
                                <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="viber://chat?number=#"><i class="fab fa-viber"></i>(917) 367-2058</a></div>
                            <?php elseif(filter_var($meta['meta_field'], FILTER_VALIDATE_URL) !== FALSE):?>
                                <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="#"><i class="fa fa-anchor"></i>(917) 367-2058</a></div>
                            <?php elseif(strpos($meta['meta_field'],'phone') !== FALSE):?>
                                <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="tel:9173672058"><i class="far fa-phone"></i>(917) 367-2058</a></div>
                            <?php else:?>
                                <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><?php echo esc_html__('meta ','wdk-membership');?> <?php echo esc_html($meta['meta_field']);?></div>
                            <?php endif;?>
                        <?php endforeach;?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($settings['user_editor_disabled'] != 'yes') : ?>
            <div class="wdk-col">
                <div class="wdk-listing-agent ">
                    <div class="agent-thumbnail">
                        <a href="#"><img src="<?php echo esc_url(wdk_placeholder_image_src());?>" alt=""></a>
                    </div>
                    <div class="agent-cont">
                        <h3 class="title link">
                            <a href="#"><?php echo esc_html__('Agent Moran','wpdirectorykit');?></a>
                        </h3>
                        <?php
                        if(!empty(wmvc_show_data('meta_fields_list', $settings)))
                        foreach (wmvc_show_data('meta_fields_list', $settings) as $meta):?>
                            <?php if(filter_var($meta['meta_field'], FILTER_VALIDATE_EMAIL) !== FALSE || strpos($meta['meta_field'],'mail') !== FALSE):?>
                                <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="mailto:agent1@wpdirectorykit.com"><i class="far fa-envelope"></i>agent@example.com</a></div>
                            <?php elseif(strpos($meta['meta_field'],'youtube') !== FALSE):?>
                                <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="#"><i class="fa fa-youtube"></i><?php echo esc_html__('link youtube','wdk-membership');?></a></div>
                            <?php elseif(strpos($meta['meta_field'],'facebook') !== FALSE):?>
                                <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="#"><i class="fa fa-facebook"></i><?php echo esc_html__('link facebook','wdk-membership');?></a></div>
                            <?php elseif(strpos($meta['meta_field'],'linkedin') !== FALSE):?>
                                <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="#"><i class="fa fa-linkedin"></i><?php echo esc_html__('link linkedin','wdk-membership');?></a></div>
                            <?php elseif(strpos($meta['meta_field'],'twitter') !== FALSE):?>
                                <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="#"><i class="fa fa-twitter"></i><?php echo esc_html__('link twitter','wdk-membership');?></a></div>
                            <?php elseif(strpos($meta['meta_field'],'telegram') !== FALSE):?>
                                <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="#"><i class="fa fa-telegram"></i><?php echo esc_html__('link telegram','wdk-membership');?></a></div>
                            <?php elseif(strpos($meta['meta_field'],'whatsapp') !== FALSE):?>
                                <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="#"><i class="fa fa-whatsapp"></i><?php echo esc_html__('link whatsapp','wdk-membership');?> </a></div>
                            <?php elseif(strpos($meta['meta_field'],'viber') !== FALSE):?>
                                <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="viber://chat?number=#"><i class="fab fa-viber"></i>(917) 367-2058</a></div>
                            <?php elseif(filter_var($meta['meta_field'], FILTER_VALIDATE_URL) !== FALSE):?>
                                <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="#"><i class="fa fa-anchor"></i>(917) 367-2058</a></div>
                            <?php elseif(strpos($meta['meta_field'],'phone') !== FALSE):?>
                                <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="tel:9173672058"><i class="far fa-phone"></i>(917) 367-2058</a></div>
                            <?php else:?>
                                <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><?php echo esc_html__('meta ','wdk-membership');?> <?php echo esc_html($meta['meta_field']);?></div>
                            <?php endif;?>
                        <?php endforeach;?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($settings['alternative_agents_disabled'] != 'yes') : ?>
        <?php for($i = 1; $i<=3; $i++):?>
            <div class="wdk-col">
                <div class="wdk-listing-agent <?php echo esc_attr(wmvc_show_data('layout', $settings)); ?>">
                    <div class="agent-thumbnail">
                        <a href="#"><img src="<?php echo esc_url(wdk_placeholder_image_src());?>" alt=""></a>
                    </div>
                    <div class="agent-cont">
                        <h3 class="title link">
                            <a href="#"><?php echo esc_html__('Alt Agents','wpdirectorykit');?> #<?php echo esc_html($i);?></a>
                        </h3>
                        <?php
                        if(!empty(wmvc_show_data('meta_fields_list', $settings)))
                        foreach (wmvc_show_data('meta_fields_list', $settings) as $meta):?>
                            <?php if(filter_var($meta['meta_field'], FILTER_VALIDATE_EMAIL) !== FALSE || strpos($meta['meta_field'],'email') !== FALSE):?>
                                <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="mailto:agent1@wpdirectorykit.com"><i class="far fa-envelope"></i>agent@example.com</a></div>
                            <?php elseif(strpos($meta['meta_field'],'youtube') !== FALSE):?>
                                <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="#"><i class="fa fa-youtube"></i><?php echo esc_html__('link youtube','wdk-membership');?></a></div>
                            <?php elseif(strpos($meta['meta_field'],'facebook') !== FALSE):?>
                                <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="#"><i class="fa fa-facebook"></i><?php echo esc_html__('link facebook','wdk-membership');?></a></div>
                            <?php elseif(strpos($meta['meta_field'],'linkedin') !== FALSE):?>
                                <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="#"><i class="fa fa-linkedin"></i><?php echo esc_html__('link linkedin','wdk-membership');?></a></div>
                            <?php elseif(strpos($meta['meta_field'],'twitter') !== FALSE):?>
                                <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="#"><i class="fa fa-twitter"></i><?php echo esc_html__('link twitter','wdk-membership');?></a></div>
                            <?php elseif(strpos($meta['meta_field'],'telegram') !== FALSE):?>
                                <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="#"><i class="fa fa-telegram"></i><?php echo esc_html__('link telegram','wdk-membership');?></a></div>
                            <?php elseif(strpos($meta['meta_field'],'whatsapp') !== FALSE):?>
                                <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="#"><i class="fa fa-whatsapp"></i><?php echo esc_html__('link whatsapp','wdk-membership');?> </a></div>
                            <?php elseif(strpos($meta['meta_field'],'viber') !== FALSE):?>
                                <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="viber://chat?number=#"><i class="fab fa-viber"></i>(917) 367-2058</a></div>
                            <?php elseif(filter_var($meta['meta_field'], FILTER_VALIDATE_URL) !== FALSE):?>
                                <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="#"><i class="fa fa-anchor"></i>(917) 367-2058</a></div>
                            <?php elseif(strpos($meta['meta_field'],'phone') !== FALSE):?>
                                <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><a href="tel:9173672058"><i class="far fa-phone"></i>(917) 367-2058</a></div>
                            <?php else:?>
                                <div class="meta-item <?php echo esc_html($meta['meta_field']);?>"><?php echo esc_html__('meta ','wdk-membership');?> <?php echo esc_html($meta['meta_field']);?></div>
                            <?php endif;?>
                        <?php endforeach;?>
                    </div>
                </div>
            </div>
        <?php endfor; ?>
    <?php endif; ?>
    </div>
</div>