<?php

defined('ABSPATH') or die("No Script Kiddies allowed!");


function getOppenentUnit()
{
    global $wpdb; //? get from database
    $tableName = $wpdb->prefix . NNC_TBL;
    $authToken = sanitize_text_field($_COOKIE['nn_game_user_id']);
    $row = $wpdb->get_row("SELECT * FROM $tableName WHERE auth_token='$authToken'");
    $userProb = unserialize($row->user_probblty);
    $a = getProb();
    if ($userProb[$a] > 0) {
        $userProb[$a]--;
        $wpdb->update($tableName, ['user_probblty' => serialize($userProb)], ['auth_token' => $authToken]);
        return $a;
    } else {
        return getOppenentUnit();
    }
}

function getProb()
{
    $oppenentUnit = ["cavalry", "archers", "pikemen"];
    return $oppenentUnit[array_rand($oppenentUnit)];
}

function getWinner($user, $computer)
{
    switch ($user) {
        case 'cavalry':
            if ($computer == 'archers') {
                return [0 => true, 1 => false];
            } else if ($computer == 'pikemen') {
                return [0 => false, 1 => true];
            } else {
                return [0 => false, 1 => false];
            }
            break;

        case 'archers':
            if ($computer == 'pikemen') {
                return [0 => true, 1 => false];
            } else if ($computer == 'cavalry') {
                return [0 => false, 1 => true];
            } else {
                return [0 => false, 1 => false];
            }
            break;

        case 'pikemen':
            if ($computer == 'cavalry') {
                return [0 => true, 1 => false];
            } else if ($computer == 'archers') {
                return [0 => false, 1 => true];
            } else {
                return [0 => false, 1 => false];
            }
            break;
    }
}


function createUser($user, $auth)
{
    global $wpdb;
    $tableName = $wpdb->prefix . NNC_TBL;
    $prob10 = getProb();
    if ($prob10 == 'cavalry') {
        $arr = ['cavalry' => 10, "archers" => 5, "pikemen" => 5];
    }
    if ($prob10 == 'archers') {
        $arr = ['cavalry' => 5, "archers" => 10, "pikemen" => 5];
    }
    if ($prob10 == 'pikemen') {
        $arr = ['cavalry' => 5, "archers" => 5, "pikemen" => 10];
    }
    return $wpdb->insert($tableName, [
        'username' => $user,
        'auth_token' => $auth,
        'game_rounds' => '',
        'user_probblty' => serialize($arr)
    ]);
}


function save_round($winner)
{
    global $wpdb;
    $tableName = $wpdb->prefix . NNC_TBL;
    $authToken = sanitize_text_field($_COOKIE['nn_game_user_id']);
    $rounds = get_rounds();
    if (count($rounds) < 20) {
        $rounds[] = $winner;
    }
    $Rounds = serialize($rounds);
    return $wpdb->update($tableName, ['game_rounds' => $Rounds], ['auth_token' => $authToken]);
}


function get_rounds()
{
    global $wpdb;
    $tableName = $wpdb->prefix . NNC_TBL;
    $authToken = sanitize_text_field($_COOKIE['nn_game_user_id']);
    $row = $wpdb->get_row("SELECT * FROM $tableName WHERE auth_token='$authToken'");
    if (empty($row->game_rounds)) {
        return [];
    }
    return (array) unserialize($row->game_rounds);
}


function nn_get_user_name()
{
    global $wpdb;
    $tableName = $wpdb->prefix . NNC_TBL;
    $authToken = sanitize_text_field($_COOKIE['nn_game_user_id']);
    $row = $wpdb->get_row("SELECT * FROM $tableName WHERE auth_token='$authToken'");
    return $row->username;
}


function get_score()
{
    $score = 0;
    $rounds = get_rounds();
    foreach ($rounds as $round) {
        $winner = getWinner($round[0], $round[1]);
        $score = ($winner[0]) ? $score + 1 : $score - 1;
    }
    return $score;
}
