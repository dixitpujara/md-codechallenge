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

if(!defined('ABSPATH') ) exit;

/**
 * Public Class
 *
 * Manage Public Class
 *
 * @package Book Search
 * @since   1.0.0
 */
class Book_Search_Public {
   
    // function that runs when shortcode is called
    function book_serch_form_shortcode() { 
        global $wpdb;
        
        $min = $wpdb->get_var("SELECT min(cast(meta_value as unsigned)) as min FROM {$wpdb->postmeta} WHERE meta_key LIKE 'book_price'");
        $max = $wpdb->get_var("SELECT MAX(cast(meta_value as unsigned)) as max FROM {$wpdb->postmeta} WHERE meta_key LIKE 'book_price'");
        
        $message = '<div class="book-search-section">';
        $message .= '<div class="book_search_title_Sec">
            <h2 class="book_search_title">'.__("Book Search", "book_search").'</h2>
        </div>';

        $message.='<form id="search-form" method="post" action="#">
            <div class="book-name-field fieldset">
                <label for="name">'.__("Book Name", "book_search").' :</label>
                <input type="text" name="bookname" id="name">
            </div>
            <div class="book-author-field fieldset">
                <label for="author">'.__("Author", "book_search").' :</label>
                <input type="text" name="author"  id="author">
            </div>
            <div class="book-publisher-field fieldset">
                <label for="publisher">'.__("Publisher", "book_search").' :</label>
                <select name="publisher" id="publisher">
                    <option value="">Select</option>';
                    $publisherterms = get_terms(
                        array(
                            'taxonomy' => 'book_publisher',
                            'hide_empty' => false
                        )
                    );
                    if($publisherterms != null) {
                        foreach($publisherterms as $publisherterm){
                            $message .= '<option value="'.$publisherterm->term_id.'">'.$publisherterm->name.'</option>';
                        }
                    }
            $message .= '</select>
            </div>
            <div class = "book-ratings-field fieldset">
                <label for="rating">'.__("Rating", "book_search").' :</label>
                <select name="rating" id="rating">
                    <option value="">Select</option>';
                    for($i=1; $i<=5; $i++){
                        $message .= '<option value="'.$i.'">'.$i.'</option>';
                    }
                $message .='</select>
            </div>
            <div class = "book-price-field fieldset">
                <label for="price" class="form-label">'.__("Price", "book_search").' :</label>
                <input type="text" class="js-range-slider" value=""
                    data-skin="round"
                    data-type="double"
                    data-min="'.$min.'"
                    data-max="'.$max.'"
                    data-grid="false"
                />';
                $message .= wp_nonce_field('name_of_my_action', 'name_of_nonce_field');
            $message .='</div>
            <div class = "book-submit-button fieldset">
                <button type = "submit" class = "search-btn btn-default">'.__("Search", "book_search").'</button>
            </div>
        </form>';

        $message .='</div>';
        
        //After ajax call result load in below container class    
        $message .='<div class="book-search-result-section">
            <div class = "book_search_loading">
                    <div class = "book_search_container">
                        <div class="book-search-content"></div>
                     </div>
                 </div>
        </div>';

        // Output needs to be return
        return $message;

    }

