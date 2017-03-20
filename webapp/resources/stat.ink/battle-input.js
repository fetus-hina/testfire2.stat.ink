/*! Copyright (C) 2016 AIZAWA Hina | MIT License */
(function ($, undefined) {
    "use strict";
    $(function () {
        var initialized = false;
        var translate = function (text) {
            var lang = $('html').attr('lang');
            switch (text) {
                case 'Favorite Weapons':
                    switch (lang) {
                        case 'ja-JP':
                            return 'よく使うブキ';

                        case 'es-ES':
                        case 'es-MX':
                            return 'Armas Favoritas';

                        default:
                            return text;
                    }
                    break;

                default:
                    return text;
            }
        };

        var $modal = $('#inputModal');
        var $selectWeapons = $('.battle-input-form--weapons', $modal);
        var $buttonStages = $('.battle-input-form--stages', $modal);
        var $buttonResults = $('.battle-input-form--result', $modal);
        var $regularSubmit = $('#battle-input-form--regular--submit', $modal);
        var $selectDisplay = $('#battle-input-form--regular--display', $modal);
        var $selectController = $('#battle-input-form--regular--controller', $modal);

        var updateUuidRegular = function () {
            $('#battle-input-form--regular--uuid').val(UUID.genV1().hexString);
        };

        var serializeForm = function ($form) {
            var ret = {};
            $.each($form.serializeArray(), function (i, obj) {
                var name = obj.name;
                var value = obj.value;
                if (name && value !== null && value !== undefined) {
                    value = (value + "").trim();
                    if (value !== "") {
                        ret[name] = value;
                    }
                }
            });
            if ('rank_after' in ret && !('rank_exp_after' in ret)) {
                delete ret.rank_after;
            }
            return ret;
        };

        var refresh = function () {
            $.ajax('/api/internal/current-data', {
                cache: false,
                method: 'GET',
                dataType: 'json',
                success: function (json) {
                    // ステージ変更時にinitializedフラグを落とす仕込み
                    (function () {
                        var timerId;
                        if (timerId) {
                            window.clearTimeout(timerId);
                        }
                        timerId = window.setTimeout(function () {
                            initialized = false;
                        }, json.current.period.next * 1000);
                    })();

                    $.each(['regular'], function (i, modeKey) {
                        // ルールを見た目用と電文用の<input>に正しく設定する
                        // （主にガチマッチ用。レギュラーも同じ仕組みにしておけば安心なのでそうしている）
                        if (json.current[modeKey] && json.current[modeKey].rule) {
                            var rule = json.current[modeKey].rule;
                            var $inputs = $('input', $modal);
                            $inputs
                                .filter(function () { return $(this).attr('id') === 'battle-input-form--' + modeKey + '--rule'; })
                                .val(rule.key);
                            $inputs
                                .filter(function () { return $(this).attr('id') === 'battle-input-form--' + modeKey + '--rule--label'; })
                                .val(rule.name);
                        }

                        // ステージ用の <button> のラベルを正しく設定する
                        // 広い画面ではフルのステージ名を、狭い画面では短縮のステージ名を表示する
                        if (json.current[modeKey] && json.current[modeKey].maps.length) {
                            var $buttons = $buttonStages.filter(function () {
                                return $(this).attr('data-game-mode') === modeKey;
                            });
                            $buttons.each(function (index) {
                                var $this = $(this);
                                var key = json.current[modeKey].maps[index];
                                if (key) {
                                    $this
                                        .attr('data-value', key)
                                        .attr('data-image', json.maps[key].image) // 今のところ使う予定なし
                                        .empty()
                                        .append($('<span>', {'class': 'hidden-xs'}).text(json.maps[key].name))
                                        .append($('<span>', {'class': 'visible-xs-inline'}).text(json.maps[key].shortName));
                                }
                            });
                        }
                    });

                    // ブキ一覧の <select> の <option> を作成する
                    $selectWeapons.each(function () {
                        var $this = $(this);
                        $this.empty();

                        // お気に入りのブキ
                        if (json.favWeapons && json.favWeapons.length > 0) {
                            $this.append((function () {
                                var $group = $('<optgroup>', {label: translate('Favorite Weapons')});
                                $.each(json.favWeapons, function (i, weapon) {
                                    $group.append(
                                        $('<option>', {label: weapon.name, value: weapon.key}).text(weapon.name)
                                    );
                                });
                                return $group;
                            })());
                        }

                        // 種類別
                        $.each(json.weapons, function (key, type) {
                            console.log(type.list.length);
                            if (type.list.length === 0) {
                                return;
                            }
                            $this.append((function () {
                                var $group = $('<optgroup>', {label: type.name});
                                $.each(type.list, function (key, weapon) {
                                    $group.append(
                                        $('<option>', {label: weapon.name, value: key}).text(weapon.name)
                                    );
                                });
                                return $group;
                            })());
                        });
                    });

                    $selectDisplay.each(function () {
                        var $this = $(this);
                        $this.empty().append($('<option>').attr('data-controllers', 'null'));
                        $.each(json.displayModes, function (key, value) {
                            $this.append(
                                $('<option>', {label: value.name, value: key})
                                    .attr('data-controllers', JSON.stringify(value.controllers))
                                    .text(value.name)
                            );
                        });
                    });

                    $selectController.each(function () {
                        var $this = $(this);
                        $this.empty().append($('<option>'));
                        $.each(json.controllers, function (key, value) {
                            $this.append(
                                $('<option>', {label: value.name, value: key})
                                    .text(value.name)
                            );
                        });
                    });

                    initialized = true;
                },
            });
        };

        var validateRegular = function () {
            var $form = $('form#battle-input-form--regular');
            var $requires = $([
                '#battle-input-form--regular--rule',
                '#battle-input-form--regular--lobby',
                '#battle-input-form--regular--weapon',
                '#battle-input-form--regular--stage',
                '#battle-input-form--regular--result',
            ].join(','), $form);
            var $empty = $requires.filter(function () {
                return $(this).val() == '';
            });
            if ($empty.length) {
                return false;
            }

            var $elem;
            var value;
            $elem = $('#battle-input-form--regular--point', $form);
            value = ($elem.val() + "").trim();
            if (value !== '' && !value.match(/^\d+$/)) {
                return false;
            }

            $elem = $('#battle-input-form--regular--kill', $form);
            value = ($elem.val() + "").trim();
            if (value !== '') {
                if (!value.match(/^\d+$/)) {
                    return false;
                }
                value = parseInt(value, 10);
                if (value < 0 || value > 99) {
                    return false;
                }
            }

            $elem = $('#battle-input-form--regular--death', $form);
            value = ($elem.val() + "").trim();
            if (value !== '') {
                if (!value.match(/^\d+$/)) {
                    return false;
                }
                value = parseInt(value, 10);
                if (value < 0 || value > 99) {
                    return false;
                }
            }
            return true;
        };

        var updateAgentVersion = function () {
            var $input = $('input[name="agent_version"]');
            $input.val((function (detect) {
                var comments = [
                    $input.attr('data-revision'),
                ];
                if (detect.mac) {
                    comments.push('macOS');
                } else if (detect.windows) {
                    comments.push('Windows');
                } else if (detect.windowsphone) {
                    comments.push('Windows Phone');
                } else if (detect.linux) {
                    comments.push('Linux');
                } else if (detect.chromeos) {
                    comments.push('Chrome OS');
                } else if (detect.android) {
                    comments.push('Android');
                } else if (detect.ios) {
                    comments.push('iOS');
                    if (detect.iphone) {
                        comments.push('iPhone');
                    } else if (detect.ipad) {
                        comments.push('iPad');
                    } else if (detect.ipod) {
                        comments.push('iPod');
                    }
                } else if (detect.blackberry) {
                    comments.push('BlackBerry');
                } else if (detect.firefoxos) {
                    comments.push('Firefox OS');
                } else if (detect.webos) {
                    comments.push('webOS');
                } else if (detect.bada) {
                    comments.push('Bada');
                } else if (detect.tizen) {
                    comments.push('Tizen');
                } else if (detect.sailfish) {
                    comments.push('Sailfish OS');
                }

                if (detect.name) {
                    comments.push(detect.name);
                }

                return $input.attr('data-version') + ' (' + comments.join(', ') + ')';
            })(window.bowser._detect(window.navigator.userAgent || '')));
        };

        // 表示時に（必要であれば）通信をして画面要素を更新する
        $modal.on('show.bs.modal', function (event) {
            if (!initialized) {
                refresh();
                updateAgentVersion();
            }
            updateUuidRegular();
        });

        // ステージボタンがクリックされた時、電文用の <input type="hidden"> を更新する
        // また、class を変更して選択されているかのように見せる
        $buttonStages.click(function () {
            var $this = $(this);
            var $input = $('input', $modal).filter(function () { return $(this).attr('id') === $this.attr('data-target'); });
            $input.val($this.attr('data-value')).change();

            $buttonStages
                .filter(function () { return $this.attr('data-target') === $(this).attr('data-target'); })
                .removeClass('btn-success')
                .addClass('btn-default');
            $this
                .removeClass('btn-default')
                .addClass('btn-success');
        });

        // 勝ち/負けボタンがクリックされた時、電文用の <input type="hidden"> を更新する
        // また、class を変更して選択されているかのように見せる
        $buttonResults.click(function () {
            var $this = $(this);
            var $input = $('input', $modal).filter(function () { return $(this).attr('id') === $this.attr('data-target'); });
            $input.val($this.attr('data-value')).change();

            $buttonResults
                .filter(function () { return $this.attr('data-target') === $(this).attr('data-target'); })
                .removeClass('btn-info')
                .removeClass('btn-danger')
                .addClass('btn-default');
            $this
                .removeClass('btn-default')
                .addClass($this.attr('data-value') === 'win' ? 'btn-info' : 'btn-danger');
        });

        // ディスプレイ接続モード変更時に対応するコントローラに更新する
        $selectDisplay.change(function () {
            var $this = $(this);
            var lastController = $selectController.val();
            var enabledControllers = JSON.parse($('option:selected', $this).attr('data-controllers'));
            if (enabledControllers && $.inArray(lastController, enabledControllers) < 0) {
                $selectController.val('');
            }
            $('option', $selectController).each(function () {
                var $option = $(this);
                if (!enabledControllers || $option.val() == '' || $.inArray($option.val(), enabledControllers) >= 0) {
                    $option.prop('disabled', false);
                } else {
                    $option.prop('disabled', true);
                }
            });
        });

        // レギュラーバトルの送信ボタン押下処理
        $regularSubmit.click(function () {
            var $this = $(this);
            var $form = $('#' + $this.attr('data-form') + ' form');
            if (!$form.length) {
                return;
            }
            $this.prop('disabled', true);
            $.ajax('/api/v1/battle', {
                'method': 'POST',
                'data': JSON.stringify(serializeForm($form)),
                'contentType': 'application/json',
                'processData': false,
                'dataType': 'json',
                'success': function (json) {
                    var clear = [
                        'battle-input-form--regular--death',
                        'battle-input-form--regular--kill',
                        'battle-input-form--regular--point',
                        'battle-input-form--regular--result',
                        'battle-input-form--regular--stage',
                    ];
                    $.each(clear, function (i, id) {
                        $('#' + id).val('');
                    });
                    $buttonStages
                        .filter('[data-target="battle-input-form--regular--stage"]')
                        .removeClass('btn-success')
                        .addClass('btn-default');
                    $buttonResults
                        .filter('[data-target="battle-input-form--regular--result"]')
                        .removeClass('btn-info')
                        .removeClass('btn-danger')
                        .addClass('btn-default');
                    $this.prop('disabled', false);
                },
                'error': function () {
                    alert('Could not create a new battle record.');
                    $this.prop('disabled', false);
                },
                'complete': function () {
                    updateUuidRegular();
                },
            });
        });

        // 変更を検知して送信ボタンの状態を切り替える
        (function () {
            // 変更即反映できる方々
            var idList = [
                '#battle-input-form--regular--rule',
                '#battle-input-form--regular--lobby',
                '#battle-input-form--regular--display',
                '#battle-input-form--regular--controller',
                '#battle-input-form--regular--weapon',
                '#battle-input-form--regular--stage',
                '#battle-input-form--regular--result',
            ];
            $(idList.join(',')).change(function () {
                $regularSubmit.prop('disabled', !validateRegular());
            });

            // ユーザ入力のためにキー入力をベースにする方々
            var timerId;
            idList = [
                '#battle-input-form--regular--point',
                '#battle-input-form--regular--kill',
                '#battle-input-form--regular--death',
            ];
            $(idList.join(',')).keydown(function () {
                if (timerId) {
                    window.clearTimeout(timerId);
                }
                timerId = window.setTimeout(function () {
                    $regularSubmit.prop('disabled', !validateRegular());
                }, 50);
            });
        })();

        // ナビゲーションバーの登録ボタン
        if ($modal.length) {
            $('#battle-input-btn')
                .prop('disabled', false)
                .click(function() {
                    $modal.modal();
                });
        }
    });
})(jQuery);
