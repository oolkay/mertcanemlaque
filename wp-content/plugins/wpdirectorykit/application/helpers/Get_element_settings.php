<?php

class WdkGetElementSettings {
	
	
	public function __construct( $postid, $widget_id, $widget_type ) {
		
		
		$this->postid 		= $postid;
		$this->widget_id 	= $widget_id;
		$this->widget_type 	= $widget_type;
		$this->widget 		= null;

		$this->parse();
		
	}
	
	public function elementor(){
		
		return 	\Elementor\Plugin::$instance;
		
	}
	
	public function get_settings () {
		if(!$this->widget) return false;
		return $this->widget;
	}
	
	private function parse() {
		$data = $this->read_data();
		$this->parse_options($data);
	}
	
	private function read_data () {

		return $this->elementor()->documents->get( $this->postid )->get_elements_data();
		
	}
	
	private function parse_options($data) {
		
		if(!is_array($data) || empty($data)){
			return;
		}		
		
		foreach ( $data as $item ) {
			
			if(empty($item)){
				continue;
			}
			
			if ( 'section' === $item['elType'] || 'column' === $item['elType'] || 'container' === $item['elType']) {
				
				$this->parse_options($item['elements']);
				
			} else {
				
				$this->parse_options_simple($item);
			}
		}
	}
	
	private function parse_options_simple($item) {
	
			if (
				
				$item['id'] === $this->widget_id && 
				$item['widgetType'] === $this->widget_type
				
			) {
				$this->widget = $item;
			}
		}

		public function generate_icon($icon, $attributes = [], $tag = 'i' ){
			if ( empty( $icon['library'] ) ) {
		return false;
	}
	$output = '';
	
	// handler SVG Icon
	if ( 'svg' === $icon['library'] ) {
		$output = \Elementor\Icons_Manager::render_uploaded_svg_icon( $icon['value'] );
	} else {
		$output = $this->render_icon_html( $icon, $attributes, $tag );
	}

	return $output;
	}
	
	public function render_icon_html( $icon, $attributes = [], $tag = 'i' ) {
		$icon_types = \Elementor\Icons_Manager::get_icon_manager_tabs();
		if ( isset( $icon_types[ $icon['library'] ]['render_callback'] ) && is_callable( $icon_types[ $icon['library'] ]['render_callback'] ) ) {
			return call_user_func_array( $icon_types[ $icon['library'] ]['render_callback'], [ $icon, $attributes, $tag ] );
		}

		if ( empty( $attributes['class'] ) ) {
			$attributes['class'] = $icon['value'];
		} else {
			if ( is_array( $attributes['class'] ) ) {
				$attributes['class'][] = $icon['value'];
			} else {
				$attributes['class'] .= ' ' . $icon['value'];
			}
		}
		return '<' . $tag . ' ' . Utils::render_html_attributes( $attributes ) . '></' . $tag . '>';
	}
}