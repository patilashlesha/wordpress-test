<?php
defined('ABSPATH') or die("No Script Kiddies allowed!");

add_action('init', function () {
    global $wpdb;
    $tableName = $wpdb->prefix . NNC_TBL;
    if (array_key_exists('nn_game_user_id', $_COOKIE)) {
        $authToken = sanitize_text_field($_COOKIE['nn_game_user_id']);
        $row = $wpdb->get_row("SELECT * FROM $tableName WHERE auth_token='$authToken'");
        if ($row == NULL) {
            setcookie('nn_game_user_id', '', -1, '/');
            wp_redirect($_SERVER['REQUEST_URI']);
            die();
        }
    }
});

add_action('wp_ajax_user_unit_send', 'nn_user_input_recieved');
add_action('wp_ajax_nopriv_user_unit_send', 'nn_user_input_recieved');

function nn_user_input_recieved()
{
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    $unit = $_POST['unit'];
    $csrf = $_POST['nn_csrf'];
    //? check for csrf token
    if (!wp_verify_nonce($csrf, 'nn_game')) {
        echo json_encode(['status' => 'error', 'msg' => 'Unable to verify the Security Token!']);
        die();
    }
    //? check for input
    if (!in_array($unit, ["cavalry", "archers", "pikemen"])) {
        echo json_encode(['status' => 'error', 'msg' => 'Invalid Response Sent!']);
        die();
    }
    //? check if user is created!
    if (!array_key_exists('nn_game_user_id', $_COOKIE)) {
        echo json_encode(['status' => 'error', 'msg' => 'Please create a user first!']);
        die();
    }
    //? check if 20 rounds are completed!
    if (count(get_rounds()) >= 20) {
        echo json_encode(['status' => 'error', 'msg' => '<strong><span style="color:#b30000">20 Rounds are finished! Please restart the game!</span></strong>']);
        die();
    }

    $opponentUnit = getOppenentUnit();
    $winner = getWinner($unit, $opponentUnit);
    save_round([$unit, $opponentUnit]);
    if ($winner[0] == true && $winner[1] == false) {
        $decision = "<strong><span style='color:green'>You Won and computer Lose!</span></strong>";
    }
    if ($winner[0] == false && $winner[1] == true) {
        $decision = "<strong><span style='color:#b30000'>You Lose and computer Won!</span></strong>";
    }
    if ($winner[0] == false && $winner[1] == false) {
        $decision = "<strong><span style='color:#b30000'>You both are Lose</span></strong>";
    }
    echo json_encode(['status' => 'success', 'msg' => "Opponent selected $opponentUnit <br> $decision"]);
    die();
}


//? create game user

add_action('wp_ajax_create_new_user', 'nn_create_game_user');
add_action('wp_ajax_nopriv_create_new_user', 'nn_create_game_user');
function nn_create_game_user()
{
    $username = sanitize_text_field($_POST['username']);
    $authCookie = password_hash(bin2hex(rand()), PASSWORD_BCRYPT);
    $insert = createUser($username, $authCookie);
    if ($insert) {
        setcookie('nn_game_user_id', $authCookie, 0, '/');
        echo json_encode(['status' => 'success', 'msg' => 'Acount Created!']);
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'Unable to create the Account! Please try again']);
    }
    die();
}



//? get total Rounds!
add_action('wp_ajax_get_total_rounds', 'nn_get_total_rounds');
add_action('wp_ajax_nopriv_get_total_rounds', 'nn_get_total_rounds');

function nn_get_total_rounds()
{
    $rounds = get_rounds();
    $decision = '';
    $i = 1;
    foreach ($rounds as $round) {
        $decision .= "<strong>Round $i:<br>You Selected {$round[0]} and opponent selected {$round[1]}</strong>";
        $winner = getWinner($round[0], $round[1]);
        if ($winner[0] == true && $winner[1] == false) {
            $decision .= "<p><strong><span style='color:green'>You Won and computer Lose!</span></strong></p>";
        }
        if ($winner[0] == false && $winner[1] == true) {
            $decision .= "<p><strong><span style='color:#b30000'>You Lose and computer Won!</span></strong></p>";
        }
        if ($winner[0] == false && $winner[1] == false) {
            $decision .= "<p><strong><span style='color:#b30000'>You both are Lose</span></strong></p>";
        }
        $i++;
    }
    echo json_encode(['status' => 'success', 'msg' => $decision]);
    die();
}


add_action('wp_ajax_restart_nn_game', 'nn_restart_nn_game');
add_action('wp_ajax_nopriv_restart_nn_game', 'nn_restart_nn_game');
function nn_restart_nn_game()
{
    $csrf = $_POST['nn_csrf'];
    //? check for csrf token
    if (!wp_verify_nonce($csrf, 'nn_game')) {
        echo json_encode(['status' => 'error', 'msg' => 'Unable to verify the Security Token!']);
        die();
    }
    $username = sanitize_text_field($_POST['username']);
    $authCookie = password_hash(bin2hex(rand()), PASSWORD_BCRYPT);
    $insert = createUser(nn_get_user_name(), $authCookie);
    if ($insert) {
        setcookie('nn_game_user_id', $authCookie, 0, '/');
        echo json_encode(['status' => 'success', 'msg' => 'Acount Created!']);
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'Unable to create the Account! Please try again']);
    }
    die();
}

//? get Total Score
add_action('wp_ajax_get_total_score', 'nn_get_total_score');
add_action('wp_ajax_nopriv_get_total_score', 'nn_get_total_score');
function nn_get_total_score()
{
    $csrf = $_POST['nn_csrf'];
    //? check for csrf token
    if (!wp_verify_nonce($csrf, 'nn_game')) {
        echo json_encode(['status' => 'error', 'msg' => 'Unable to verify the Security Token!']);
        die();
    }

    echo json_encode(['status' => 'success', 'msg' => get_score()]);
    die();
}
