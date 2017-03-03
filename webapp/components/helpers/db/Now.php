<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components\helpers\db;

use DateTimeZone;
use Yii;
use yii\db\Expression;
use app\components\helpers\DateTimeFormatter;

class Now extends Expression
{
    public function __construct()
    {
        $time = isset($_SERVER['REQUEST_TIME_FLOAT'])
            ? $_SERVER['REQUEST_TIME_FLOAT']
            : microtime(true);
        $strtime = DateTimeFormatter::unixTimeToString(
            $time,
            new DateTimeZone('Etc/UTC')
        );
        parent::__construct(Yii::$app->db->quoteValue($strtime));
    }
}