    // Ajax call function which call on search and pagintation click result return
    function book_pagination_load_posts() {
        global $wpdb;
        $msg = '';
        if(isset($_POST['page'])){
            
            // get all value from search form
            $page = sanitize_text_field($_POST['page']);
            $bookname = $_POST['bookname'];
            $author = $_POST['author'];
            $publisher = $_POST['publisher'];
            $rating = $_POST['rating'];
            $from = (int)$_POST['from'];
            $to = (int)$_POST['to'];
            $cur_page = $page;
            $page -= 1;
            $per_page = get_option('posts_per_page');
            $previous_btn = true;
            $next_btn = true;
            $first_btn = true;
            $last_btn = true;
            $start = $page * $per_page;
            
            // arguments for wp_query run 
            $args=array(
                'post_type'         => 'book',
                'post_status '      => 'publish',
                'posts_per_page'    => $per_page,
                'offset'            => $start,
            );
            
            // argument for bookname
            if($bookname) {$args['bookname']= $bookname;}
            // Argument for both book author
            if($author) {$args['tax_query'][]=array (
                    'taxonomy' => 'book_author',
                    'field' => 'name',
                    'terms' => $author,
                );
            }

            // Argument for both book publisher
            if($publisher) {
                $args['tax_query'][]=array (
                    'taxonomy' => 'book_publisher',
                    'field' => 'term_id',
                    'terms' => $publisher,
                );
            }

            // Argument related book rating
            if($rating) {
                $args['meta_query'][] = array(
                    'key'       => 'book_rating',
                    'value'     => $rating,
                    'compare'   => '='
                );
            }

            // Argument for price range
            if( isset($from) && isset($to)) {
                $args['meta_query'][] = array(
                    'key'       => 'book_price',
                    'value'     => array( $from, $to ),
                    'type'         => 'numeric',
                    'compare'     => 'BETWEEN'
                );
            }

            // Query run with above argument set based on search form field set
            $all_blog_posts = new WP_Query($args);

             // For total count of result get argument set
             $args['posts_per_page']=-1;
             unset($args['offset']);

             //For total post count get query run
             $count_query = new WP_Query($args);

             //Count set in count variable
            $count = $count_query->post_count;

            // If found result  display below output
            if($all_blog_posts->have_posts() ) {?>
                <div class="book-search-result">
                	<div class="book-header">
                        <span><?php _e('No.', 'book-search'); ?></span>
                        <span><?php _e('Book Name', 'book-search'); ?></span>
                        <span><?php _e('Price', 'book-search'); ?></span>
                        <span><?php _e('Author', 'book-search'); ?></span>
                        <span><?php _e('Publisher', 'book-search'); ?></span>
                        <span><?php _e('Rating', 'book-search'); ?></span>
                    </div>
                	
                    <?php while ( $all_blog_posts->have_posts() ) {
                        $all_blog_posts->the_post(); 
                        $post_id=get_the_id();
                        $authorterms = get_the_terms($post->ID, 'book_author');
                        if($authorterms != null) {
                            $authoroutput = array();
                            foreach($authorterms as $authorterm) {
                                $authoroutput[] = '<a href="'.get_term_link($authorterm->term_id,'book_author').'">'.$authorterm->name.'</a>';
                                unset($authorterm);
                            }
                            $author = implode(", ", $authoroutput);
                        }

                        $publisherterms = get_the_terms($post->ID, 'book_publisher');
                        if($publisherterms != null) {
                            $publisheroutput = array();
                            foreach($publisherterms as $publisherterm) {
                                $publisheroutput[] = '<a href="'.get_term_link($publisherterm->term_id, 'book_publisher').'">'.$publisherterm->name.'</a>';
                                unset($publisherterm);
                            }
                            $publisher = implode(", ", $publisheroutput);
                        } $start++; ?>
                        <div class="book-record">
                            <span><?php echo $start; ?></span>                    
                            <span><a href="<?php echo get_the_permalink();?>"><?php echo get_the_title(); ?></a></span>
                            <span><?php echo '$'.get_post_meta($post_id, 'book_price', true); ?></span>                    
                            <span><?php echo $author; ?></span>
                            <span><?php echo    $publisher ;?></span>
                            <span class="book-ratings">
                            
                            <?php 
                            $book_ratings = get_post_meta($post_id, 'book_rating', true);
                            for ($i=0; $i<5; $i++) {
                             	if($i < $book_ratings){
                             		echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--! Font Awesome Pro 6.2.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"/></svg>';
                             	} else {
                             		echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--! Font Awesome Pro 6.2.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M287.9 0C297.1 0 305.5 5.25 309.5 13.52L378.1 154.8L531.4 177.5C540.4 178.8 547.8 185.1 550.7 193.7C553.5 202.4 551.2 211.9 544.8 218.2L433.6 328.4L459.9 483.9C461.4 492.9 457.7 502.1 450.2 507.4C442.8 512.7 432.1 513.4 424.9 509.1L287.9 435.9L150.1 509.1C142.9 513.4 133.1 512.7 125.6 507.4C118.2 502.1 114.5 492.9 115.1 483.9L142.2 328.4L31.11 218.2C24.65 211.9 22.36 202.4 25.2 193.7C28.03 185.1 35.5 178.8 44.49 177.5L197.7 154.8L266.3 13.52C270.4 5.249 278.7 0 287.9 0L287.9 0zM287.9 78.95L235.4 187.2C231.9 194.3 225.1 199.3 217.3 200.5L98.98 217.9L184.9 303C190.4 308.5 192.9 316.4 191.6 324.1L171.4 443.7L276.6 387.5C283.7 383.7 292.2 383.7 299.2 387.5L404.4 443.7L384.2 324.1C382.9 316.4 385.5 308.5 391 303L476.9 217.9L358.6 200.5C350.7 199.3 343.9 194.3 340.5 187.2L287.9 78.95z"/></svg>';
                             	}
                            } ?>
                            </span>
                        </div> 
                    <?php }
                    wp_reset_postdata(); ?>
                </table> 
            <?php } else {
                 // If not found any post below message display.
                 echo '<p class="no-result">';
                 _e('No books found', 'book-search');
                 echo '</p>';
            }
            
            // This is where the magic happens
            $no_of_paginations = ceil($count / $per_page);
            if($cur_page >= 7) {
                $start_loop = $cur_page - 3;
                if($no_of_paginations > $cur_page + 3)
                    $end_loop = $cur_page + 3;
                elseif($cur_page <= $no_of_paginations && $cur_page > $no_of_paginations - 6) {
                    $start_loop = $no_of_paginations - 6;
                    $end_loop = $no_of_paginations;
                }else{
                    $end_loop = $no_of_paginations;
                }
            } else {
                $start_loop = 1;
                if ($no_of_paginations > 7)
                    $end_loop = 7;
                else
                    $end_loop = $no_of_paginations;
            }
            
            // Pagination Buttons logic     
            ?>
            <div class='book-search-pagination'>
                <ul>
                    <?php if ($previous_btn && $cur_page > 1) {
                        $pre = $cur_page - 1; ?>
                         <li p='<?php echo $pre; ?>' class='active'> <?php _e('Previous','book-search');?> </li>
                    <?php } else if ($previous_btn) { ?>
                         <li class='inactive'> <?php _e('Previous','book-search');?> </li>
                    <?php }
                     
                    for ($i = $start_loop; $i <= $end_loop; $i++) {
                        if ($cur_page == $i){ ?>
                            <li p='<?php echo $i; ?>' class = 'selected' ><?php echo $i; ?></li>
                         <?php } else { ?>
                             <li p='<?php echo $i; ?>' class='active'><?php echo $i; ?></li>
                         <?php }
                    }
                     
                    if ($next_btn && $cur_page < $no_of_paginations) {
                         $nex = $cur_page + 1; ?>
                         <li p='<?php echo $nex; ?>' class='active'> <?php _e('Next','book-search');?> </li>
                    <?php } else if ($next_btn) { ?>
                         <li class='inactive'> <?php _e('Next','book-search');?> </li>
                    <?php } ?>
                </ul>
            </div>

        <?php }
        exit();
    }

