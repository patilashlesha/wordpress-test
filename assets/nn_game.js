(function ($) {
    //? new User
    if ($('.nn-create-user').length) {
        $(document).on('click', '.create-new-user', function () {
            username = $('.new-user-field').val()
            if (username.length <= 3) {
                $('.nn-game-notice').html('Username is required and minimum 3 characters')
                return;
            }
            $('button.battlefield-unit').prop('disabled', true).addClass('disabled')
            $.ajax({
                type: "post",
                url: nn_game_config.ajax_url,
                data: { action: 'create_new_user', username, nn_csrf: nn_game_config.nn_csrf },
                beforeSend: function () {
                    $('.nn-game-notice').html('Please wait we are creating a username')
                },
                success: function (response) {
                    res = JSON.parse(response)
                    if (res.status === 'success') {
                        $('.nn-game-notice').append(`<p>Account created!</p>`)
                        setTimeout(() => { location.reload() }, 250)
                    } else if (res.status === 'error') {
                        $('.nn-game-notice').html(`<p>${res.msg}</p>`)
                    } else {
                        $('.nn-game-notice').html(`<p>Something went wrong please try again later!</p>`)
                    }
                },
                error: function () {
                    $('.nn-game-notice').html('Something went wrong please try again later!')
                }
            })
        });
    }

    //? Play Game
    if ($('.nn_challenge').length) {
        function nn_game_rounds() {
            $.ajax({
                type: "post",
                url: nn_game_config.ajax_url,
                data: {
                    action: 'get_total_rounds',
                    nn_csrf: nn_game_config.nn_csrf
                },
                success: function (response) {
                    res = JSON.parse(response)
                    if (res.status == 'success') {
                        $('.nn_game_rounds').html(res.msg)
                    } else if (res.status == 'error') {
                        $('.nn_game_rounds').html(res.msg)
                    } else {
                        $('.nn_game_rounds').html('Something went wrong please try again later!')
                    }
                },
                error: function () {
                    $('.nn_game_rounds').html('Something went wrong please try again later!')
                }
            });
            $.ajax({
                type: "post",
                url: nn_game_config.ajax_url,
                data: {
                    action: 'get_total_score',
                    nn_csrf: nn_game_config.nn_csrf
                },
                success: function (response) {
                    res = JSON.parse(response)
                    if (res.status == 'success') {
                        $('.user-score-count').html(res.msg)
                    } else if (res.status == 'error') {
                        $('.user-score-count').html(res.msg)
                    } else {
                        $('.user-score-count').html('Something went wrong please try again later!')
                    }
                },
                error: function () {
                    $('.user-score-count').html('Something went wrong please try again later!')
                }
            });
        }
        nn_game_rounds();
        $(document).on('click', 'button.battlefield-unit', function () {
            unit = $(this).data('unit')
            $('button.battlefield-unit').prop('disabled', true).addClass('disabled')
            $('.gameboard').html(`<p>You have selected ${unit}</p>`)
            $.ajax({
                type: "post",
                url: nn_game_config.ajax_url,
                data: {
                    action: 'user_unit_send',
                    unit,
                    nn_csrf: nn_game_config.nn_csrf,
                },
                beforeSend: function () {
                    $('.gameboard').append(`<p>Waiting for oppenent!</p>`)
                },
                success: function (response) {
                    res = JSON.parse(response)
                    if (res.status === 'success') {
                        $('.gameboard').append(`<p>${res.msg}</p>`)
                    } else if (res.status === 'error') {
                        $('.gameboard').append(`<p>${res.msg}</p>`)
                    } else {
                        $('.gameboard').append(`<p>Something went wrong please try again later!</p>`)
                    }
                    $('button.battlefield-unit').prop('disabled', false).removeClass('disabled')
                    nn_game_rounds()
                },
                error: function (e) {
                    $('.gameboard').html('Something Went wrong Please try again later!')
                    $('button.battlefield-unit').prop('disabled', false).removeClass('disabled')
                    nn_game_rounds()
                }
            });
        })
        $(document).on('click', '.restart_nn_game', function () {
            $.ajax({
                type: "post",
                url: nn_game_config.ajax_url,
                data: {
                    action: 'restart_nn_game',
                    nn_csrf: nn_game_config.nn_csrf,
                },
                success: function (response) {
                    location.reload()
                },
                error: function () {
                    alert('Unable to reset the game!')
                }
            });
        })
    }
})(jQuery);