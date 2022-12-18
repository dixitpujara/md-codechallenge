<?php
/**
 * Book search plugin
 * php version 7.4.30
 *
 * @category  PHP
 * @package   Book_Search
 * @author    Dixit Pjara <dixitpujara@gmail.com>
 * @copyright 2022 Dixit Pujara
 * @license   https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html GPL-2.0+ 
 * @link      https://github.com/dixitpujara/md-codechallenge
 */
// Exit if accessed directly
if (!defined('ABSPATH'))exit;

/**
 * Admin Class
 *
 * Manage Admin Class
 *
 * @package Book Search
 * @since 1.0.0
 */
class Book_Search_Admin {
    
    // Register a custom post type called "book". 
    function book_custom_post_type() {
        $labels = array(
            'name'                  => _x('Books', 'Post type general name', 'book-search'),
            'singular_name'         => _x('Book', 'Post type singular name', 'book-search'),
            'menu_name'             => _x('Books', 'Admin Menu text', 'book-search'),
            'name_admin_bar'        => _x('Book', 'Add New on Toolbar', 'book-search'),
            'add_new'               => __('Add Nem', 'book-search'),
            'add_new_item'          => __('Add New Book', 'book-search'),
            'new_item'              => __('New Book', 'book-search'),
            'edit_item'             => __('Edit Book', 'book-search'),
            'view_item'             => __('View Book', 'book-search'),
            'all_items'             => __('All Books', 'book-search'),
            'search_items'          => __('Search Books', 'book-search'),
            'parent_item_colon'     => __('Parent Books:', 'book-search'),
            'not_found'             => __('No books found.', 'book-search'),
            'not_found_in_trash'    => __('No books found in Trash.', 'book-search'),
            'featured_image'        => _x('Book Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'book-search'),
            'set_featured_image'    => _x('Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'book-search'),
            'remove_featured_image' => _x('Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'book-search'),
            'use_featured_image'    => _x('Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'book-search'),
            'archives'              => _x('Book archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'book-search'),
            'insert_into_item'      => _x('Insert into book', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'book-search'),
            'uploaded_to_this_item' => _x('Uploaded to this book', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'book-search'),
            'filter_items_list'     => _x('Filter books list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'book-search'),
            'items_list_navigation' => _x('Books list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'book-search'),
            'items_list'            => _x('Books list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'book-search'),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'taxonomies'         => array('book_author', 'book_publisher'),
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'book'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
        );

        register_post_type('book', $args);
    }

    // Register Author Taxonomy
    function book_author_custom_taxonomy() 
    {
        $labels = array(
            'name'                       => _x('Authors', 'taxonomy general name', 'book-search'),
            'singular_name'              => _x('Author', 'taxonomy singular name', 'book-search'),
            'menu_name'                  => __('Author', 'book-search'),
            'all_items'                  => __('All Authors', 'book-search'),
            'parent_item'                => __('Parent Author', 'book-search'),
            'parent_item_colon'          => __('Parent Author:', 'book-search'),
            'new_item_name'              => __('New Author Name', 'book-search'),
            'add_new_item'               => __('Add New Author', 'book-search'),
            'edit_item'                  => __('Edit Author', 'book-search'),
            'update_item'                => __('Update Author', 'book-search'),
            'view_item'                  => __('View Author', 'book-search'),
            'separate_items_with_commas' => __('Separate authors with commas', 'book-search'),
            'add_or_remove_items'        => __('Add or remove authors', 'book-search'),
            'choose_from_most_used'      => __('Choose from the most used', 'book-search'),
            'popular_items'              => __('Popular Authors', 'book-search'),
            'search_items'               => __('Search Authors', 'book-search'),
            'not_found'                  => __('Not Found', 'book-search'),
            'no_terms'                   => __('No authors', 'book-search'),
            'items_list'                 => __('Authors list', 'book-search'),
            'items_list_navigation'      => __('Authors list navigation', 'book-search'),
        );
        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => true,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
        );
        register_taxonomy('book_author', array('book'), $args);
    }

    // Register Publisher Taxonomy
    function book_publisher_custom_taxonomy() {
        $labels = array(
            'name'                       => _x('Publishers', 'taxonomy general name', 'book-search'),
            'singular_name'              => _x('Publisher', 'taxonomy singular name', 'book-search'),
            'menu_name'                  => __('Publisher', 'book-search'),
            'all_items'                  => __('All Publishers', 'book-search'),
            'parent_item'                => __('Parent Publisher', 'book-search'),
            'parent_item_colon'          => __('Parent Publisher:', 'book-search'),
            'new_item_name'              => __('New Publisher Name', 'book-search'),
            'add_new_item'               => __('Add New Publisher', 'book-search'),
            'edit_item'                  => __('Edit Publisher', 'book-search'),
            'update_item'                => __('Update Publisher', 'book-search'),
            'view_item'                  => __('View Publisher', 'book-search'),
            'separate_items_with_commas' => __('Separate publishers with commas', 'book-search'),
            'add_or_remove_items'        => __('Add or remove publishers', 'book-search'),
            'choose_from_most_used'      => __('Choose from the most used', 'book-search'),
            'popular_items'              => __('Popular Publishers', 'book-search'),
            'search_items'               => __('Search Publishers', 'book-search'),
            'not_found'                  => __('Not Found', 'book-search'),
            'no_terms'                   => __('No publishers', 'book-search'),
            'items_list'                 => __('Publishers list', 'book-search'),
            'items_list_navigation'      => __('Publishers list navigation', 'book-search'),
        );
        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => true,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
        );
        register_taxonomy('book_publisher', array('book'), $args);
    }

    //Book price metabox register
    function book_price_field() {
        add_meta_box('book-price-field', 'Book Price', array($this,'book_price_cb'), 'book', 'side');
    }

    //Book rating metabox register 
    function book_rating_field() {
        add_meta_box('book-rating-field', 'Book Rating', array($this,'book_rating_cb'), 'book', 'side');
    }

    //Book price metabox callback function
    function book_price_cb($post) {
        $book_price = get_post_meta($post->ID, 'book_price', true);
        ?>
        <input type="number" name="book_price" value="<?php echo esc_attr($book_price); ?>">
        <?php
    }

    //Book rating meta callback function
    function book_rating_cb($post) {
        $book_rating = get_post_meta($post->ID, 'book_rating', true);
        ?>
        <select name="book_rating">
            <?php for($i=1; $i<=5; $i++){ ?>
                <option value="<?php echo $i; ?>" <?php if($book_rating ==  $i ){echo "selected";} ?>><?php echo $i; ?></option>
            <?php } ?>
        </select>
        <?php
    }

    //Book price and rating meta value store postmeta table
    function save_metabox_val($post_id) {
        if (isset($_POST['book_price'])) {
            update_post_meta($post_id, 'book_price', $_POST['book_price']);
        }
        if (isset($_POST['book_rating'])) {
            update_post_meta($post_id, 'book_rating', $_POST['book_rating']);
        }
    }

    /**
     * Adding Hooks
     *
     * @package Book Search
     * @since 1.0.0
    */
    function add_hooks() 
    {

        add_action('init', array($this, 'book_custom_post_type'));
        add_action('init', array($this,'book_author_custom_taxonomy'));
        add_action('init', array($this,'book_publisher_custom_taxonomy'));
        add_action('add_meta_boxes', array($this,'book_price_field'));
        add_action('add_meta_boxes', array($this,'book_rating_field'));
        add_action('save_post', array($this,'save_metabox_val'));
    
    }
}