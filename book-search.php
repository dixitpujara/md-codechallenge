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
 *
 * @wordpress-plugin
 * Plugin Name: Book Search
 * Plugin URI: https://github.com/dixitpujara/md-codechallenge
 * Description: Search and Filtering system for Book with pagination.
 * Version: 1.0.0
 * Author: Dixit Pujara
 * Author URI: https://github.com/dixitpujara
 * Text Domain: book-search
 * License: GPLv2
 * Domain Path: /languages
 **/

/* define folder*/
if (!defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}

/*
* Set up Plugin Globals
*/
if(!defined('BOOK_SEARCH_DIR'))
  define('BOOK_SEARCH_DIR', dirname(__FILE__)); // Plugin dir

if(!defined('BOOK_SEARCH_VERSION'))
  define('BOOK_SEARCH_VERSION', '1.0.0'); // Plugin Version

if(!defined('BOOK_SEARCH_URL'))
  define('BOOK_SEARCH_URL', plugin_dir_url(__FILE__)); // Plugin url

if(!defined('BOOK_SEARCH_INC_DIR'))
  define('BOOK_SEARCH_INC_DIR', BOOK_SEARCH_DIR.'/includes'); // Plugin include dir

if(!defined('BOOK_SEARCH_INC_URL'))
  define('BOOK_SEARCH_INC_URL', BOOK_SEARCH_URL.'includes'); // Plugin include url

if(!defined('BOOK_SEARCH_ADMIN_DIR')) 
  define('BOOK_SEARCH_ADMIN_DIR', BOOK_SEARCH_INC_DIR.'/admin'); // Plugin admin dir


// Global variables
global $book_search_admin, $book_search_public, $book_search_script;

// CSS and JS Files
require_once(BOOK_SEARCH_INC_DIR.'/class-book-search-script.php');
$book_search_script = new Book_Search_Script();
$book_search_script->add_hooks();

// Frontend  Related Code
require_once(BOOK_SEARCH_INC_DIR.'/class-book-search-public.php');
$book_search_public = new Book_Search_Public();
$book_search_public->add_hooks();

// Dashboard (Backend) Related Code
require_once(BOOK_SEARCH_ADMIN_DIR.'/class-book-search-admin.php');
$book_search_admin = new Book_Search_Admin();
$book_search_admin->add_hooks();


// Plugin activation hook
register_activation_hook(__FILE__, 'Book_Search_activation');

// Plugin deactivation hook
register_uninstall_hook(__FILE__, 'Book_Search_deactivation');

// On plugin activation book cpt and taxonomy regoster
function Book_Search_activation()
{
    $bookSearchAdmin = new Book_Search_Admin();
    $bookSearchAdmin->book_custom_post_type();
    $bookSearchAdmin->book_author_custom_taxonomy();
    $bookSearchAdmin->book_publisher_custom_taxonomy();
    flush_rewrite_rules();
}

/**
 * On plugin deactivation function called
 * @retutn void
 */
function Book_Search_deactivation() 
{
    // On Uninstallation Plugin cpt and taxonomy remove
    unregister_post_type('book');
    unregister_taxonomy('book_author');
    unregister_taxonomy('book_publisher');
}  // EOF