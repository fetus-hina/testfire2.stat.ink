<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\api\v1;

use Yii;
use yii\web\ViewAction as BaseAction;
use app\models\Gear;
use app\models\api\v1\GearGetForm;

class GearAction extends BaseAction
{
    public function run()
    {
        $response = Yii::$app->getResponse();
        $response->format = 'json';

        $form = new GearGetForm();
        $form->attributes = Yii::$app->getRequest()->get();
        if (!$form->validate()) {
            $response->statusCode = 400;
            return [
                'error' => $form->getErrors(),
            ];
        }

        $query = Gear::find()
            ->with([
                'type',
                'ability',
                'brand',
                'brand.strength',
                'brand.weakness',
            ])
            ->orderBy('{{gear}}.[[id]]');
        $form->filterQuery($query);

        return array_map(
            function ($gear) {
                return $gear->toJsonArray();
            },
            $query->all()
        );
    }
}