    // js code related ajax call and function 
    function book_search_ajax_js(){ ?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                function book_load_all_posts(page){
                    var data = {
                        page: page,
                        bookname: $('#name').val(),
                        author: $('#author').val(),
                        publisher: $('#publisher').val(),
                        rating: $('#rating').val(),
                        from: $('.irs-from').text(),
                        to: $('.irs-to').text(),
                        action: "book-pagination-load-posts"
                    };
                    var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
                    $.post(ajaxurl, data, function(response) {
                    	$(".book-search-result-section").show();
                        $(".book_search_container").html('').append(response);
                    });
                }

                // On pagination button click ajax call
                jQuery(document).on("click",".book_search_container .book-search-pagination li.active", function (){
                    var page = jQuery(this).attr('p');
                    book_load_all_posts(page);
                });

                // On search button click ajax call
                jQuery('#search-form').on('submit', function(e) {
                    e.preventDefault();
                    var page = 1;
                    book_load_all_posts(page);
                });
            }); 
        </script>
        
        <?php
    }

    //Search by book name fucntion
    function book_search_by_title($where, $wp_query ){
        global $wpdb;
        if ($title = $wp_query->get('bookname') ) {
            $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql($wpdb->esc_like($title)).'%\'';
        }
        return $where;
    }
    
    /**
     * Adding Hooks
     *
     * @package Book Search
     * @since 1.0.0
     */
    function add_hooks(){

        add_shortcode('book_serch', array($this,'book_serch_form_shortcode'));
        add_action('wp_ajax_book-pagination-load-posts', array($this,'book_pagination_load_posts'));
        add_action('wp_ajax_nopriv_book-pagination-load-posts', array($this,'book_pagination_load_posts'));
        add_action('wp_footer', array($this,'book_search_ajax_js'));
        add_filter('posts_where', array($this,'book_search_by_title'), 10, 2);

    }
}  // EOF