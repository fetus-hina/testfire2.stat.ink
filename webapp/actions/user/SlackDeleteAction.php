<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\user;

use Yii;
use yii\base\DynamicModel;
use yii\web\BadRequestHttpException;
use yii\web\ViewAction as BaseAction;
use app\models\Slack;

class SlackDeleteAction extends BaseAction
{
    public function run()
    {
        $ident = Yii::$app->user->getIdentity();
        if (!$ident) {
            throw new BadRequestHttpException('Bad Request');
        }

        $req = Yii::$app->request;
        $form = DynamicModel::validateData(
            [
                'id' => $req->post('id'),
            ],
            [
                [['id'], 'exist', 'targetClass' => Slack::class, 'targetAttribute' => 'id'],
            ]
        );
        if ($form->hasErrors()) {
            throw new BadRequestHttpException('Bad Request');
        }

        $model = Slack::findOne([
            'id' => $form->id,
            'user_id' => $ident->id,
        ]);
        if (!$model) {
            throw new BadRequestHttpException('Bad Request');
        }

        $resp = Yii::$app->response;
        $resp->format = 'json';
        return [
            'result' => !!$model->delete(),
        ];
    }
}
