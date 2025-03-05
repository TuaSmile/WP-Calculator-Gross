<?php
namespace NetGrossCalc;

class CPT
{
    public static function init()
    {
        add_action('init', [__CLASS__, 'registerCalculations']);
    }

    /**
     * Registers a custom post type for calculations.
     * 
     * This function sets up the necessary arguments for registering a custom post type named "calculations".
     * The post type is used to store calculations made using a form.
     * 
     * @return void
     */
    public static function registerCalculations()
    {
        $labels = [
            "name" => __("Calculations", "twentytwentyfour"),
            "singular_name" => __("Calculation", "twentytwentyfour"),
        ];

        $args = [
            "label" => __("Calculations", "twentytwentyfour"),
            "labels" => $labels,
            "description" => "Calculations made using form.",
            "public" => true,
            "publicly_queryable" => true,
            "show_ui" => true,
            "show_in_rest" => true,
            "rest_base" => "",
            "rest_controller_class" => "WP_REST_Posts_Controller",
            "has_archive" => false,
            "show_in_menu" => true,
            "show_in_nav_menus" => true,
            "delete_with_user" => false,
            "exclude_from_search" => false,
            "capability_type" => "post",
            "map_meta_cap" => true,
            "hierarchical" => false,
            "rewrite" => ["slug" => "calculations", "with_front" => true],
            "query_var" => true,
            "menu_icon" => "dashicons-calculator",
            "supports" => ["title", "editor", "custom-fields"],
            "show_in_graphql" => false,
        ];

        register_post_type("calculations", $args);
    }
}
