<?php
/**
 * Created by IntelliJ IDEA.
 * User: you-f
 * Date: 24/10/2020
 * Time: 11:55
 */

// TODO: Load this file in this plugin
if( function_exists('acf_add_local_field_group') ):

    acf_add_local_field_group(array(
        'key' => 'group_5f93e8d8e0629',
        'title' => 'Transaction',
        'fields' => array(
            array(
                'key' => 'field_5f93ef0fbc92a',
                'label' => 'ID transaction',
                'name' => 'id_transaction',
                'type' => 'text',
                'instructions' => '',
                'required' => 1,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
            ),
            array(
                'key' => 'field_5f93e8e88ab46',
                'label' => 'Customer',
                'name' => 'customer',
                'type' => 'user',
                'instructions' => '',
                'required' => 1,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'role' => '',
                'allow_null' => 0,
                'multiple' => 0,
                'return_format' => 'array',
            ),
            array(
                'key' => 'field_5f93ebf834b71',
                'label' => 'Order',
                'name' => 'order',
                'type' => 'post_object',
                'instructions' => '',
                'required' => 1,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'post_type' => array(
                    0 => 'shop_order',
                ),
                'taxonomy' => '',
                'allow_null' => 0,
                'multiple' => 0,
                'return_format' => 'object',
                'ui' => 1,
            ),
            array(
                'key' => 'field_5f93ef26bc92b',
                'label' => 'Date send',
                'name' => 'date_send',
                'type' => 'date_time_picker',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'display_format' => 'm/d/Y g:i a',
                'return_format' => 'd/m/Y g:i a',
                'first_day' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'mm_visa_transaction',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
    ));

endif;