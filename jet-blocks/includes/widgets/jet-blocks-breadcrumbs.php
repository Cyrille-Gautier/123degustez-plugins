<?php
/**
 * Class: Jet_Blocks_Breadcrumbs
 * Name: Breadcrumbs
 * Slug: jet-breadcrumbs
 */

namespace Elementor;

use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Widget_Base;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Jet_Blocks_Breadcrumbs extends Jet_Blocks_Base {

	public function get_name() {
		return 'jet-breadcrumbs';
	}

	public function get_title() {
		return esc_html__( 'Breadcrumbs', 'jet-blocks' );
	}

	public function get_icon() {
		return 'jet-blocks-icon-breadcrumbs';
	}

	public function get_jet_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/how-to-add-breadcrumbs-to-the-header-built-with-elementor-breadcrumbs-path-customization/';
	}

	public function get_categories() {
		return array( 'jet-blocks' );
	}

	public function is_reload_preview_required() {
		return true;
	}

	protected function register_controls() {

		$css_scheme = apply_filters(
			'jet-blocks/jet-breadcrumbs/css-scheme',
			array(
				'module'  => '.jet-breadcrumbs',
				'title'   => '.jet-breadcrumbs__title',
				'content' => '.jet-breadcrumbs__content',
				'browse'  => '.jet-breadcrumbs__browse',
				'item'    => '.jet-breadcrumbs__item',
				'sep'     => '.jet-breadcrumbs__item-sep',
				'link'    => '.jet-breadcrumbs__item-link',
				'target'  => '.jet-breadcrumbs__item-target',
			)
		);

		$this->start_controls_section(
			'section_breadcrumbs_settings',
			array(
				'label' => esc_html__( 'General Settings', 'jet-blocks' ),
			)
		);

		$this->add_control(
			'show_on_front',
			array(
				'label'   => esc_html__( 'Show on Front Page', 'jet-blocks' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => '',
				'prefix_class' => 'jet-breadcrumbs-on-front-',
			)
		);

		$this->add_control(
			'show_title',
			array(
				'label'   => esc_html__( 'Show Page Title', 'jet-blocks' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => '',
				'render_type'  => 'template',
				'prefix_class' => 'jet-breadcrumbs-page-title-',
			)
		);

		$this->add_control(
			'title_tag',
			array(
				'label' => esc_html__( 'Title HTML Tag', 'jet-blocks' ),
				'type'  => Controls_Manager::SELECT,
				'options' => array(
					'h1'  => 'H1',
					'h2'  => 'H2',
					'h3'  => 'H3',
					'h4'  => 'H4',
					'h5'  => 'H5',
					'h6'  => 'H6',
					'div' => 'div',
				),
				'default' => 'h3',
				'condition' => array(
					'show_title' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_browse',
			array(
				'label'   => esc_html__( 'Show Prefix', 'jet-blocks' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => '',
			)
		);

		$this->add_control(
			'browse_label',
			array(
				'label'       => esc_html__( 'Prefix for the breadcrumb path', 'jet-blocks' ),
				'label_block' => true,
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Browse:', 'jet-blocks' ),
				'condition' => array(
					'show_browse' => 'yes',
				),
			)
		);

		$this->add_control(
			'enabled_custom_home_page_label',
			array(
				'label'   => esc_html__( 'Custom Home Page Label', 'jet-blocks' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => '',
			)
		);

		$this->add_control(
			'custom_home_page_label',
			array(
				'label'       => esc_html__( 'Label for home page', 'jet-blocks' ),
				'label_block' => true,
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Home', 'jet-blocks' ),
				'condition' => array(
					'enabled_custom_home_page_label' => 'yes',
				),
			)
		);

		$this->add_control(
			'cpt_item_with_links',
			array(
				'label'   => esc_html__( 'Custom post type link', 'jet-blocks' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'cpt_item_title_with_post_type',
			array(
				'label'   => esc_html__( 'Prepend post title with post type', 'jet-blocks' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => array(
					'cpt_item_with_links' => '',
				),
			)
		);

		$page_for_posts = get_option( 'page_for_posts' );

		if ( $page_for_posts ) {
			$this->add_control(
				'show_blog_link',
				array(
					'label'   => esc_html__('Show Blog Link', 'jet-blocks'),
					'type'    => Controls_Manager::SWITCHER,
					'default' => '',
					'description' => esc_html__('If enabled, a link to the blog page will be prepended to the default archives and single posts.', 'jet-blocks'),
				)
			);
		} else {
			$this->add_control(
				'show_blog_link_notice',
				array(
					'type' => Controls_Manager::RAW_HTML,
					'raw'  => sprintf(
						esc_html__( 'The "Show Blog Link" option works only when a static page for posts is enabled. You can set it up %1$shere%2$s.', 'jet-blocks' ),
						'<a href="' . esc_url( admin_url( 'options-reading.php' ) ) . '" target="_blank">',
						'</a>'
					),
				)
			);
		}

		$this->add_control(
			'separator_type',
			array(
				'label' => esc_html__( 'Separator Type', 'jet-blocks' ),
				'type'  => Controls_Manager::SELECT,
				'options' => array(
					'icon'   => esc_html__( 'Icon', 'jet-blocks' ),
					'custom' => esc_html__( 'Custom', 'jet-blocks' ),
				),
				'default' => 'icon',
			)
		);

		$this->__add_advanced_icon_control(
			'icon_separator',
			array(
				'label'   => esc_html__( 'Icon Separator', 'jet-blocks' ),
				'type'    => Controls_Manager::ICON,
				'default' => 'fa fa-angle-right',
				'fa5_default' => array(
					'value'   => 'fas fa-angle-right',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'separator_type' => 'icon',
				),
			)
		);

		$this->add_control(
			'custom_separator',
			array(
				'label'   => esc_html__( 'Custom Separator', 'jet-blocks' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '/',
				'condition' => array(
					'separator_type' => 'custom',
				),
			)
		);

		$this->add_control(
			'path_type',
			array(
				'label'   => esc_html__( 'Path type', 'jet-blocks' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'full',
				'options' => array(
					'full'     => esc_html__( 'Full', 'jet-blocks' ),
					'minified' => esc_html__( 'Minified', 'jet-blocks' ),
				),
			)
		);

		$this->add_responsive_control(
			'alignment',
			array(
				'label'   => esc_html__( 'Alignment', 'jet-blocks' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => esc_html__( 'Left', 'jet-blocks' ),
						'icon'  => 'fa fa-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'jet-blocks' ),
						'icon'  => 'fa fa-align-center',
					),
					'right' => array(
						'title' => esc_html__( 'Right', 'jet-blocks' ),
						'icon'  => 'fa fa-align-right',
					),
					'justify' => array(
						'title' => esc_html__( 'Justified', 'jet-blocks' ),
						'icon'  => 'fa fa-align-justify',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['module'] => 'text-align: {{VALUE}};',
				),
				'prefix_class' => 'jet-breadcrumbs-align%s-',
				'classes'      => 'jet-blocks-text-align-control',
			)
		);

		$this->add_control(
			'order',
			array(
				'label'       => esc_html__( 'Order', 'jet-blocks' ),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT,
				'default'     => '-1',
				'options' => array(
					'-1' => esc_html__( 'Page Title / Breadcrumbs Items', 'jet-blocks' ),
					'1'  => esc_html__( 'Breadcrumbs Items / Page Title', 'jet-blocks' ),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['title'] => 'order: {{VALUE}};',
				),
				'condition' => array(
					'show_title' => 'yes',
				),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'breadcrumbs_settings_desc',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor',
				'separator'       => 'before',
				'raw'             => sprintf(
					esc_html__( 'Additional settings are available in the %s', 'jet-blocks' ),
					'<a target="_blank" href="' . jet_blocks_settings()->get_settings_page_link( 'general' ) . '">' . esc_html__( 'Settings page', 'jet-blocks' ) . '</a>'
				),
			)
		);

		$this->end_controls_section();

		/**
		 * `Page Title` Section
		 */
		$this->__start_controls_section(
			'title_style',
			array(
				'label'      => esc_html__( 'Page Title', 'jet-blocks' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
				'condition'  => array(
					'show_title' => 'yes',
				),
			)
		);

		$this->__add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['title'],
			),
			50
		);

		$this->__add_control(
			'title_color',
			array(
				'label'  => esc_html__( 'Color', 'jet-blocks' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['title'] => 'color: {{VALUE}};',
				),
			),
			25
		);

		$this->__add_control(
			'title_bg_color',
			array(
				'label'  => esc_html__( 'Background Color', 'jet-blocks' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['title'] => 'background-color: {{VALUE}};',
				),
			),
			75
		);

		$this->__add_responsive_control(
			'title_margin',
			array(
				'label'      => esc_html__( 'Margin', 'jet-blocks' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['title'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			25
		);

		$this->__add_responsive_control(
			'title_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-blocks' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['title'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			75
		);

		$this->__add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'title_border',
				'label'          => esc_html__( 'Border', 'jet-blocks' ),
				'placeholder'    => '1px',
				'selector'       => '{{WRAPPER}} ' . $css_scheme['title'],
			),75
		);

		$this->__add_responsive_control(
			'title_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-blocks' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['title'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),75
		);

		$this->__end_controls_section();

		/**
		 * `Breadcrumbs` Section
		 */
		$this->__start_controls_section(
			'breadcrumbs_style',
			array(
				'label'      => esc_html__( 'Breadcrumbs', 'jet-blocks' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->__add_control(
			'breadcrumbs_crumbs_heading',
			array(
				'label' => esc_html__( 'Crumbs Style', 'jet-blocks' ),
				'type'  => Controls_Manager::HEADING,
			),
			25
		);

		$this->__start_controls_tabs( 'breadcrumbs_item_style' );

		$this->__start_controls_tab(
			'breadcrumbs_item_normal',
			array(
				'label' => esc_html__( 'Normal', 'jet-blocks' ),
			)
		);

		$this->__add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'breadcrumbs_item_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['item'] . ' > *',
			),
			50
		);

		$this->__add_control(
			'breadcrumbs_link_color',
			array(
				'label'  => esc_html__( 'Color', 'jet-blocks' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['link'] => 'color: {{VALUE}};',
				),
			),
			25
		);

		$this->__add_control(
			'breadcrumbs_link_bg_color',
			array(
				'label'  => esc_html__( 'Background Color', 'jet-blocks' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['link'] => 'background-color: {{VALUE}};',
				),
			),
			25
		);

		$this->__end_controls_tab();

		$this->__start_controls_tab(
			'breadcrumbs_item_hover',
			array(
				'label' => esc_html__( 'Hover', 'jet-blocks' ),
			)
		);

		$this->__add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'breadcrumbs_link_hover_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['link'] . ':hover',
			),
			50
		);

		$this->__add_control(
			'breadcrumbs_link_hover_color',
			array(
				'label'  => esc_html__( 'Color', 'jet-blocks' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['link'] . ':hover' => 'color: {{VALUE}};',
				),
			),
			25
		);

		$this->__add_control(
			'breadcrumbs_link_hover_bg_color',
			array(
				'label'  => esc_html__( 'Background Color', 'jet-blocks' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['link'] . ':hover' => 'background-color: {{VALUE}};',
				),
			),
			25
		);

		$this->__add_control(
			'breadcrumbs_link_hover_border_color',
			array(
				'label'  => esc_html__( 'Border Color', 'jet-blocks' ),
				'type'   => Controls_Manager::COLOR,
				'condition' => array(
					'breadcrumbs_item_border_border!' => '',
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['link'] . ':hover' => 'border-color: {{VALUE}};',
				),
			),
			75
		);

		$this->__end_controls_tab();

		$this->__start_controls_tab(
			'breadcrumbs_item_target',
			array(
				'label' => esc_html__( 'Current', 'jet-blocks' ),
			)
		);

		$this->__add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'breadcrumbs_target_item_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['target'],
			),
			50
		);

		$this->__add_control(
			'breadcrumbs_target_item_color',
			array(
				'label'  => esc_html__( 'Color', 'jet-blocks' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['target'] => 'color: {{VALUE}};',
				),
			),
			25
		);

		$this->__add_control(
			'breadcrumbs_target_item_bg_color',
			array(
				'label'  => esc_html__( 'Background Color', 'jet-blocks' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['target'] => 'background-color: {{VALUE}};',
				),
			),
			25
		);

		$this->__add_control(
			'breadcrumbs_target_item_border_color',
			array(
				'label'  => esc_html__( 'Border Color', 'jet-blocks' ),
				'type'   => Controls_Manager::COLOR,
				'condition' => array(
					'breadcrumbs_item_border_border!' => '',
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['item'] . ' ' . $css_scheme['target'] => 'border-color: {{VALUE}};',
				),
			),
			75
		);

		$this->__end_controls_tab();

		$this->__end_controls_tabs();

		$this->__add_responsive_control(
			'breadcrumbs_item_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-blocks' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['link'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} ' . $css_scheme['target'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator' => 'before',
			),
			25
		);

		$this->__add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'breadcrumbs_item_border',
				'label'       => esc_html__( 'Border', 'jet-blocks' ),
				'placeholder' => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['link'] . ', {{WRAPPER}} ' . $css_scheme['target'],
			),
			75
		);

		$this->__add_responsive_control(
			'breadcrumbs_item_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-blocks' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['link'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} ' . $css_scheme['target'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			75
		);

		$this->__add_control(
			'breadcrumbs_sep_heading',
			array(
				'label'     => esc_html__( 'Separators Style', 'jet-blocks' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			),
			25
		);

		$this->__add_responsive_control(
			'breadcrumbs_sep_gap',
			array(
				'label'      => esc_html__( 'Gap', 'jet-blocks' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['sep'] => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}};',
				),
			),
			25
		);

		$this->__add_responsive_control(
			'breadcrumbs_sep_icon_size',
			array(
				'label'      => esc_html__( 'Icon Size', 'jet-blocks' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array(
						'min' => 5,
						'max' => 200,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['sep'] => 'font-size: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'separator_type' => 'icon',
				),
			),
			50
		);

		$this->__add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'breadcrumbs_sep_typography',
				'selector'  => '{{WRAPPER}} ' . $css_scheme['sep'],
				'condition' => array(
					'separator_type' => 'custom',
				),
			),
			50
		);

		$this->__add_control(
			'breadcrumbs_sep_color',
			array(
				'label'  => esc_html__( 'Color', 'jet-blocks' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['sep'] => 'color: {{VALUE}};',
				),
			),
			25
		);

		$this->__add_control(
			'breadcrumbs_sep_bg_color',
			array(
				'label'  => esc_html__( 'Background Color', 'jet-blocks' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['sep'] => 'background-color: {{VALUE}};',
				),
			),
			25
		);

		$this->__add_responsive_control(
			'breadcrumbs_sep_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-blocks' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['sep'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			25
		);

		$this->__add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'breadcrumbs_sep_border',
				'label'       => esc_html__( 'Border', 'jet-blocks' ),
				'placeholder' => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['sep'],
			),
			75
		);

		$this->__add_responsive_control(
			'breadcrumbs_sep_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-blocks' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['sep'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			75
		);

		$this->__add_control(
			'breadcrumbs_browse_heading',
			array(
				'label'     => esc_html__( 'Prefix Style', 'jet-blocks' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'show_browse' => 'yes',
				),
			),
			25
		);

		$this->__add_responsive_control(
			'breadcrumbs_browse_gap',
			array(
				'label'      => esc_html__( 'Gap', 'jet-blocks' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['browse'] => 'margin-right: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'show_browse' => 'yes',
				),
			),
			25
		);

		$this->__add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'breadcrumbs_browse_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['browse'],
				'condition' => array(
					'show_browse' => 'yes',
				),
			),
			50
		);

		$this->__add_control(
			'breadcrumbs_browse_color',
			array(
				'label'  => esc_html__( 'Color', 'jet-blocks' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['browse'] => 'color: {{VALUE}};',
				),
				'condition' => array(
					'show_browse' => 'yes',
				),
			),
			25
		);

		$this->__add_control(
			'breadcrumbs_browse_bg_color',
			array(
				'label'  => esc_html__( 'Background Color', 'jet-blocks' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['browse'] => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'show_browse' => 'yes',
				),
			),
			25
		);

		$this->__add_responsive_control(
			'breadcrumbs_browse_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-blocks' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['browse'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'show_browse' => 'yes',
				),
			),
			25
		);

		$this->__add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'breadcrumbs_browse_border',
				'label'       => esc_html__( 'Border', 'jet-blocks' ),
				'placeholder' => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['browse'],
				'condition' => array(
					'show_browse' => 'yes',
				),
			),
			75
		);

		$this->__add_responsive_control(
			'breadcrumbs_browse_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-blocks' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['browse'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'show_browse' => 'yes',
				),
			),
			75
		);

		$this->__end_controls_section();
	}

	protected function render() {

		$this->__open_wrap();

		$settings = $this->get_settings();

		$title_tag = jet_blocks_tools()->validate_html_tag( $settings['title_tag'] );

		$title_format = '<' .$title_tag . ' class="jet-breadcrumbs__title">%s</' . $title_tag . '>';

		$custom_home_page_enabled = ! empty( $settings['enabled_custom_home_page_label'] ) ? $settings['enabled_custom_home_page_label'] : false;
		$custom_home_page_enabled = filter_var( $custom_home_page_enabled, FILTER_VALIDATE_BOOLEAN );
		$custom_home_page_label   = ( $custom_home_page_enabled && ! empty( $settings['custom_home_page_label'] ) ) ? $settings['custom_home_page_label'] : esc_html__( 'Home', 'jet-blocks' );
		
		$args = array(
			'wrapper_format'    => '%1$s%2$s',
			'page_title_format' => $title_format,
			'separator'         => $this->__get_separator(),
			'show_on_front'     => filter_var( $settings['show_on_front'], FILTER_VALIDATE_BOOLEAN ),
			'show_title'        => filter_var( $settings['show_title'], FILTER_VALIDATE_BOOLEAN ),
			'show_browse'       => filter_var( $settings['show_browse'], FILTER_VALIDATE_BOOLEAN ),
			'path_type'         => $settings['path_type'],
			'action'            => 'jet_breadcrumbs/render',
			'cpt_item_with_links' => filter_var( $settings['cpt_item_with_links'], FILTER_VALIDATE_BOOLEAN ),
			'cpt_item_title_with_post_type' => filter_var( $settings['cpt_item_title_with_post_type'], FILTER_VALIDATE_BOOLEAN ),
			'show_blog_link'    => isset( $settings['show_blog_link'] ) ? filter_var( $settings['show_blog_link'], FILTER_VALIDATE_BOOLEAN ) : false,
			'strings' => array(
				'browse'         => $settings['browse_label'],
				'home'           => $custom_home_page_label,
				'error_404'      => esc_html__( '404 Not Found', 'jet-blocks' ),
				'archives'       => esc_html__( 'Archives', 'jet-blocks' ),
				'search'         => esc_html__( 'Search results for &#8220;%s&#8221;', 'jet-blocks' ),
				'paged'          => esc_html__( 'Page %s', 'jet-blocks' ),
				'archive_minute' => esc_html__( 'Minute %s', 'jet-blocks' ),
				'archive_week'   => esc_html__( 'Week %s', 'jet-blocks' ),
			),
			'date_labels' => array(
				'archive_minute_hour' => esc_html_x( 'g:i a', 'minute and hour archives time format', 'jet-blocks' ),
				'archive_minute'      => esc_html_x( 'i', 'minute archives time format', 'jet-blocks' ),
				'archive_hour'        => esc_html_x( 'g a', 'hour archives time format', 'jet-blocks' ),
				'archive_year'        => esc_html_x( 'Y', 'yearly archives date format', 'jet-blocks' ),
				'archive_month'       => esc_html_x( 'F', 'monthly archives date format', 'jet-blocks' ),
				'archive_day'         => esc_html_x( 'j', 'daily archives date format', 'jet-blocks' ),
				'archive_week'        => esc_html_x( 'W', 'weekly archives date format', 'jet-blocks' ),
			),
			'css_namespace' => array(
				'module'    => 'jet-breadcrumbs',
				'content'   => 'jet-breadcrumbs__content',
				'wrap'      => 'jet-breadcrumbs__wrap',
				'browse'    => 'jet-breadcrumbs__browse',
				'item'      => 'jet-breadcrumbs__item',
				'separator' => 'jet-breadcrumbs__item-sep',
				'link'      => 'jet-breadcrumbs__item-link',
				'target'    => 'jet-breadcrumbs__item-target',
			),
			'post_taxonomy' => apply_filters(
				'cx_breadcrumbs/trail_taxonomies',
				jet_blocks_tools()->get_breadcrumbs_post_taxonomy_settings()
			),
		);

		if ( $custom_home_page_enabled ) {
			add_filter( 'cx_breadcrumbs/custom_home_title', array( $this, 'static_home_page_title_off' ) );
		}

		$breadcrumbs = new \CX_Breadcrumbs( $args );

		if ( $custom_home_page_enabled ) {
			remove_filter( 'cx_breadcrumbs/custom_home_title', array( $this, 'static_home_page_title_off' ) );
		}

		$breadcrumbs->get_trail();

		$this->__close_wrap();
	}

	/**
	 * [__get_separator description]
	 * @return [type] [description]
	 */
	public function __get_separator() {
		$separator = '';
		$settings  = $this->get_settings();

		$separator_type = $settings['separator_type'];

		if ( 'icon' === $separator_type ) {
			$separator = $this->__get_icon( 'icon_separator', '<span class="jet-blocks-icon">%s</span>' );
		} else {
			$separator = sprintf( '<span>%s</span>', $settings['custom_separator'] );
		}

		return $separator;
	}

	/**
	 * Disables getting the title of the home page if a static page is selected.
	 *
	 * @return boolean
	 */
	function static_home_page_title_off() {
		return false;
	}
}
