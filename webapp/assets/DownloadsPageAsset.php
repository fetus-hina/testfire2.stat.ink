<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\assets;

use yii\web\AssetBundle;

class DownloadsPageAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/stat.ink';
    public $css = [
        'downloads.css',
    ];
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
        AppAsset::class,
    ];
}
