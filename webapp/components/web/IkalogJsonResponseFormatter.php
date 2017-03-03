<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components\web;

use Yii;
use yii\base\Component;
use yii\web\ResponseFormatterInterface;

class IkalogJsonResponseFormatter extends Component implements ResponseFormatterInterface
{
    public function format($response)
    {
        $tmpfile = tmpfile();
        foreach ($response->data['rows'] as $row) {
            fwrite($tmpfile, $this->formatRow($row) . "\x0d\x0a");
        }
        fseek($tmpfile, 0, SEEK_SET);
        $response->content = null;
        $response->stream = $tmpfile;
    }

    protected function formatRow(array $row)
    {
        return json_encode($row, JSON_UNESCAPED_SLASHES);
    }
}
