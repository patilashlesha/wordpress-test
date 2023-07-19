<?php
defined('ABSPATH') or die("No Script Kiddies allowed!");

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('nn-game-main', NN_CHALLENGE_URI . '/assets/nn_game.css');
    wp_enqueue_script('nn-game-main', NN_CHALLENGE_URI . '/assets/nn_game.js', ['jquery'], false, true);
    wp_localize_script('nn-game-main', 'nn_game_config', ['ajax_url' => admin_url('admin-ajax.php'), 'nn_csrf' => wp_create_nonce('nn_game')]);
});
