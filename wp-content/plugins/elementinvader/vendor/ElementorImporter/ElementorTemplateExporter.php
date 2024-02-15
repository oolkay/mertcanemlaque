<?php

namespace Elementor\TemplateLibrary;

use Elementor\Core\Base\Document;
use Elementor\Core\Editor\Editor;
use Elementor\DB;
use Elementor\Core\Settings\Manager as SettingsManager;
use Elementor\Core\Settings\Page\Model;
use Elementor\Modules\Library\Documents\Library_Document;
use Elementor\Plugin;
use Elementor\Utils;

class Elementor_Template_Exporter extends Source_Local {

	/**
	 * Prepare template to export.
	 *
	 * Retrieve the relevant template data and return them as an array.
	 *
	 * @since 1.6.0
	 * @access private
	 *
	 * @param int $template_id The template ID.
	 *
	 * @return \WP_Error|array Exported template data.
	 */
	public function ei_prepare_template_export( $template_id ) {
		$template_data = $this->get_data( [
			'template_id' => $template_id,
		] );

		if ( empty( $template_data['content'] ) ) {
			return new \WP_Error( 'empty_template', 'The template is empty' );
		}

		$template_data['content'] = $this->process_export_import_content( $template_data['content'], 'on_export' );

		if ( get_post_meta( $template_id, '_elementor_page_settings', true ) ) {
			$page = SettingsManager::get_settings_managers( 'page' )->get_model( $template_id );

			$page_settings_data = $this->process_element_export_import_content( $page, 'on_export' );

			if ( ! empty( $page_settings_data['settings'] ) ) {
				$template_data['page_settings'] = $page_settings_data['settings'];
			}
		}

		$export_data = [
			'version' => DB::DB_VERSION,
			'title' => get_the_title( $template_id ),
			'type' => self::get_template_type( $template_id ),
		];

		$export_data += $template_data;

		return [
			'name' => 'elementor-' . $template_id . '-' . gmdate( 'Y-m-d' ) . '.json',
			'content' => wp_json_encode( $export_data ),
		];
	}

}