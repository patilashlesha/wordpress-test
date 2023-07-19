<?php

/**
 * Plugin Name: Mini Game Challene
 * Plugin URI: 
 * Description: Mini Game Challenge developed by Ashlesha for WordPress Challenge for NeuroNation. Use this [nn_challenge_mini_game] shortcode.
 * Version: 0.2
 * Author: Ashlesha Patil
 * Author URI: 
 **/

defined('ABSPATH') or die("No Script Kiddies allowed!");

define('NN_CHALLENGE_FILE', __FILE__);
define('NN_CHALLENGE_DIR', plugin_dir_path(__FILE__));
define('NN_CHALLENGE_URI',plugin_dir_url(__FILE__));

define('NNC_TBL', 'nn_game_challenge');

//? require neccessory files
require_once NN_CHALLENGE_DIR . '/includes/functions.php';
require_once NN_CHALLENGE_DIR . '/includes/enqueue_scripts.php';
require_once NN_CHALLENGE_DIR . '/includes/activate.php';
require_once NN_CHALLENGE_DIR . '/includes/shortcodes.php';
require_once NN_CHALLENGE_DIR . '/includes/ajax.php';
