<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\assets;

use yii\web\AssetBundle;

class SlackAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/slack';
    public $js = [
        'slack.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        BootstrapToggleAsset::class,
    ];
}
