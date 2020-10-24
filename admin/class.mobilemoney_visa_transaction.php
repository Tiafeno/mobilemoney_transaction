<?php
/**
 * Created by IntelliJ IDEA.
 * User: you-f
 * Date: 24/10/2020
 * Time: 10:14
 */

class MobileMoney_Visa_Transaction_Admin {
    public function __construct () {
        add_action('init', [$this, 'register_post_type']);
    }

    /**
     * Enregistrer un post type pour les transactions
     */
    public function register_post_type(){
        register_post_type('mm_visa_transaction', [
            'label' => "VISA Transaction",
            'labels' => [
                'name' => "Les transactions",
                'singular_name' => "Transaction",
                'add_new' => 'Ajouter',
                'add_new_item' => "Ajouter une nouvelle transaction",
                'edit_item' => 'Modifier',
                'view_item' => 'Voir',
                'search_items' => "Trouver des transactions",
                'all_items' => "Tous les transactions",
                'not_found' => "Aucun transaction trouver",
                'not_found_in_trash' => "Aucun transaction dans la corbeille"
            ],
            'public' => false,
            'hierarchical' => false,
            'menu_position' => null,
            'show_ui' => true,
            'has_archive' => false,
            'rewrite' => ['slug' => 'visa_transaction'],
            'capability_type' => 'post',
            'map_meta_cap' => true,
            'menu_icon' => 'dashicons-money-alt',
            'menu_position' => 100,
            'supports' => ['title', 'editor', 'custom-fields'],
            'show_in_rest' => true,
            'query_var' => true
        ]);
    }
}

new MobileMoney_Visa_Transaction_Admin();