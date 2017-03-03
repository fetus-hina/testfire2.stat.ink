<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components\validators;

use yii\validators\FilterValidator;

class IdnToPunycodeFilterValidator extends FilterValidator
{
    public function init()
    {
        $this->filter = function ($value) {
            if (strpos($value, '/') === false) {
                return strtolower(idn_to_ascii($value));
            }
            if (strpos($value, '//') !== false) {
                return preg_replace_callback(
                    '!(?<=//)([^/:]+)!',
                    function ($match) {
                        return strtolower(idn_to_ascii($match[1]));
                    },
                    $value,
                    1
                );
            }
            return preg_replace_callback(
                '!^([^/:]+)!',
                function ($match) {
                    return strtolower(idn_to_ascii($match[1]));
                },
                $value,
                1
            );
        };
        parent::init();
    }
}
