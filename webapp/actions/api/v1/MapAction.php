<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\api\v1;

use Yii;
use yii\web\ViewAction as BaseAction;
use app\models\Map;

class MapAction extends BaseAction
{
    public function run()
    {
        $response = Yii::$app->getResponse();
        $response->format = 'json';
        return array_map(
            function ($map) {
                return $map->toJsonArray();
            },
            Map::find()->orderBy('{{map}}.[[id]] ASC')->all()
        );
    }
}
