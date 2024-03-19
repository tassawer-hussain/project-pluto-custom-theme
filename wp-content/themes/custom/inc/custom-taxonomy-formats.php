<?php

/**
 * Formats Taxonomy Registration
 */
function create_post_formats_taxonomy() {
    // Add new taxonomy, make it hierarchical like categories
    $labels = array(
        'name' => _x('Formats', 'pluto'),
        'singular_name' => _x('Formats', 'pluto'),
        'search_items' => __('Search Posts'),
        'all_items' => __('All Posts Formats'),
        'parent_item' => __('Parent Format'),
        'parent_item_colon' => __('Parent Format:'),
        'edit_item' => __('Edit Format'),
        'update_item' => __('Update Format'),
        'add_new_item' => __('Add New Format'),
        'new_item_name' => __('New Format Name'),
        'menu_name' => __('Formats'),
    );
    // Now register the taxonomy
    register_taxonomy('formats', array('post'), array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_in_menu' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array(
            'slug' => 'formats'
        ),
        'show_in_rest' => true,
        'capabilities' => array(
            'edit_terms' => true,
        ),
    ));
}

add_action('init', 'create_post_formats_taxonomy');

/**
 * Use radio inputs instead of checkboxes for term checklists in specified taxonomies.
 */
function post_format_radio_checklist($args) {
    if (!empty($args['taxonomy']) && $args['taxonomy'] === 'formats') {
        if (empty($args['walker']) || is_a($args['walker'], 'Walker')) {
            if (!class_exists('Walker_Category_Radio_Checklist')) {

                /**
                 * Custom walker for switching checkbox inputs to radio.
                 *
                 * @see Walker_Category_Checklist
                 */
                class Walker_Category_Radio_Checklist extends Walker_Category_Checklist {

                    function walk($elements, $max_depth, $args = array()) {
                        $output = parent::walk($elements, $max_depth, $args);
                        $output = str_replace(
                                array('type="checkbox"', "type='checkbox'"), array('type="radio"', "type='radio'"), $output
                        );
                        return $output;
                    }

                }

            }
            $args['walker'] = new Walker_Category_Radio_Checklist;
        }
    }
    return $args;
}

add_filter('wp_terms_checklist_args', 'post_format_radio_checklist');
