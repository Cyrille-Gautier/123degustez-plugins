<?php
/**
 * Class: Jet_Elements_Price_List
 * Name: Price List
 * Slug: jet-price-list
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

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Jet_Elements_Price_List extends Jet_Elements_Base {
	public $_processed_item_index = 0;

	public $_hidden_price_items = array();

	public function get_name() {
		return 'jet-price-list';
	}

	public function get_title() {
		return esc_html__( 'Price List', 'jet-elements' );
	}

	public function get_icon() {
		return 'jet-elements-icon-price-list';
	}

	public function get_jet_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/how-to-add-a-price-list-to-the-page-built-with-elementor-using-jetelements-price-list-widget/';
	}

	public function get_categories() {
		return array( 'cherry' );
	}

	public function get_style_depends() { 
		return array( 'jet-price-list', 'jet-price-list-skin' ); 
	}

	protected function register_controls() {
		$css_scheme = apply_filters(
			'jet-elements/price-list/css-scheme',
			array(
				'price_list'       => '.jet-price-list',
				'item'             => '.jet-price-list .price-list__item',
				'item_inner'       => '.jet-price-list .price-list__item-inner',
				'item_title'       => '.jet-price-list .price-list__item-title',
				'item_price'       => '.jet-price-list .price-list__item-price',
				'item_description' => '.jet-price-list .price-list__item-desc',
				'item_separator'   => '.jet-price-list .price-list__item-separator',
				'item_image_wrap'  => '.jet-price-list .price-list__item-img-wrap',
			)
		);

		$this->start_controls_section(
			'section_general',
			array(
				'label'      => esc_html__( 'General', 'jet-elements' ),
				'tab'        => Controls_Manager::TAB_CONTENT,
				'show_label' => false,
			)
		);

		do_action( 'jet-engine-query-gateway/control', $this, 'price_list' );

		$repeater = new Repeater();

		$repeater->add_control(
			'item_title',
			array(
				'label'   => esc_html__( 'Title', 'jet-elements' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'item_price',
			array(
				'label'   => esc_html__( 'Price', 'jet-elements' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'item_text',
			array(
				'label'   => esc_html__( 'Description', 'jet-elements' ),
				'type'    => Controls_Manager::TEXTAREA,
				'dynamic' => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'item_image',
			array(
				'label'   => esc_html__( 'Image', 'jet-elements' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array(
					'url' => '',
				),
				'dynamic' => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'item_url',
			array(
				'label'   => esc_html__( 'URL', 'jet-elements' ),
				'type'    => Controls_Manager::URL,
				'default' => array(
					'url' => '',
				),
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'price_list',
			array(
				'label'       => esc_html__( 'List items', 'jet-elements' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array(
						'item_title' => esc_html__( 'Item #1', 'jet-elements' ),
						'item_price' => esc_html__( '$12', 'jet-elements' ),
						'item_text'  => esc_html__( 'Lorem ipsum dolor sit amet, mea ei viderer probatus consequuntur, sonet vocibus lobortis has ad. Eos erant indoctum an, dictas invidunt est ex, et sea consulatu torquatos. Nostro aperiam petentium eu nam, mel debet urbanitas ad, idque complectitur eu quo. An sea autem dolore dolores.', 'jet-elements' ),
					),
					array(
						'item_title' => esc_html__( 'Item #1', 'jet-elements' ),
						'item_price' => esc_html__( '$12', 'jet-elements' ),
						'item_text'  => esc_html__( 'Lorem ipsum dolor sit amet, mea ei viderer probatus consequuntur, sonet vocibus lobortis has ad. Eos erant indoctum an, dictas invidunt est ex, et sea consulatu torquatos. Nostro aperiam petentium eu nam, mel debet urbanitas ad, idque complectitur eu quo. An sea autem dolore dolores.', 'jet-elements' ),
					),
					array(
						'item_title' => esc_html__( 'Item #1', 'jet-elements' ),
						'item_price' => esc_html__( '$12', 'jet-elements' ),
						'item_text'  => esc_html__( 'Lorem ipsum dolor sit amet, mea ei viderer probatus consequuntur, sonet vocibus lobortis has ad. Eos erant indoctum an, dictas invidunt est ex, et sea consulatu torquatos. Nostro aperiam petentium eu nam, mel debet urbanitas ad, idque complectitur eu quo. An sea autem dolore dolores.', 'jet-elements' ),
					),
				),
				'title_field' => '{{{ item_title }}}',
			)
		);

		$this->add_control(
			'title_html_tag',
			array(
				'label'   => esc_html__( 'Title HTML Tag', 'jet-elements' ),
				'type'    => Controls_Manager::SELECT,
				'options' => jet_elements_tools()->get_available_title_html_tags(),
				'default' => 'h5',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'hide_items_with_empty_price',
			array(
				'label'     => esc_html__( 'Hide items with a missing price', 'jet-elements' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => '',
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

		$this->_add_responsive_control(
			'item_space_between',
			array(
				'label'      => esc_html__( 'Space Between Items (px)', 'jet-elements' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px', 'custom'
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 150,
					),
				),
				'default'    => array(
					'size' => 15,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['item'] . '+ .price-list__item' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			),
			25
		);

		$this->_add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'item_border',
				'label'       => esc_html__( 'Border', 'jet-elements' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['item'],
			),
			75
		);

		$this->_add_control(
			'item_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-elements' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['item'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow:hidden;',
				),
			),
			75
		);

		$this->_add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'item_box_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['item'],
			),
			100
		);

		$this->_add_responsive_control(
			'item_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-elements' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['item'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			50
		);

		$this->_add_responsive_control(
			'item_content_alignment',
			array(
				'label'   => esc_html__( 'Content Vertical Alignment', 'jet-elements' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'flex-start' => array(
						'title' => esc_html__( 'Top', 'jet-elements' ),
						'icon'  => 'eicon-v-align-top',
					),
					'center' => array(
						'title' => esc_html__( 'Middle', 'jet-elements' ),
						'icon'  => 'eicon-v-align-middle',
					),
					'flex-end' => array(
						'title' => esc_html__( 'Bottom', 'jet-elements' ),
						'icon'  => 'eicon-v-align-bottom',
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['item_inner'] => 'align-items: {{VALUE}};',
				),
			),
			50
		);

		$this->_add_control(
			'first_item_heading',
			array(
				'label'     => esc_html__( 'First Item', 'jet-elements' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			),
			75
		);

		$this->_add_responsive_control(
			'first_item_border_width',
			array(
				'label'      => esc_html__( 'Border Width', 'jet-elements' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['item'] . ':first-child' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			75
		);

		$this->_add_control(
			'first_item_border_color',
			array(
				'label' => esc_html__( 'Border Color', 'jet-elements' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['item'] . ':first-child' => 'border-color: {{VALUE}}',
				),
			),
			75
		);

		$this->_add_control(
			'last_item_heading',
			array(
				'label'     => esc_html__( 'Last Item', 'jet-elements' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			),
			75
		);

		$this->_add_responsive_control(
			'last_item_border_width',
			array(
				'label'      => esc_html__( 'Border Width', 'jet-elements' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['item'] . ':last-child' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			75
		);

		$this->_add_control(
			'last_item_border_color',
			array(
				'label' => esc_html__( 'Border Color', 'jet-elements' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['item'] . ':last-child' => 'border-color: {{VALUE}}',
				),
			),
			75
		);

		$this->_end_controls_section();

		$this->_start_controls_section(
			'section_title_style',
			array(
				'label'      => esc_html__( 'Title', 'jet-elements' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->_add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['item_title'],
				'global' => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
			),
			50
		);

		$this->_add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-elements' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['item_title'] => 'color: {{VALUE}}',
				),
			),
			25
		);

		$this->_add_responsive_control(
			'title_vertical_alignment',
			array(
				'label'     => esc_html__( 'Alignment', 'jet-elements' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'left',
				'options'   => array(
					'flex-start' => array(
						'title' => esc_html__( 'Top', 'jet-elements' ),
						'icon'  => 'eicon-v-align-top',
					),
					'center'     => array(
						'title' => esc_html__( 'Middle', 'jet-elements' ),
						'icon'  => 'eicon-v-align-middle',
					),
					'flex-end'   => array(
						'title' => esc_html__( 'Bottom', 'jet-elements' ),
						'icon'  => 'eicon-v-align-bottom',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['item_title'] => 'align-self: {{VALUE}};',
				),
			),
			50
		);

		$this->_end_controls_section();

		$this->_start_controls_section(
			'section_price_style',
			array(
				'label'      => esc_html__( 'Price', 'jet-elements' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->_add_responsive_control(
			'price_min_width',
			array(
				'label'      => esc_html__( 'Price Minimal Width (px)', 'jet-elements' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px', 'custom'
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 400,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['item_price'] => 'min-width: {{SIZE}}{{UNIT}};',
				),
			),
			50
		);

		$this->_add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'price_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['item_price'],
				'global' => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
			),
			50
		);

		$this->_add_control(
			'price_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-elements' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['item_price'] => 'color: {{VALUE}}',
				),
			),
			25
		);

		$this->_add_control(
			'price_background',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-elements' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['item_price'] => 'background-color: {{VALUE}}',
				),
			),
			25
		);

		$this->_add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'price_border',
				'label'       => esc_html__( 'Border', 'jet-elements' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['item_price'],
			),
			75
		);

		$this->_add_control(
			'price_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-elements' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['item_price'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			75
		);

		$this->_add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'price_box_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['item_price'],
			),
			100
		);

		$this->_add_responsive_control(
			'price_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-elements' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['item_price'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			25
		);

		$this->_add_responsive_control(
			'price_vertical_alignment',
			array(
				'label'     => esc_html__( 'Vertical Alignment', 'jet-elements' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'left',
				'options'   => array(
					'flex-start' => array(
						'title' => esc_html__( 'Top', 'jet-elements' ),
						'icon'  => 'eicon-v-align-top',
					),
					'center'     => array(
						'title' => esc_html__( 'Middle', 'jet-elements' ),
						'icon'  => 'eicon-v-align-middle',
					),
					'flex-end'   => array(
						'title' => esc_html__( 'Bottom', 'jet-elements' ),
						'icon'  => 'eicon-v-align-bottom',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['item_price'] => 'align-self: {{VALUE}};',
				),
			),
			50
		);

		$this->_add_responsive_control(
			'price_alignment',
			array(
				'label'     => esc_html__( 'Text Alignment', 'jet-elements' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'center',
				'options'   => array(
					'left'   => array(
						'title' => esc_html__( 'Left', 'jet-elements' ),
						'icon'  => 'fa fa-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'jet-elements' ),
						'icon'  => 'fa fa-align-center',
					),
					'right'  => array(
						'title' => esc_html__( 'Right', 'jet-elements' ),
						'icon'  => 'fa fa-align-right',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['item_price'] => 'text-align: {{VALUE}};',
				),
				'classes' => 'jet-elements-text-align-control',
			),
			50
		);

		$this->_end_controls_section();

		$this->_start_controls_section(
			'section_description_style',
			array(
				'label'      => esc_html__( 'Description', 'jet-elements' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->_add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['item_description'],
				'global' => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
			),
			50
		);

		$this->_add_control(
			'description_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-elements' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['item_description'] => 'color: {{VALUE}}',
				),
			),
			25
		);

		$this->_add_responsive_control(
			'description_margin',
			array(
				'label'      => esc_html__( 'Margin', 'jet-elements' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['item_description'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			25
		);

		$this->_add_responsive_control(
			'description_alignment',
			array(
				'label'     => esc_html__( 'Alignment', 'jet-elements' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'left',
				'options'   => array(
					'left'    => array(
						'title' => esc_html__( 'Left', 'jet-elements' ),
						'icon'  => 'fa fa-align-left',
					),
					'center'  => array(
						'title' => esc_html__( 'Center', 'jet-elements' ),
						'icon'  => 'fa fa-align-center',
					),
					'right'   => array(
						'title' => esc_html__( 'Right', 'jet-elements' ),
						'icon'  => 'fa fa-align-right',
					),
					'justify' => array(
						'title' => esc_html__( 'Justified', 'jet-elements' ),
						'icon'  => 'fa fa-align-justify',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['item_description'] => 'text-align: {{VALUE}};',
				),
				'classes' => 'jet-elements-text-align-control',
			),
			50
		);

		$this->_end_controls_section();

		$this->_start_controls_section(
			'section_separator_style',
			array(
				'label'      => esc_html__( 'Separator', 'jet-elements' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->_add_control(
			'separator_border_type',
			array(
				'label'     => esc_html__( 'Separator Type', 'jet-elements' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'dotted',
				'options'   => array(
					'none'   => esc_html__( 'None', 'jet-elements' ),
					'solid'  => esc_html__( 'Solid', 'jet-elements' ),
					'double' => esc_html__( 'Double', 'jet-elements' ),
					'dotted' => esc_html__( 'Dotted', 'jet-elements' ),
					'dashed' => esc_html__( 'Dashed', 'jet-elements' ),
					'groove' => esc_html__( 'Groove', 'jet-elements' ),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['item_separator'] => 'border-style: {{VALUE}};',
				),
			),
			25
		);

		$this->_add_responsive_control(
			'separator_border_width',
			array(
				'label'      => esc_html__( 'Separator Width', 'jet-elements' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px', 'custom'
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 15,
					),
				),
				'default'    => array(
					'size' => 1,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['item_separator'] => 'border-bottom-width: {{SIZE}}{{UNIT}}; border-top-width:0; border-right-width:0; border-left-width:0;',
				),
			),
			25
		);

		$this->_add_control(
			'separator_border_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-elements' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['item_separator'] => 'border-color: {{VALUE}}',
				),
			),
			25
		);

		$this->_add_responsive_control(
			'separator_vertical_alignment',
			array(
				'label'     => esc_html__( 'Vertical Alignment', 'jet-elements' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'left',
				'options'   => array(
					'flex-start' => array(
						'title' => esc_html__( 'Top', 'jet-elements' ),
						'icon'  => 'eicon-v-align-top',
					),
					'center'     => array(
						'title' => esc_html__( 'Middle', 'jet-elements' ),
						'icon'  => 'eicon-v-align-middle',
					),
					'flex-end'   => array(
						'title' => esc_html__( 'Bottom', 'jet-elements' ),
						'icon'  => 'eicon-v-align-bottom',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['item_separator'] => 'align-self: {{VALUE}};',
				),
			),
			50
		);

		$this->_add_responsive_control(
			'separator_margin',
			array(
				'label'      => esc_html__( 'Margin', 'jet-elements' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['item_separator'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			25
		);

		$this->_end_controls_section();

		$this->_start_controls_section(
			'section_image_style',
			array(
				'label'      => esc_html__( 'Image', 'jet-elements' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->_add_responsive_control(
			'image_offset',
			array(
				'label'      => esc_html__( 'Image Offset (px)', 'jet-elements' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px', 'custom'
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default'    => array(
					'size' => 20,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['item_image_wrap'] => ! is_rtl() ? 'margin-right: {{SIZE}}{{UNIT}};' : 'margin-left: {{SIZE}}{{UNIT}};',
				),
			),
			25
		);

		$this->_add_responsive_control(
			'image_width',
			array(
				'label'      => esc_html__( 'Image Width', 'jet-elements' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'%',
					'custom',
				),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 1000,
					),
					'%'  => array(
						'min' => 0,
						'max' => 80,
					),
				),
				'default'    => array(
					'size' => 150,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['item_image_wrap'] => 'max-width: {{SIZE}}{{UNIT}};',
				),
			),
			25
		);

		$this->_add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'image_border',
				'label'       => esc_html__( 'Border', 'jet-elements' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['item_image_wrap'],
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
					'{{WRAPPER}} ' . $css_scheme['item_image_wrap'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow:hidden;',
				),
			),
			75
		);

		$this->_add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'image_box_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['item_image_wrap'],
			),
			100
		);

		$this->_end_controls_section();

	}

	public function _open_price_item_link( $url_key ) {
		call_user_func( array( $this, sprintf( '_open_price_item_link_%s', $this->_context ) ), $url_key );
	}

	public function _open_price_item_link_format() {
		return '<a href="%1$s" class="price-list__item-link"%2$s%3$s>';
	}

	public function _open_price_item_link_render( $url_key ) {

		$item = $this->_processed_item;

		if ( empty( $item[ $url_key ]['url'] ) ) {
			return;
		}

		$id = $this->_processed_item['_id'];

		if ( method_exists( $this, 'add_link_attributes' ) ) {
			$this->add_link_attributes( 'link-' . $id, $item[ $url_key ] );
		} else {
			$this->add_render_attribute( 'link-' . $id, 'href', $item[ $url_key ]['url'] );

			if ( ! empty( $item[ $url_key ]['is_external'] ) ) {
				$this->add_render_attribute( 'link-' . $id, 'target', '_blank' );
			}

			if ( ! empty( $item[ $url_key ]['nofollow'] ) ) {
				$this->add_render_attribute( 'link-' . $id, 'rel', 'nofollow' );
			}
		}

		printf(
			'<a %s>',
			$this->get_render_attribute_string( 'link-' . $id )
		);

	}

	public function _open_price_item_link_edit( $url_key ) {

		echo '<# if ( item.' . $url_key . '.url ) { #>';
		printf(
			'<a href="%1$s" class="price-list__item-link"%2$s%3$s>',
			'{{{ item.' . $url_key . '.url }}}',
			'<# if ( item.' . $url_key . '.is_external ) { #> target="_blank"<# } #>',
			'<# if ( item.' . $url_key . '.nofollow ) { #> rel="nofollow"<# } #>'
		);
		echo '<# } #>';

	}

	public function _close_price_item_link( $url_key ) {
		call_user_func( array( $this, sprintf( '_close_price_item_link_%s', $this->_context ) ), $url_key );
	}

	public function _close_price_item_link_render( $url_key ) {

		$item = $this->_processed_item;

		if ( empty( $item[ $url_key ]['url'] ) ) {
			return;
		}

		echo '</a>';

	}

	public function _close_price_item_link_edit( $url_key ) {

		echo '<# if ( item.' . $url_key . '.url ) { #>';
		echo '</a>';
		echo '<# } #>';

	}

	public function get_item_inline_editing_attributes( $settings_item_key, $repeater_item_key, $index, $classes ) {
		$item_key = $this->get_repeater_setting_key( $settings_item_key, $repeater_item_key, $index );
		$this->add_render_attribute( $item_key, [ 'class' => $classes ] );
		$this->add_inline_editing_attributes( $item_key, 'basic' );

		return $this->get_render_attribute_string( $item_key );
	}
	
	public function _get_price_list_image() {
		$image_item = $this->_processed_item['item_image'];
		
		if ( empty( $image_item['url'] ) ) {
			return;
		}
		
		return sprintf( '<div class="price-list__item-img-wrap"><img src="%1$s" alt="%2$s" class="price-list__item-img" loading="lazy"></div>',
			$image_item['url'],
			esc_attr( Control_Media::get_image_alt( $image_item ) )
		);
	}

	public function check_render_price_items() {
		$hide_items_with_empty_price = $this->get_settings( 'hide_items_with_empty_price' );

		if ( ! Plugin::$instance->editor->is_edit_mode() && filter_var( $hide_items_with_empty_price, FILTER_VALIDATE_BOOLEAN ) ) {
			$check      = false;
			$price_list = $this->get_settings_for_display( 'price_list' );

			foreach ( $price_list as $inx => $item ) {
				if ( ! empty( $item['item_price'] ) ) {
					$check = true;
				} else {
					$this->_hidden_price_items[] = $inx;
				}
			}

			return $check;
		}

		return true;
	}

	protected function render() {
		$this->_context = 'render';

		if ( ! $this->check_render_price_items() ) {
			return;
		}

		$this->_open_wrap();
		include $this->_get_global_template( 'index' );
		$this->_close_wrap();

		$this->_processed_item_index = 0;
	}

}