<?php
/**
 * Class: Jet_Elements_Testimonials
 * Name: Testimonials
 * Slug: jet-testimonials
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

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Jet_Elements_Testimonials extends Jet_Elements_Base {

	public function get_name() {
		return 'jet-testimonials';
	}

	public function get_title() {
		return esc_html__( 'Testimonials', 'jet-elements' );
	}

	public function get_icon() {
		return 'jet-elements-icon-testimonials';
	}

	public function get_jet_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/jetelements-testimonials-widget-how-to-add-customers-reviews/';
	}

	public function get_categories() {
		return array( 'cherry' );
	}
	
	public function get_style_depends() { 
		return array( 'jet-testimonials', 'jet-testimonials-skin' ); 
	}

	public function get_script_depends() {
		return array( 'jet-slick' );
	}

	protected function register_controls() {
		$css_scheme = apply_filters(
			'jet-elements/testimonials/css-scheme',
			array(
				'instance'   => '.jet-testimonials__instance',
				'item'       => '.jet-testimonials__item',
				'item_inner' => '.jet-testimonials__item-inner',
				'image'      => '.jet-testimonials__figure',
				'image_tag'  => '.jet-testimonials__tag-img',
				'content'    => '.jet-testimonials__content',
				'icon'       => '.jet-testimonials__icon',
				'icon_inner' => '.jet-testimonials__icon-inner',
				'title'      => '.jet-testimonials__title',
				'comment'    => '.jet-testimonials__comment',
				'name'       => '.jet-testimonials__name',
				'position'   => '.jet-testimonials__position',
				'date'       => '.jet-testimonials__date',
				'rating'     => '.jet-testimonials__rating',
				'arrow'      => '.jet-testimonials__instance .jet-arrow',
				'dots'       => '.jet-testimonials__instance .jet-slick-dots',
			)
		);

		$this->start_controls_section(
			'section_items_data',
			array(
				'label' => esc_html__( 'Items', 'jet-elements' ),
			)
		);

		do_action( 'jet-engine-query-gateway/control', $this, 'item_list' );

		$repeater = new Repeater();

		$repeater->add_control(
			'item_image',
			array(
				'label'   => esc_html__( 'Image', 'jet-elements' ),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->_add_advanced_icon_control(
			'item_icon',
			array(
				'label'       => esc_html__( 'Icon', 'jet-elements' ),
				'type'        => Controls_Manager::ICON,
				'label_block' => true,
				'file'        => '',
			),
			$repeater
		);

		$repeater->add_control(
			'item_title',
			array(
				'label'   => esc_html__( 'Title', 'jet-elements' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'item_comment',
			array(
				'label'   => esc_html__( 'Comment', 'jet-elements' ),
				'type'    => Controls_Manager::TEXTAREA,
				'dynamic' => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'item_name',
			array(
				'label'   => esc_html__( 'Name', 'jet-elements' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'item_position',
			array(
				'label'   => esc_html_x( 'Position', 'Position at work', 'jet-elements' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'item_date',
			array(
				'label'   => esc_html__( 'Date', 'jet-elements' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'item_link',
			array(
				'label'   => esc_html__( 'Link', 'jet-elements' ),
				'type'    => Controls_Manager::URL,
				'dynamic' => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'item_rating',
			array(
				'label'   => esc_html__( 'Rating', 'jet-elements' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 0,
				'options' => array(
					'0' => esc_html__( 'Hidden', 'jet-elements' ),
					'1' => 1,
					'2' => 2,
					'3' => 3,
					'4' => 4,
					'5' => 5,
				),
			)
		);

		$this->add_control(
			'item_list',
			array(
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array(
						'item_comment'  => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'jet-elements' ),
						'item_name'     => esc_html__( 'Mary Scott', 'jet-elements' ),
						'item_position' => esc_html__( 'Founder & CEO', 'jet-elements' ),
						'item_date'     => esc_html__( 'Thursday, August 31, 2017', 'jet-elements' ),
					),
					array(
						'item_comment'  => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'jet-elements' ),
						'item_name'     => esc_html__( 'John Borthwick', 'jet-elements' ),
						'item_position' => esc_html__( 'Founder & CEO', 'jet-elements' ),
						'item_date'     => esc_html__( 'Thursday, August 31, 2017', 'jet-elements' ),
					),
					array(
						'item_comment'  => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'jet-elements' ),
						'item_name'     => esc_html__( 'Jane Bill', 'jet-elements' ),
						'item_position' => esc_html__( 'Founder & CEO', 'jet-elements' ),
						'item_date'     => esc_html__( 'Thursday, August 31, 2017', 'jet-elements' ),
					),
					array(
						'item_comment'  => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'jet-elements' ),
						'item_name'     => esc_html__( 'David Anderson', 'jet-elements' ),
						'item_position' => esc_html__( 'Founder & CEO', 'jet-elements' ),
						'item_date'     => esc_html__( 'Thursday, August 31, 2017', 'jet-elements' ),
					),
					array(
						'item_comment'  => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'jet-elements' ),
						'item_name'     => esc_html__( 'Susan O’Neill', 'jet-elements' ),
						'item_position' => esc_html__( 'Founder & CEO', 'jet-elements' ),
						'item_date'     => esc_html__( 'Thursday, August 31, 2017', 'jet-elements' ),
					),
					array(
						'item_comment'  => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'jet-elements' ),
						'item_name'     => esc_html__( 'Ryan King', 'jet-elements' ),
						'item_position' => esc_html__( 'Founder & CEO', 'jet-elements' ),
						'item_date'     => esc_html__( 'Thursday, August 31, 2017', 'jet-elements' ),
					),
				),
				'title_field' => '{{{ item_title }}}',
			)
		);

		$this->add_control(
			'item_title_tag',
			array(
				'label'     => esc_html__( 'Title HTML Tag', 'jet-elements' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'h5',
				'options'   => jet_elements_tools()->get_available_title_html_tags(),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_settings',
			array(
				'label' => esc_html__( 'Settings', 'jet-elements' ),
			)
		);

		$this->add_control(
			'equal_height_cols',
			array(
				'label'        => esc_html__( 'Equal Items Height', 'jet-elements' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-elements' ),
				'label_off'    => esc_html__( 'No', 'jet-elements' ),
				'return_value' => 'true',
				'default'      => '',
			)
		);

		$this->add_control(
			'image_lazyload',
			array(
				'label'        => esc_html__( 'Lazy Load Images', 'jet-elements' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-elements' ),
				'label_off'    => esc_html__( 'No', 'jet-elements' ),
				'return_value' => 'true',
				'default'      => 'true',
			)
		);

		$this->add_responsive_control(
			'slides_to_show',
			array(
				'label'              => esc_html__( 'Slides to Show', 'jet-elements' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => '1',
				'options'            => jet_elements_tools()->get_select_range( 10 ),
				'frontend_available' => true,
				'render_type'        => 'template',
			)
		);

		$this->add_responsive_control(
			'slides_to_scroll',
			array(
				'label'     => esc_html__( 'Slides to Scroll', 'jet-elements' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '1',
				'options'   => jet_elements_tools()->get_select_range( 10 ),
				'frontend_available' => true,
				'render_type'        => 'template',
			)
		);

		$this->add_control(
			'pause_on_hover',
			array(
				'label'        => esc_html__( 'Pause on Hover', 'jet-elements' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-elements' ),
				'label_off'    => esc_html__( 'No', 'jet-elements' ),
				'return_value' => 'true',
				'default'      => '',
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label'        => esc_html__( 'Autoplay', 'jet-elements' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-elements' ),
				'label_off'    => esc_html__( 'No', 'jet-elements' ),
				'return_value' => 'true',
				'default'      => 'true',
			)
		);

		$this->add_control(
			'autoplay_speed',
			array(
				'label'     => esc_html__( 'Autoplay Speed', 'jet-elements' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 5000,
				'condition' => array(
					'autoplay' => 'true',
				),
			)
		);

		$this->add_control(
			'infinite',
			array(
				'label'        => esc_html__( 'Infinite Loop', 'jet-elements' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-elements' ),
				'label_off'    => esc_html__( 'No', 'jet-elements' ),
				'return_value' => 'true',
				'default'      => 'true',
			)
		);

		$this->add_control(
			'centered',
			array(
				'label'        => esc_html__( 'Center Mode', 'jet-elements' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-elements' ),
				'label_off'    => esc_html__( 'No', 'jet-elements' ),
				'return_value' => 'true',
				'default'      => 'false',
			)
		);

		$this->add_control(
			'centered_padding',
			array(
				'label'     => esc_html__( 'Side padding', 'jet-elements' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 50,
				'condition' => array(
					'centered' => 'true',
				),
			)
		);

		$this->add_control(
			'shuffle',
			array(
				'label'        => esc_html__( 'Slides Will be Shuffled', 'jet-elements' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-elements' ),
				'label_off'    => esc_html__( 'No', 'jet-elements' ),
				'return_value' => 'true',
				'default'      => 'false',
			)
		);

		$this->add_control(
			'adaptive_height',
			array(
				'label'        => esc_html__( 'Adaptive Height', 'jet-elements' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-elements' ),
				'label_off'    => esc_html__( 'No', 'jet-elements' ),
				'return_value' => 'true',
				'default'      => 'true',
			)
		);

		$this->add_control(
			'effect',
			array(
				'label'   => esc_html__( 'Effect', 'jet-elements' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'slide',
				'options' => array(
					'slide' => esc_html__( 'Slide', 'jet-elements' ),
					'fade'  => esc_html__( 'Fade', 'jet-elements' ),
				),
				'condition' => array(
					'slides_to_show' => '1',
				),
			)
		);

		$this->add_control(
			'speed',
			array(
				'label'   => esc_html__( 'Animation Speed', 'jet-elements' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 500,
			)
		);

		$this->add_control(
			'arrows',
			array(
				'label'        => esc_html__( 'Show Arrows Navigation', 'jet-elements' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-elements' ),
				'label_off'    => esc_html__( 'No', 'jet-elements' ),
				'return_value' => 'true',
				'default'      => 'false',
			)
		);

		$this->_add_advanced_icon_control(
			'prev_arrow',
			array(
				'label'       => esc_html__( 'Prev Arrow Icon', 'jet-elements' ),
				'type'        => Controls_Manager::ICON,
				'label_block' => true,
				'file'        => '',
				'default'     => 'fa fa-angle-left',
				'fa5_default' => array(
					'value'   => 'fas fa-angle-left',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'arrows' => 'true',
				),
			)
		);

		$this->_add_advanced_icon_control(
			'next_arrow',
			array(
				'label'       => esc_html__( 'Next Arrow Icon', 'jet-elements' ),
				'type'        => Controls_Manager::ICON,
				'label_block' => true,
				'file'        => '',
				'default'     => 'fa fa-angle-right',
				'fa5_default' => array(
					'value'   => 'fas fa-angle-right',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'arrows' => 'true',
				),
			)
		);

		$this->add_control(
			'dots',
			array(
				'label'        => esc_html__( 'Show Dots Navigation', 'jet-elements' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-elements' ),
				'label_off'    => esc_html__( 'No', 'jet-elements' ),
				'return_value' => 'true',
				'default'      => 'true',
			)
		);

		$this->add_control(
			'rating_heading',
			array(
				'label'     => esc_html__( 'Rating', 'jet-elements' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'rating_star_style',
			array(
				'label' => esc_html__( 'Stars Style', 'jet-elements' ),
				'type'  => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => array(
					'solid' => array(
						'title' => esc_html__( 'Solid', 'jet-elements' ),
						'icon'  => 'eicon-star',
					),
					'outline' => array(
						'title' => esc_html__( 'Outline', 'jet-elements' ),
						'icon'  => 'eicon-star-o',
					),
				),
				'default' => 'solid',
			)
		);

		$this->add_control(
			'rating_active_star_style',
			array(
				'label' => esc_html__( 'Active Stars Style', 'jet-elements' ),
				'type'  => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => array(
					'solid' => array(
						'title' => esc_html__( 'Solid', 'jet-elements' ),
						'icon'  => 'eicon-star',
					),
					'outline' => array(
						'title' => esc_html__( 'Outline', 'jet-elements' ),
						'icon'  => 'eicon-star-o',
					),
				),
				'default' => 'solid',
			)
		);

		$this->end_controls_section();

		$this->_start_controls_section(
			'section_item_style',
			array(
				'label'      => esc_html__( 'Item', 'jet-elements' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->_add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'item_background',
				'selector' => '{{WRAPPER}} ' . $css_scheme['item_inner'],
			),
			25
		);

		$this->_add_responsive_control(
			'item_margin',
			array(
				'label'       => esc_html__( 'Item Margin', 'jet-elements' ),
				'type'        => Controls_Manager::DIMENSIONS,
				'size_units'  => array( 'px', 'custom' ),
				'render_type' => 'template',
				'selectors'   => array(
					'{{WRAPPER}} ' . $css_scheme['item_inner'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			50
		);

		$this->_add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'item_border',
				'label'       => esc_html__( 'Border', 'jet-elements' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['item_inner'],
			),
			50
		);

		$this->_add_responsive_control(
			'item_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-elements' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['item_inner'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			75
		);

		$this->_add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'item_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['item_inner'],
			),
			100
		);

		$this->_add_control(
			'order_heading',
			array(
				'label'     => esc_html__( 'Order', 'jet-elements' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			),
			100
		);

		$this->_add_control(
			'image_order',
			array(
				'label'   => esc_html__( 'Image Order', 'jet-elements' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 1,
				'min'     => 1,
				'max'     => 8,
				'step'    => 1,
				'selectors' => array(
					'{{WRAPPER}} '. $css_scheme['image'] => 'order: {{VALUE}};',
				),
			),
			100
		);

		$this->_add_control(
			'icon_order',
			array(
				'label'   => esc_html__( 'Icon Order', 'jet-elements' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 2,
				'min'     => 1,
				'max'     => 8,
				'step'    => 1,
				'selectors' => array(
					'{{WRAPPER}} '. $css_scheme['icon'] => 'order: {{VALUE}};',
				),
			),
			100
		);

		$this->_add_control(
			'title_order',
			array(
				'label'   => esc_html__( 'Title Order', 'jet-elements' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 3,
				'min'     => 1,
				'max'     => 8,
				'step'    => 1,
				'selectors' => array(
					'{{WRAPPER}} '. $css_scheme['title'] => 'order: {{VALUE}};',
				),
			),
			100
		);

		$this->_add_control(
			'comment_order',
			array(
				'label'   => esc_html__( 'Description Order', 'jet-elements' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 4,
				'min'     => 1,
				'max'     => 8,
				'step'    => 1,
				'selectors' => array(
					'{{WRAPPER}} '. $css_scheme['comment'] => 'order: {{VALUE}};',
				),
			),
			100
		);

		$this->_add_control(
			'name_order',
			array(
				'label'   => esc_html__( 'Name Order', 'jet-elements' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 5,
				'min'     => 1,
				'max'     => 8,
				'step'    => 1,
				'selectors' => array(
					'{{WRAPPER}} '. $css_scheme['name'] => 'order: {{VALUE}};',
				),
			),
			100
		);

		$this->_add_control(
			'position_order',
			array(
				'label'   => esc_html__( 'Position Order', 'jet-elements' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 6,
				'min'     => 1,
				'max'     => 8,
				'step'    => 1,
				'selectors' => array(
					'{{WRAPPER}} '. $css_scheme['position'] => 'order: {{VALUE}};',
				),
			),
			100
		);

		$this->_add_control(
			'date_order',
			array(
				'label'   => esc_html__( 'Date Order', 'jet-elements' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 7,
				'min'     => 1,
				'max'     => 8,
				'step'    => 1,
				'selectors' => array(
					'{{WRAPPER}} '. $css_scheme['date'] => 'order: {{VALUE}};',
				),
			),
			100
		);

		$this->_add_control(
			'rating_order',
			array(
				'label'   => esc_html__( 'Rating Order', 'jet-elements' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 8,
				'min'     => 1,
				'max'     => 8,
				'step'    => 1,
				'selectors' => array(
					'{{WRAPPER}} '. $css_scheme['rating'] => 'order: {{VALUE}};',
				),
			),
			100
		);

		$this->_add_control(
			'content_alignment',
			array(
				'label'   => esc_html__( 'Cover Content Vertical Alignment', 'jet-elements' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'center',
				'options' => array(
					'flex-start'    => esc_html__( 'Top', 'jet-elements' ),
					'center'        => esc_html__( 'Center', 'jet-elements' ),
					'flex-end'      => esc_html__( 'Bottom', 'jet-elements' ),
					'space-between' => esc_html__( 'Space between', 'jet-elements' ),
				),
				'selectors'  => array(
					'{{WRAPPER}} '. $css_scheme['content'] => 'justify-content: {{VALUE}};',
				),
			),
			100
		);

		$this->_end_controls_section();

		/*
		 * Image section
		 */
		$this->_start_controls_section(
			'section_image_style',
			array(
				'label'      => esc_html__( 'Image', 'jet-elements' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->_add_responsive_control(
			'image_alignment',
			array(
				'label'   => esc_html__( 'Alignment', 'jet-elements' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'flex-start' => array(
						'title' => esc_html__( 'Start', 'jet-elements' ),
						'icon'  => ! is_rtl() ? 'eicon-h-align-left' : 'eicon-h-align-right',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'jet-elements' ),
						'icon'  => 'eicon-h-align-center',
					),
					'flex-end' => array(
						'title' => esc_html__( 'End', 'jet-elements' ),
						'icon'  => ! is_rtl() ? 'eicon-h-align-right' : 'eicon-h-align-left',
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['image'] => 'align-self: {{VALUE}};',
				),
			),
			50
		);

		$this->_add_responsive_control(
			'image_width',
			array(
				'label'      => esc_html__( 'Width', 'jet-elements' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px', '%', 'custom'
				),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 1000,
					),
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default' => array(
					'size' => 150,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['image_tag'] => 'width: {{SIZE}}{{UNIT}};',
				),
			),
			25
		);

		$this->_add_responsive_control(
			'image_height',
			array(
				'label'      => esc_html__( 'Height', 'jet-elements' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px', '%', 'custom'
				),
				'range'      => array(
					'px' => array(
						'min' => 50,
						'max' => 800,
					),
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default' => array(
					'size' => 150,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['image_tag'] => 'height: {{SIZE}}{{UNIT}};',
				),
			),
			25
		);

		$this->_add_responsive_control(
			'image_margin',
			array(
				'label'      => __( 'Margin', 'jet-elements' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['item'] . ' ' . $css_scheme['image'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			75
		);

		$this->_add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'image_border',
				'label'       => esc_html__( 'Border', 'jet-elements' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['image'],
			),
			75
		);

		$this->_add_control(
			'image_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-elements' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['image'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			75
		);

		$this->_add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'image_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['image'],
			),
			100
		);

		$this->_end_controls_section();

		/**
		 * Icon Style Section
		 */
		$this->_start_controls_section(
			'section_icon_style',
			array(
				'label'      => esc_html__( 'Icon', 'jet-elements' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->_add_responsive_control(
			'icon_box_alignment',
			array(
				'label'   => esc_html__( 'Alignment', 'jet-elements' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => array(
					'flex-start' => array(
						'title' => esc_html__( 'Start', 'jet-elements' ),
						'icon'  => ! is_rtl() ? 'eicon-h-align-left' : 'eicon-h-align-right',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'jet-elements' ),
						'icon'  => 'eicon-h-align-center',
					),
					'flex-end' => array(
						'title' => esc_html__( 'End', 'jet-elements' ),
						'icon'  => ! is_rtl() ? 'eicon-h-align-right' : 'eicon-h-align-left',
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['icon'] => 'align-self: {{VALUE}};',
				),
			),
			50
		);

		$this->_add_control(
			'icon_color',
			array(
				'label' => esc_html__( 'Icon Color', 'jet-elements' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['icon'] . ' i' => 'color: {{VALUE}}',
					'{{WRAPPER}} ' . $css_scheme['icon'] . ' svg' => 'fill: {{VALUE}}',
				),
			),
			25
		);

		$this->_add_control(
			'icon_bg_color',
			array(
				'label' => esc_html__( 'Icon Background Color', 'jet-elements' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['icon'] . ' ' . $css_scheme['icon_inner'] => 'background-color: {{VALUE}}',
				),
			),
			25
		);

		$this->_add_responsive_control(
			'icon_font_size',
			array(
				'label'      => esc_html__( 'Icon Font Size', 'jet-elements' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px', 'em', 'rem', 'custom'
				),
				'range'      => array(
					'px' => array(
						'min' => 18,
						'max' => 200,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['icon'] . ' i' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} ' . $css_scheme['icon'] => 'font-size: {{SIZE}}{{UNIT}}',
				),
			),
			50
		);

		$this->_add_responsive_control(
			'icon_size',
			array(
				'label'      => esc_html__( 'Icon Box Size', 'jet-elements' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px', 'em', '%', 'custom'
				),
				'range'      => array(
					'px' => array(
						'min' => 18,
						'max' => 200,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['icon'] . ' ' . $css_scheme['icon_inner'] => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			),
			50
		);

		$this->_add_responsive_control(
			'icon_box_margin',
			array(
				'label'      => __( 'Margin', 'jet-elements' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['icon'] . ' ' . $css_scheme['icon_inner'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			75
		);

		$this->_add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'icon_border',
				'label'       => esc_html__( 'Border', 'jet-elements' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['icon'] . ' ' . $css_scheme['icon_inner'],
			),
			75
		);

		$this->_add_control(
			'icon_box_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-elements' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['icon'] . ' ' . $css_scheme['icon_inner'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			75
		);

		$this->_add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'icon_box_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['icon'] . ' ' . $css_scheme['icon_inner'],
			),
			100
		);

		$this->_end_controls_section();

		/**
		 * Title Style Section
		 */
		$this->_start_controls_section(
			'section_title_style',
			array(
				'label'      => esc_html__( 'Title', 'jet-elements' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->_add_control(
			'title_custom_width',
			array(
				'label'        => esc_html__( 'Custom width', 'jet-elements' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-elements' ),
				'label_off'    => esc_html__( 'No', 'jet-elements' ),
				'return_value' => 'yes',
				'default'      => 'false',
			),
			100
		);

		$this->_add_responsive_control(
			'title_width',
			array(
				'label'      => esc_html__( 'Width', 'jet-elements' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px', '%', 'custom'
				),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 1000,
					),
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default' => array(
					'size' => 350,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['title'] => 'width: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'title_custom_width' => 'yes',
				),
			),
			100
		);

		$this->_add_responsive_control(
			'title_text_alignment',
			array(
				'label'   => esc_html__( 'Text Alignment', 'jet-elements' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => array(
					'left'    => array(
						'title' => esc_html__( 'Left', 'jet-elements' ),
						'icon'  => 'fa fa-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'jet-elements' ),
						'icon'  => 'fa fa-align-center',
					),
					'right' => array(
						'title' => esc_html__( 'Right', 'jet-elements' ),
						'icon'  => 'fa fa-align-right',
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['title'] => 'text-align: {{VALUE}};',
				),
				'classes' => 'jet-elements-text-align-control',
			),
			50
		);

		$this->_add_responsive_control(
			'title_alignment',
			array(
				'label'   => esc_html__( 'Alignment', 'jet-elements' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => array(
					'flex-start' => array(
						'title' => esc_html__( 'Start', 'jet-elements' ),
						'icon'  => ! is_rtl() ? 'eicon-h-align-left' : 'eicon-h-align-right',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'jet-elements' ),
						'icon'  => 'eicon-h-align-center',
					),
					'flex-end' => array(
						'title' => esc_html__( 'End', 'jet-elements' ),
						'icon'  => ! is_rtl() ? 'eicon-h-align-right' : 'eicon-h-align-left',
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['title'] => 'align-self: {{VALUE}};',
				),
			),
			50
		);

		$this->_add_control(
			'title_color',
			array(
				'label'  => esc_html__( 'Color', 'jet-elements' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['title'] => 'color: {{VALUE}}',
				),
			),
			25
		);

		$this->_add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['title'],
				'global' => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
			),
			50
		);

		$this->_add_responsive_control(
			'title_padding',
			array(
				'label'      => __( 'Padding', 'jet-elements' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['title'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			50
		);

		$this->_add_responsive_control(
			'title_margin',
			array(
				'label'      => __( 'Margin', 'jet-elements' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['title'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			75
		);

		$this->_end_controls_section();

		/**
		 * Comment Style Section
		 */
		$this->_start_controls_section(
			'section_comment_style',
			array(
				'label'      => esc_html__( 'Comment', 'jet-elements' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->_add_responsive_control(
			'comment_alignment',
			array(
				'label'   => esc_html__( 'Alignment', 'jet-elements' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => array(
					'flex-start' => array(
						'title' => esc_html__( 'Start', 'jet-elements' ),
						'icon'  => ! is_rtl() ? 'eicon-h-align-left' : 'eicon-h-align-right',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'jet-elements' ),
						'icon'  => 'eicon-h-align-center',
					),
					'flex-end' => array(
						'title' => esc_html__( 'End', 'jet-elements' ),
						'icon'  => ! is_rtl() ? 'eicon-h-align-right' : 'eicon-h-align-left',
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['comment'] => 'align-self: {{VALUE}};',
				),
			),
			50
		);

		$this->_add_responsive_control(
			'comment_text_alignment',
			array(
				'label'   => esc_html__( 'Text Alignment', 'jet-elements' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => array(
					'left'    => array(
						'title' => esc_html__( 'Left', 'jet-elements' ),
						'icon'  => 'fa fa-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'jet-elements' ),
						'icon'  => 'fa fa-align-center',
					),
					'right' => array(
						'title' => esc_html__( 'Right', 'jet-elements' ),
						'icon'  => 'fa fa-align-right',
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['comment'] => 'text-align: {{VALUE}};',
				),
				'classes' => 'jet-elements-text-align-control',
			),
			50
		);

		$this->_add_control(
			'comment_color',
			array(
				'label'  => esc_html__( 'Color', 'jet-elements' ),
				'type'   => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['comment'] => 'color: {{VALUE}}',
				),
			),
			25
		);

		$this->_add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'comment_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['comment'],
				'global' => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
			),
			50
		);

		$this->_add_responsive_control(
			'comment_width',
			array(
				'label'      => esc_html__( 'Width', 'jet-elements' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px', '%', 'custom'
				),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 1000,
					),
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default' => array(
					'size' => 350,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['comment'] => 'width: {{SIZE}}{{UNIT}};',
				),
			),
			25
		);

		$this->_add_control(
			'use_comment_corner',
			array(
				'label'        => esc_html__( 'Use comment corner', 'jet-elements' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-elements' ),
				'label_off'    => esc_html__( 'No', 'jet-elements' ),
				'return_value' => 'yes',
				'default'      => 'false',
			),
			75
		);

		$this->_add_control(
			'comment_corner_color',
			array(
				'label'   => esc_html__( 'Color', 'jet-elements' ),
				'type'    => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['comment'] . ':after' => 'border-top-color: {{VALUE}};',
				),
				'condition' => array(
					'use_comment_corner' => 'yes',
				),
			),
			75
		);

		$this->_add_responsive_control(
			'comment_corner_position',
			array(
				'label'      => esc_html__( 'Corner Position', 'jet-elements' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%', 'custom' ),
				'range'      => array(
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default' => array(
					'size' => 50,
					'unit' => '%',
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['comment'] . ':after' => 'left: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'use_comment_corner' => 'yes',
				),
			),
			75
		);

		$this->_add_responsive_control(
			'comment_corner_width',
			array(
				'label'      => esc_html__( 'Corner Width', 'jet-elements' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'custom' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'default' => array(
					'size' => 10,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['comment'] . ':after' => 'border-right-width: {{SIZE}}{{UNIT}}; margin-left: calc({{SIZE}}{{UNIT}}/-2);',
				),
				'condition' => array(
					'use_comment_corner' => 'yes',
				),
			),
			75
		);

		$this->_add_responsive_control(
			'comment_corner_height',
			array(
				'label'      => esc_html__( 'Corner Height', 'jet-elements' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'custom' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'default' => array(
					'size' => 10,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['comment'] . ':after' => 'border-top-width: {{SIZE}}{{UNIT}}; bottom: -{{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'use_comment_corner' => 'yes',
				),
			),
			75
		);

		$this->_add_responsive_control(
			'comment_corner_skew',
			array(
				'label'      => esc_html__( 'Corner Skew', 'jet-elements' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'custom' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'default' => array(
					'size' => 10,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['comment'] . ':after' => 'border-left-width: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'use_comment_corner' => 'yes',
				),
			),
			75
		);

		$this->_add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'comment_background',
				'selector' => '{{WRAPPER}} ' . $css_scheme['comment'],
				'fields_options' => array(
					'color' => array(
						'global' => array(
							'default' => Global_Colors::COLOR_PRIMARY,
						),
					),
				),
			),
			25
		);

		$this->_add_responsive_control(
			'comment_padding',
			array(
				'label'      => __( 'Padding', 'jet-elements' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'custom' ),
				'default' => array(
					'top'    => 25,
					'right'  => 15,
					'bottom' => 25,
					'left'   => 15,
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['comment'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			50
		);

		$this->_add_responsive_control(
			'comment_margin',
			array(
				'label'      => __( 'Margin', 'jet-elements' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['comment'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			100
		);

		$this->_add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'comment_border',
				'label'       => esc_html__( 'Border', 'jet-elements' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['comment'],
			),
			75
		);

		$this->_add_control(
			'comment_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-elements' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'custom' ),
				'default' => array(
					'top'    => 5,
					'right'  => 5,
					'bottom' => 5,
					'left'   => 5,
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['comment'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			75
		);

		$this->_add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'comment_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['comment'],
			),
			100
		);

		$this->_end_controls_section();

		/**
		 * Name Style Section
		 */
		$this->_start_controls_section(
			'section_name_style',
			array(
				'label'      => esc_html__( 'Name', 'jet-elements' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->_add_responsive_control(
			'name_alignment',
			array(
				'label'   => esc_html__( 'Alignment', 'jet-elements' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => array(
					'flex-start' => array(
						'title' => esc_html__( 'Start', 'jet-elements' ),
						'icon'  => ! is_rtl() ? 'eicon-h-align-left' : 'eicon-h-align-right',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'jet-elements' ),
						'icon'  => 'eicon-h-align-center',
					),
					'flex-end' => array(
						'title' => esc_html__( 'End', 'jet-elements' ),
						'icon'  => ! is_rtl() ? 'eicon-h-align-right' : 'eicon-h-align-left',
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['name'] => 'align-self: {{VALUE}};',
				),
			),
			50
		);

		$this->_add_responsive_control(
			'name_text_alignment',
			array(
				'label'   => esc_html__( 'Text Alignment', 'jet-elements' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => array(
					'left'    => array(
						'title' => esc_html__( 'Left', 'jet-elements' ),
						'icon'  => 'fa fa-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'jet-elements' ),
						'icon'  => 'fa fa-align-center',
					),
					'right' => array(
						'title' => esc_html__( 'Right', 'jet-elements' ),
						'icon'  => 'fa fa-align-right',
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['name'] => 'text-align: {{VALUE}};',
				),
				'classes' => 'jet-elements-text-align-control',
			),
			50
		);

		$this->_add_control(
			'name_custom_width',
			array(
				'label'        => esc_html__( 'Custom width', 'jet-elements' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-elements' ),
				'label_off'    => esc_html__( 'No', 'jet-elements' ),
				'return_value' => 'yes',
				'default'      => 'false',
			),
			100
		);

		$this->_add_responsive_control(
			'name_width',
			array(
				'label'      => esc_html__( 'Width', 'jet-elements' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px', '%', 'custom'
				),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 1000,
					),
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default' => array(
					'size' => 350,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['name'] => 'width: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'name_custom_width' => 'yes',
				),
			),
			100
		);

		$this->_add_control(
			'name_color',
			array(
				'label'  => esc_html__( 'Color', 'jet-elements' ),
				'type'   => Controls_Manager::COLOR,
				'global' => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['name'] => 'color: {{VALUE}}',
				),
			),
			25
		);

		$this->_add_control(
			'name_hover_color',
			array(
				'label'  => esc_html__( 'Hover Color', 'jet-elements' ),
				'type'   => Controls_Manager::COLOR,
				'global' => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['name'] . ' a:hover' => 'color: {{VALUE}}',
				),
			),
			25
		);

		$this->_add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'name_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['name'],
				'global' => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
			),
			50
		);

		$this->_add_responsive_control(
			'name_padding',
			array(
				'label'      => __( 'Padding', 'jet-elements' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['name'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			100
		);

		$this->_add_responsive_control(
			'name_margin',
			array(
				'label'      => __( 'Margin', 'jet-elements' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['name'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			50
		);

		$this->_end_controls_section();

		/**
		 * Position Style Section
		 */
		$this->_start_controls_section(
			'section_position_style',
			array(
				'label'      => esc_html_x( 'Position', 'Position at work', 'jet-elements' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->_add_responsive_control(
			'position_alignment',
			array(
				'label'   => esc_html__( 'Alignment', 'jet-elements' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => array(
					'flex-start' => array(
						'title' => esc_html__( 'Start', 'jet-elements' ),
						'icon'  => ! is_rtl() ? 'eicon-h-align-left' : 'eicon-h-align-right',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'jet-elements' ),
						'icon'  => 'eicon-h-align-center',
					),
					'flex-end' => array(
						'title' => esc_html__( 'End', 'jet-elements' ),
						'icon'  => ! is_rtl() ? 'eicon-h-align-right' : 'eicon-h-align-left',
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['position'] => 'align-self: {{VALUE}};',
				),
			),
			50
		);

		$this->_add_responsive_control(
			'position_text_alignment',
			array(
				'label'   => esc_html__( 'Text Alignment', 'jet-elements' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => array(
					'left'    => array(
						'title' => esc_html__( 'Left', 'jet-elements' ),
						'icon'  => 'fa fa-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'jet-elements' ),
						'icon'  => 'fa fa-align-center',
					),
					'right' => array(
						'title' => esc_html__( 'Right', 'jet-elements' ),
						'icon'  => 'fa fa-align-right',
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['position'] => 'text-align: {{VALUE}};',
				),
				'classes' => 'jet-elements-text-align-control',
			),
			50
		);

		$this->_add_control(
			'position_custom_width',
			array(
				'label'        => esc_html__( 'Custom width', 'jet-elements' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-elements' ),
				'label_off'    => esc_html__( 'No', 'jet-elements' ),
				'return_value' => 'yes',
				'default'      => 'false',
			),
			100
		);

		$this->_add_responsive_control(
			'position_width',
			array(
				'label'      => esc_html__( 'Width', 'jet-elements' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px', '%', 'custom'
				),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 1000,
					),
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default' => array(
					'size' => 350,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['position'] => 'width: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'position_custom_width' => 'yes',
				),
			),
			100
		);

		$this->_add_control(
			'position_color',
			array(
				'label'  => esc_html__( 'Color', 'jet-elements' ),
				'type'   => Controls_Manager::COLOR,
				'global' => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['position'] => 'color: {{VALUE}}',
				),
			),
			25
		);

		$this->_add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'position_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['position'],
				'global' => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
			),
			50
		);

		$this->_add_responsive_control(
			'position_padding',
			array(
				'label'      => __( 'Padding', 'jet-elements' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['position'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			100
		);

		$this->_add_responsive_control(
			'position_margin',
			array(
				'label'      => __( 'Margin', 'jet-elements' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['position'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			50
		);

		$this->_end_controls_section();

		/**
		 * Date Style Section
		 */
		$this->_start_controls_section(
			'section_date_style',
			array(
				'label'      => esc_html__( 'Date', 'jet-elements' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->_add_responsive_control(
			'date_alignment',
			array(
				'label'   => esc_html__( 'Alignment', 'jet-elements' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => array(
					'flex-start' => array(
						'title' => esc_html__( 'Start', 'jet-elements' ),
						'icon'  => ! is_rtl() ? 'eicon-h-align-left' : 'eicon-h-align-right',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'jet-elements' ),
						'icon'  => 'eicon-h-align-center',
					),
					'flex-end' => array(
						'title' => esc_html__( 'End', 'jet-elements' ),
						'icon'  => ! is_rtl() ? 'eicon-h-align-right' : 'eicon-h-align-left',
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['date'] => 'align-self: {{VALUE}};',
				),
			),
			50
		);

		$this->_add_responsive_control(
			'date_text_alignment',
			array(
				'label'   => esc_html__( 'Text Alignment', 'jet-elements' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => array(
					'left'    => array(
						'title' => esc_html__( 'Left', 'jet-elements' ),
						'icon'  => 'fa fa-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'jet-elements' ),
						'icon'  => 'fa fa-align-center',
					),
					'right' => array(
						'title' => esc_html__( 'Right', 'jet-elements' ),
						'icon'  => 'fa fa-align-right',
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['date'] => 'text-align: {{VALUE}};',
				),
				'classes' => 'jet-elements-text-align-control',
			),
			50
		);

		$this->_add_control(
			'date_custom_width',
			array(
				'label'        => esc_html__( 'Custom width', 'jet-elements' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-elements' ),
				'label_off'    => esc_html__( 'No', 'jet-elements' ),
				'return_value' => 'yes',
				'default'      => 'false',
			),
			100
		);

		$this->_add_responsive_control(
			'date_width',
			array(
				'label'      => esc_html__( 'Width', 'jet-elements' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px', '%', 'custom'
				),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 1000,
					),
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default' => array(
					'size' => 350,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['date'] => 'width: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'date_custom_width' => 'yes',
				),
			),
			100
		);

		$this->_add_control(
			'date_color',
			array(
				'label'  => esc_html__( 'Color', 'jet-elements' ),
				'type'   => Controls_Manager::COLOR,
				'global' => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['date'] => 'color: {{VALUE}}',
				),
			),
			25
		);

		$this->_add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'date_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['date'],
				'global' => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
			),
			50
		);

		$this->_add_responsive_control(
			'date_padding',
			array(
				'label'      => __( 'Padding', 'jet-elements' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['date'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			100
		);

		$this->_add_responsive_control(
			'date_margin',
			array(
				'label'      => __( 'Margin', 'jet-elements' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['date'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			50
		);

		$this->_end_controls_section();

		/**
		 * Rating Style Section
		 */
		$this->_start_controls_section(
			'section_rating_style',
			array(
				'label'      => esc_html__( 'Rating', 'jet-elements' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->_add_control(
			'star_color',
			array(
				'label'  => esc_html__( 'Stars', 'jet-elements' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['rating'] . ' i' => 'color: {{VALUE}}',
				),
			),
			25
		);

		$this->_add_control(
			'rating_active_star_color',
			array(
				'label'  => esc_html__( 'Active Stars', 'jet-elements' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['rating'] => 'color: {{VALUE}}',
				),
			),
			25
		);

		$this->_add_responsive_control(
			'rating_icon_size',
			array(
				'label' => esc_html__( 'Size', 'jet-elements' ),
				'type'  => Controls_Manager::SLIDER,
				'default' => array(
					'unit' => 'px',
					'size' => '20',
				),
				'render_type' => 'template',
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['rating'] => 'font-size: {{SIZE}}{{UNIT}}',
				),
			),
			50
		);

		$this->_add_responsive_control(
			'rating_icon_space',
			array(
				'label' => esc_html__( 'Spacing', 'jet-elements' ),
				'type'  => Controls_Manager::SLIDER,
				'default' => array(
					'unit' => 'px',
					'size' => '7',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					'body:not(.rtl) {{WRAPPER}} i:not(:last-of-type)' => 'margin-right: {{SIZE}}{{UNIT}}',
					'body.rtl {{WRAPPER}} i:not(:last-of-type)' => 'margin-left: {{SIZE}}{{UNIT}}',
				),
			),
			25
		);

		$this->_add_control(
			'rating_star_margin',
			array(
				'label'       => esc_html__( 'Margin', 'jet-elements' ),
				'type'        => Controls_Manager::DIMENSIONS,
				'size_units'  => array( 'px', 'custom' ),
				'render_type' => 'template',
				'selectors'   => array(
					'{{WRAPPER}} ' . $css_scheme['rating'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			50
		);

		$this->_end_controls_section();

		/*
		 * Arrows section
		 */
		$this->_start_controls_section(
			'section_arrows_style',
			array(
				'label'      => esc_html__( 'Arrows', 'jet-elements' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->_start_controls_tabs( 'tabs_arrows_style' );

		$this->_start_controls_tab(
			'tab_arrows_normal',
			array(
				'label' => esc_html__( 'Normal', 'jet-elements' ),
			)
		);

		$this->_add_group_control(
			\Jet_Group_Control_Box_Style::get_type(),
			array(
				'name'           => 'arrows_style',
				'label'          => esc_html__( 'Arrows Style', 'jet-elements' ),
				'selector'       => '{{WRAPPER}} ' . $css_scheme['arrow'],
				'fields_options' => array(
					'color' => array(
						'global' => array(
							'default' => Global_Colors::COLOR_PRIMARY,
						),
					),
				),
			),
			25
		);

		$this->_end_controls_tab();

		$this->_start_controls_tab(
			'tab_arrows_hover',
			array(
				'label' => esc_html__( 'Hover', 'jet-elements' ),
			)
		);

		$this->_add_group_control(
			\Jet_Group_Control_Box_Style::get_type(),
			array(
				'name'           => 'arrows_hover_style',
				'label'          => esc_html__( 'Arrows Style', 'jet-elements' ),
				'selector'       => '{{WRAPPER}} ' . $css_scheme['arrow'] . ':hover',
				'fields_options' => array(
					'color' => array(
						'global' => array(
							'default' => Global_Colors::COLOR_ACCENT,
						),
					),
				),
			),
			25
		);

		$this->_end_controls_tab();

		$this->_end_controls_tabs();

		$this->_add_control(
			'prev_arrow_position',
			array(
				'label'     => esc_html__( 'Prev Arrow Position', 'jet-elements' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			),
			75
		);

		$this->_add_control(
			'prev_vert_position',
			array(
				'label'   => esc_html__( 'Vertical Position by', 'jet-elements' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'top',
				'options' => array(
					'top'    => esc_html__( 'Top', 'jet-elements' ),
					'bottom' => esc_html__( 'Bottom', 'jet-elements' ),
				),
			),
			75
		);

		$this->_add_responsive_control(
			'prev_top_position',
			array(
				'label'      => esc_html__( 'Top Indent', 'jet-elements' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em', 'custom' ),
				'range'      => array(
					'px' => array(
						'min' => -400,
						'max' => 400,
					),
					'%' => array(
						'min' => -100,
						'max' => 100,
					),
					'em' => array(
						'min' => -50,
						'max' => 50,
					),
				),
				'condition' => array(
					'prev_vert_position' => 'top',
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['arrow'] . '.prev-arrow' => 'top: {{SIZE}}{{UNIT}}; bottom: auto;',
				),
			),
			75
		);

		$this->_add_responsive_control(
			'prev_bottom_position',
			array(
				'label'      => esc_html__( 'Bottom Indent', 'jet-elements' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em', 'custom' ),
				'range'      => array(
					'px' => array(
						'min' => -400,
						'max' => 400,
					),
					'%' => array(
						'min' => -100,
						'max' => 100,
					),
					'em' => array(
						'min' => -50,
						'max' => 50,
					),
				),
				'condition' => array(
					'prev_vert_position' => 'bottom',
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['arrow'] . '.prev-arrow' => 'bottom: {{SIZE}}{{UNIT}}; top: auto;',
				),
			),
			75
		);

		$this->_add_control(
			'prev_hor_position',
			array(
				'label'   => esc_html__( 'Horizontal Position by', 'jet-elements' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => array(
					'left'  => esc_html__( 'Left', 'jet-elements' ),
					'right' => esc_html__( 'Right', 'jet-elements' ),
				),
			),
			75
		);

		$this->_add_responsive_control(
			'prev_left_position',
			array(
				'label'      => esc_html__( 'Left Indent', 'jet-elements' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em', 'custom' ),
				'range'      => array(
					'px' => array(
						'min' => -400,
						'max' => 400,
					),
					'%' => array(
						'min' => -100,
						'max' => 100,
					),
					'em' => array(
						'min' => -50,
						'max' => 50,
					),
				),
				'condition' => array(
					'prev_hor_position' => 'left',
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['arrow'] . '.prev-arrow' => 'left: {{SIZE}}{{UNIT}}; right: auto;',
				),
			),
			75
		);

		$this->_add_responsive_control(
			'prev_right_position',
			array(
				'label'      => esc_html__( 'Right Indent', 'jet-elements' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em', 'custom' ),
				'range'      => array(
					'px' => array(
						'min' => -400,
						'max' => 400,
					),
					'%' => array(
						'min' => -100,
						'max' => 100,
					),
					'em' => array(
						'min' => -50,
						'max' => 50,
					),
				),
				'condition' => array(
					'prev_hor_position' => 'right',
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['arrow'] . '.prev-arrow' => 'right: {{SIZE}}{{UNIT}}; left: auto;',
				),
			),
			75
		);

		$this->_add_control(
			'next_arrow_position',
			array(
				'label'     => esc_html__( 'Next Arrow Position', 'jet-elements' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			),
			75
		);

		$this->_add_control(
			'next_vert_position',
			array(
				'label'   => esc_html__( 'Vertical Position by', 'jet-elements' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'top',
				'options' => array(
					'top'    => esc_html__( 'Top', 'jet-elements' ),
					'bottom' => esc_html__( 'Bottom', 'jet-elements' ),
				),
			),
			75
		);

		$this->_add_responsive_control(
			'next_top_position',
			array(
				'label'      => esc_html__( 'Top Indent', 'jet-elements' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em', 'custom' ),
				'range'      => array(
					'px' => array(
						'min' => -400,
						'max' => 400,
					),
					'%' => array(
						'min' => -100,
						'max' => 100,
					),
					'em' => array(
						'min' => -50,
						'max' => 50,
					),
				),
				'condition' => array(
					'next_vert_position' => 'top',
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['arrow'] . '.next-arrow' => 'top: {{SIZE}}{{UNIT}}; bottom: auto;',
				),
			),
			75
		);

		$this->_add_responsive_control(
			'next_bottom_position',
			array(
				'label'      => esc_html__( 'Bottom Indent', 'jet-elements' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em', 'custom' ),
				'range'      => array(
					'px' => array(
						'min' => -400,
						'max' => 400,
					),
					'%' => array(
						'min' => -100,
						'max' => 100,
					),
					'em' => array(
						'min' => -50,
						'max' => 50,
					),
				),
				'condition' => array(
					'next_vert_position' => 'bottom',
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['arrow'] . '.next-arrow' => 'bottom: {{SIZE}}{{UNIT}}; top: auto;',
				),
			),
			75
		);

		$this->_add_control(
			'next_hor_position',
			array(
				'label'   => esc_html__( 'Horizontal Position by', 'jet-elements' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'right',
				'options' => array(
					'left'  => esc_html__( 'Left', 'jet-elements' ),
					'right' => esc_html__( 'Right', 'jet-elements' ),
				),
			),
			75
		);

		$this->_add_responsive_control(
			'next_left_position',
			array(
				'label'      => esc_html__( 'Left Indent', 'jet-elements' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em', 'custom' ),
				'range'      => array(
					'px' => array(
						'min' => -400,
						'max' => 400,
					),
					'%' => array(
						'min' => -100,
						'max' => 100,
					),
					'em' => array(
						'min' => -50,
						'max' => 50,
					),
				),
				'condition' => array(
					'next_hor_position' => 'left',
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['arrow'] . '.next-arrow' => 'left: {{SIZE}}{{UNIT}}; right: auto;',
				),
			),
			75
		);

		$this->_add_responsive_control(
			'next_right_position',
			array(
				'label'      => esc_html__( 'Right Indent', 'jet-elements' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em', 'custom' ),
				'range'      => array(
					'px' => array(
						'min' => -400,
						'max' => 400,
					),
					'%' => array(
						'min' => -100,
						'max' => 100,
					),
					'em' => array(
						'min' => -50,
						'max' => 50,
					),
				),
				'condition' => array(
					'next_hor_position' => 'right',
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['arrow'] . '.next-arrow' => 'right: {{SIZE}}{{UNIT}}; left: auto;',
				),
			),
			75
		);

		$this->_end_controls_section();

		/*
		 * Dots section
		 */
		$this->_start_controls_section(
			'section_dots_style',
			array(
				'label'      => esc_html__( 'Dots', 'jet-elements' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->_add_responsive_control(
			'dots_alignment',
			array(
				'label'   => esc_html__( 'Alignment', 'jet-elements' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => array(
					'flex-start' => array(
						'title' => esc_html__( 'Start', 'jet-elements' ),
						'icon'  => ! is_rtl() ? 'eicon-h-align-left' : 'eicon-h-align-right',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'jet-elements' ),
						'icon'  => 'eicon-h-align-center',
					),
					'flex-end' => array(
						'title' => esc_html__( 'End', 'jet-elements' ),
						'icon'  => ! is_rtl() ? 'eicon-h-align-right' : 'eicon-h-align-left',
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['dots'] => 'justify-content: {{VALUE}};',
				),
			),
			25
		);

		$this->_add_control(
			'dots_gap',
			array(
				'label' => esc_html__( 'Gap', 'jet-elements' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 5,
					'unit' => 'px',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['dots'] . ' li' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}}',
				),
			),
			25
		);

		$this->_add_control(
			'dots_margin',
			array(
				'label'      => esc_html__( 'Dots Box Margin', 'jet-elements' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['dots'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			50
		);

		$this->_start_controls_tabs( 'tabs_dots_style' );

		$this->_start_controls_tab(
			'tab_dots_normal',
			array(
				'label' => esc_html__( 'Normal', 'jet-elements' ),
			)
		);

		$this->_add_group_control(
			\Jet_Group_Control_Box_Style::get_type(),
			array(
				'name'           => 'dots_style',
				'label'          => esc_html__( 'Dots Style', 'jet-elements' ),
				'selector'       => '{{WRAPPER}} ' . $css_scheme['dots'] .' li span',
				'fields_options' => array(
					'color' => array(
						'global' => array(
							'default' => Global_Colors::COLOR_TEXT,
						),
					),
				),
				'exclude' => array(
					'box_font_color',
					'box_font_size',
				),
			),
			25
		);

		$this->_end_controls_tab();

		$this->_start_controls_tab(
			'tab_dots_hover',
			array(
				'label' => esc_html__( 'Hover', 'jet-elements' ),
			)
		);

		$this->_add_group_control(
			\Jet_Group_Control_Box_Style::get_type(),
			array(
				'name'           => 'dots_style_hover',
				'label'          => esc_html__( 'Dots Style', 'jet-elements' ),
				'selector'       => '{{WRAPPER}} ' . $css_scheme['dots'] . ' li span:hover',
				'fields_options' => array(
					'color' => array(
						'global' => array(
							'default' => Global_Colors::COLOR_PRIMARY,
						),
					),
				),
				'exclude' => array(
					'box_font_color',
					'box_font_size',
				),
			),
			25
		);

		$this->_end_controls_tab();

		$this->_start_controls_tab(
			'tab_dots_active',
			array(
				'label' => esc_html__( 'Active', 'jet-elements' ),
			)
		);

		$this->_add_group_control(
			\Jet_Group_Control_Box_Style::get_type(),
			array(
				'name'           => 'dots_style_active',
				'label'          => esc_html__( 'Dots Style', 'jet-elements' ),
				'selector'       => '{{WRAPPER}} ' . $css_scheme['dots'] .' li.slick-active span',
				'fields_options' => array(
					'color' => array(
						'global' => array(
							'default' => Global_Colors::COLOR_ACCENT,
						),
					),
				),
				'exclude' => array(
					'box_font_color',
					'box_font_size',
				),
			),
			25
		);

		$this->_end_controls_tab();

		$this->_end_controls_tabs();

		$this->_end_controls_section();
	}

	protected function render() {

		$this->_context = 'render';

		$this->_open_wrap();
		include $this->_get_global_template( 'index' );
		$this->_close_wrap();
	}

	protected function render_stars() {

		$settings   = $this->get_settings();
		$stars_html = '';

		for ( $stars = 1; $stars <= 5; $stars++ ) {

			$star_class = ( "outline" === $settings['rating_star_style'] ) ? 'far fa-star' : 'fas fa-star';
			$active_star_class = ( "outline" === $settings['rating_active_star_style'] ) ? 'far fa-star' : 'fas fa-star';
			$stars_html .= '<i aria-hidden="true" data-star="' . $star_class .'" data-active-star="' . $active_star_class .'"></i>';
		}
		return $stars_html;
	}

	/**
	 * Generate setting json
	 *
	 * @return string
	 */
	public function generate_setting_json() {
		$settings = $this->get_settings();
		$widget_id = $this->get_id();


		$instance_settings = array(
			'autoplaySpeed'  => absint( $settings['autoplay_speed'] ),
			'autoplay'       => filter_var( $settings['autoplay'], FILTER_VALIDATE_BOOLEAN ),
			'infinite'       => filter_var( $settings['infinite'], FILTER_VALIDATE_BOOLEAN ),
			'centerMode'     => filter_var( $settings['centered'], FILTER_VALIDATE_BOOLEAN ),
			'centerPadding'  => ! empty( $settings['centered_padding'] ) ? $settings['centered_padding'] . 'px' : '50px',
			'adaptiveHeight' => filter_var( $settings['adaptive_height'], FILTER_VALIDATE_BOOLEAN ),
			'pauseOnHover'   => filter_var( $settings['pause_on_hover'], FILTER_VALIDATE_BOOLEAN ),
			'speed'          => absint( $settings['speed'] ),
			'arrows'         => filter_var( $settings['arrows'], FILTER_VALIDATE_BOOLEAN ),
			'dots'           => filter_var( $settings['dots'], FILTER_VALIDATE_BOOLEAN ),
			'slidesToScroll' => 1 < absint( $settings['slides_to_show'] ) ? absint( $settings['slides_to_scroll'] ) : 1,
			'prevArrow'      => '.jet-testimonial__prev-arrow-' . $widget_id,
			'nextArrow'      => '.jet-testimonial__next-arrow-' . $widget_id,
			'rtl'            => is_rtl(),
		);

		if ( 'fade' === $settings['effect'] && 1 >= absint( $settings['slides_to_show'] ) ) {
			$instance_settings['fade'] = true;
		}

		$instance_settings = apply_filters( 'jet-elements/jet-testimonials/carousel-options', $instance_settings, $settings, $widget_id );

		$instance_settings = json_encode( $instance_settings );

		// Escape the JSON string for safe use in HTML attributes
		$data_settings_attribute = esc_attr( $instance_settings );

		return sprintf( 'data-settings=\'%1$s\'', $data_settings_attribute );
	}

	public function _get_testimonials_image() {
		$settings        = $this->get_settings_for_display();
		$image_item      = $this->_processed_item['item_image'];
		$image_lazyload  = isset( $settings['image_lazyload'] ) ? $settings['image_lazyload'] : 'true';

		if ( empty( $image_item['url'] ) ) {
			return;
		}

		$lazyload = 'true' === $image_lazyload ? 'lazy' : '';

		$item_link = isset( $this->_processed_item['item_link'] ) ? $this->_processed_item['item_link'] : false;

		if ( $item_link && ! empty( $item_link['url'] ) ) {

			$_id = $this->_processed_item['_id'];

			if ( method_exists( $this, 'add_link_attributes' ) ) {
				$this->add_link_attributes( 'img-link-' . $_id, $item_link );
			} else {
				$this->add_render_attribute( 'img-link-' . $_id, 'href', esc_url( $item_link['url'] ) );

				if ( ! empty( $item_link['is_external'] ) ) {
					$this->add_render_attribute( 'img-link-' . $_id, 'target', '_blank' );
				}

				if ( ! empty( $item_link['nofollow'] ) ) {
					$this->add_render_attribute( 'img-link-' . $_id, 'rel', 'nofollow' );
				}
			}

			return sprintf( '<figure class="jet-testimonials__figure"><a %3$s><img class="jet-testimonials__tag-img" src="%1$s" alt="%2$s" loading="%4$s"></a></figure>',
				$image_item['url'],
				esc_attr( Control_Media::get_image_alt( $image_item ) ),
				$this->get_render_attribute_string( 'img-link-' . $_id ),
				$lazyload
			);
		}

		return sprintf( '<figure class="jet-testimonials__figure"><img class="jet-testimonials__tag-img" src="%1$s" alt="%2$s" loading="%3$s"></figure>',
			$image_item['url'],
			esc_attr( Control_Media::get_image_alt( $image_item ) ),
			$lazyload
		);
	}

	public function _get_testimonials_name() {
		$item_name = $this->_processed_item['item_name'];

		if ( empty( $item_name ) ) {
			return;
		}

		$item_link = isset( $this->_processed_item['item_link'] ) ? $this->_processed_item['item_link'] : false;

		if ( $item_link && ! empty( $item_link['url'] ) ) {

			$_id = $this->_processed_item['_id'];

			if ( method_exists( $this, 'add_link_attributes' ) ) {
				$this->add_link_attributes( 'name-link-' . $_id, $item_link );
			} else {
				$this->add_render_attribute( 'name-link-' . $_id, 'href', esc_url( $item_link['url'] ) );

				if ( ! empty( $item_link['is_external'] ) ) {
					$this->add_render_attribute( 'name-link-' . $_id, 'target', '_blank' );
				}

				if ( ! empty( $item_link['nofollow'] ) ) {
					$this->add_render_attribute( 'name-link-' . $_id, 'rel', 'nofollow' );
				}
			}

			return sprintf( '<div class="jet-testimonials__name"><a %2$s><span>%1$s</span></a></div>',
				$item_name,
				$this->get_render_attribute_string( 'name-link-' . $_id )
			);
		}

		return sprintf( '<div class="jet-testimonials__name"><span>%s</span></div>', $item_name );
	}

	public function _shuffle_items( $loop ) {
		shuffle($loop);
		return $loop;
	}
}
