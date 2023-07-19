<?php
defined('ABSPATH') or die("No Script Kiddies allowed!");

add_shortcode('nn_challenge_mini_game', function () {
    if (!array_key_exists('nn_game_user_id', $_COOKIE)) {
        ob_start();
?>
        <div class="nn-create-user">
            <div class="new-user">
                <div class="new-user-form">
                    <label for="username">Create Username to play the game!</label>
                    <input type="text" class="new-user-field" id="username">
                    <button class="create-new-user">Play Game!</button>
                </div>
                <div class="nn-game-notice"></div>
            </div>
        </div>
    <?php
        return ob_get_clean();
    } else {
        ob_start();
    ?>
        <div class="nn_challenge">
            <div class="nn_row">
                <div class="nn_game">
                    <h3>Hello <?= nn_get_user_name() ?></h3>
                    <div class="score-board">
                        <div class="user-score">
                            <h4>Your Score: <Strong class="user-score-count"><?= get_score(); ?></Strong></h4>
                        </div>
                    </div>
                    <div class="gameboard">
                        waiting for your unit...
                    </div>
                    <div class="user-input">
                        <p><strong>Please Select your unit to the battlefield:</strong></p>
                        <button class="battlefield-unit" data-unit="cavalry">Cavalry </button>
                        <button class="battlefield-unit" data-unit="archers">Archers </button>
                        <button class="battlefield-unit" data-unit="pikemen">Pikemen </button>
                    </div>
                </div>
                <div class="nn_game_history">
                    <h3>Game History</h3>
                    <button class="restart_nn_game">Reset Game!</button>
                    <div class="nn_game_rounds"></div>
                </div>
            </div>
        </div>
<?php
        return ob_get_clean();
    }
});
