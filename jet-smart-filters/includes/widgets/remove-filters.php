<?php

namespace Elementor;

use Elementor\Plugin;
use Elementor\Group_Control_Border;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography as Global_Typography;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Jet_Smart_Filters_Remove_Filters_Widget extends Widget_Base {

	public function get_name() {

		return 'jet-smart-filters-remove-filters';
	}

	public function get_title() {

		return __( 'Remove Filters', 'jet-smart-filters' );
	}

	public function get_icon() {

		return 'jet-smart-filters-icon-remove-filter';
	}

	public function get_html_wrapper_class() {

		return 'elementor-widget-' . $this->get_name() . ' hide-widget';
	}

	public function get_help_url() {

		return jet_smart_filters()->widgets->prepare_help_url(
			'https://crocoblock.com/knowledge-base/articles/jetsmartfilters-how-to-use-remove-filters-widget/',
			$this->get_name()
		);
	}

	public function get_categories() {

		return array( jet_smart_filters()->widgets->get_category() );
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_general',
			array(
				'label' => __( 'Content', 'jet-smart-filters' ),
			)
		);

		$this->add_control(
			'content_provider',
			array(
				'label'   => __( 'This filter for', 'jet-smart-filters' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => jet_smart_filters()->data->content_providers(),
			)
		);

		$this->add_control(
			'apply_type',
			array(
				'label'   => __( 'Apply type', 'jet-smart-filters' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'ajax',
				'options' => array(
					'ajax'   => __( 'AJAX', 'jet-smart-filters' ),
					'reload' => __( 'Page reload', 'jet-smart-filters' )
				),
			)
		);

		$this->add_control(
			'remove_filters_text',
			array(
				'label'     => esc_html__( 'Remove button text', 'jet-smart-filters' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Remove filters', 'jet-smart-filters' ),
			)
		);

		$this->add_control(
			'query_id',
			array(
				'label'       => esc_html__( 'Query ID', 'jet-smart-filters' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'description' => __( 'Set unique query ID if you use multiple widgets of same provider on the page. Same ID you need to set for filtered widget.', 'jet-smart-filters' ),
			)
		);

		// Include Additional Providers Settings
		include jet_smart_filters()->plugin_path( 'includes/widgets/common-controls/additional-providers.php' );

		$this->end_controls_section();

		$css_scheme = apply_filters(
			'jet-smart-filters/widgets/remove-filters/css-scheme',
			array(
				'filter'               => '.jet-filter',
				'remove-filters'        => '.jet-remove-all-filters',
				'remove-filters-button' => '.jet-remove-all-filters__button',
			)
		);

		$this->start_controls_section(
			'section_remove_filters_button_style',
			array(
				'label'      => esc_html__( 'Button', 'jet-smart-filters' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'remove_filters_button_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} ' . $css_scheme['remove-filters-button'],
			)
		);

		$this->start_controls_tabs( 'remove_filters_button_style_tabs' );

		$this->start_controls_tab(
			'remove_filters_button_normal_styles',
			array(
				'label' => esc_html__( 'Normal', 'jet-smart-filters' ),
			)
		);

		$this->add_control(
			'remove_filters_button_normal_color',
			array(
				'label'     => esc_html__( 'Text Color', 'jet-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['remove-filters-button'] => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'remove_filters_button_normal_background_color',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['remove-filters-button'] => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'remove_filters_button_hover_styles',
			array(
				'label' => esc_html__( 'Hover', 'jet-smart-filters' ),
			)
		);

		$this->add_control(
			'remove_filters_button_hover_color',
			array(
				'label'     => esc_html__( 'Text Color', 'jet-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['remove-filters-button'] . ':hover' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'remove_filters_button_hover_background_color',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['remove-filters-button'] . ':hover' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'remove_filters_button_hover_border_color',
			array(
				'label'     => esc_html__( 'Border Color', 'jet-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['remove-filters-button'] . ':hover' => 'border-color: {{VALUE}}',
				),
				'condition' => array(
					'remove_filters_button_border_border!' => '',
				)
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'remove_filters_button_border',
				'label'       => esc_html__( 'Border', 'jet-smart-filters' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['remove-filters-button'],
				'separator'   => 'before'
			)
		);

		$this->add_control(
			'remove_filters_button_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-smart-filters' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['remove-filters-button'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'remove_filters_button_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['remove-filters-button'],
			)
		);

		$this->add_responsive_control(
			'remove_filters_button_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-smart-filters' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['remove-filters-button'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator'  => 'before'
			)
		);

		$this->add_responsive_control(
			'remove_filters_button_margin',
			array(
				'label'      => esc_html__( 'Margin', 'jet-smart-filters' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['remove-filters-button'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'remove_filters_button_alignment',
			array(
				'label'     => esc_html__( 'Alignment', 'jet-smart-filters' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start'   => array(
						'title' => esc_html__( 'Left', 'jet-smart-filters' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'jet-smart-filters' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'  => array(
						'title' => esc_html__( 'Right', 'jet-smart-filters' ),
						'icon'  => 'eicon-text-align-right',
					),
					'stretch'  => array(
						'title' => esc_html__( 'Stretch', 'jet-smart-filters' ),
						'icon'  => 'eicon-text-align-justify',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['remove-filters-button'] => '-webkit-align-self:{{VALUE}}; align-self:{{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {

		jet_smart_filters()->set_filters_used();

		$base_class           = $this->get_name();
		$settings             = $this->get_settings();
		$provider             = ! empty( $settings['content_provider'] ) ? $settings['content_provider'] : '';
		$query_id             = ! empty( $settings['query_id'] ) ? $settings['query_id'] : 'default';
		$additional_providers = jet_smart_filters()->utils->get_additional_providers( $settings );
		$edit_mode            = Plugin::instance()->editor->is_edit_mode();

		echo '<div class="' . $base_class . ' jet-filter">';
		include jet_smart_filters()->get_template( 'common/remove-filters.php' );
		echo '</div>';
	}
}
