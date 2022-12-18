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
if(!defined('ABSPATH')) exit;

/**
 * Script Class
 *
 * Manage Script Class
 *
 * @package Book Search
 * @since 1.0.0
 */
class Book_Search_Script {
    
    // Add css and js frontend side
    function book_search_scripts(){
        
        // Price range slider related js and custom js file added
        wp_register_script('rangeSlider_js', BOOK_SEARCH_URL.'/assets/js/ion.rangeSlider.min.js', array('jquery'), BOOK_SEARCH_VERSION, true);
        wp_enqueue_script('rangeSlider_js');
        wp_register_script('custom_js', BOOK_SEARCH_URL.'/assets/js/custom.js', array('jquery'), BOOK_SEARCH_VERSION, true);
        wp_enqueue_script('custom_js');

        // Price range slider related js and custom css file added
        wp_register_style('rangeSlider_css', BOOK_SEARCH_URL.'/assets/css/ion.rangeSlider.min.css', '', BOOK_SEARCH_VERSION, false);
        wp_enqueue_style('rangeSlider_css');
        wp_register_style('custom_css', BOOK_SEARCH_URL.'/assets/css/custom.css', '', BOOK_SEARCH_VERSION, false);
        wp_enqueue_style('custom_css');

    }

    /**
     * Adding Hooks
     *
     * @package Book Search
     * @since 1.0.0
     */
    function add_hooks()
    {
        
        add_action('wp_enqueue_scripts', array($this,'book_search_scripts'));

    }
}  // EOF