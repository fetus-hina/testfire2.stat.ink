<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\user;

use Yii;
use yii\web\ViewAction as BaseAction;

class ProfileAction extends BaseAction
{
    public function run()
    {
        $ident = Yii::$app->user->getIdentity();
        return $this->controller->render('profile.tpl', [
            'user' => $ident,
        ]);
    }
}
