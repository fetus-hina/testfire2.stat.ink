<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\commands;

use Yii;
use Zend\Feed\Reader\Reader as FeedReader;
use Zend\Validator\Uri as UriValidator;
use jp3cki\uuid\NS as UuidNS;
use jp3cki\uuid\Uuid;
use yii\console\Controller;
use app\models\BlogEntry;

class BlogFeedController extends Controller
{
    public function actionCrawl()
    {
        $transaction = Yii::$app->db->beginTransaction();
        $entries = $this->fetchFeed();
        foreach ($entries as $entry) {
            $this->processEntry($entry);
        }
        $transaction->commit();
    }

    private function fetchFeed()
    {
        echo "Fetching feed...\n";
        $feed = FeedReader::import('https://blog.fetus.jp/category/website/stat-ink/feed');
        echo "done.\n";
        $ret = [];
        foreach ($feed as $entry) {
            if (!$entry->getDateCreated()) {
                continue;
            }
            $ret[] = $entry;
        }
        usort($ret, function ($a, $b) {
            return $a->getDateCreated()->getTimestamp() <=> $b->getDateCreated()->getTimestamp();
        });
        return $ret;
    }

    private function processEntry($entry)
    {
        $id = $entry->getId() ?? $entry->getLink() ?? false;
        if (!$id) {
            return;
        }
        $uuid = Uuid::v5(
            (new UriValidator())->isValid($id)
                ? UuidNs::url()
                : 'd0ec81fc-c8e6-11e5-a890-9ca3ba01e1f8',
            $id
        )->__toString();
        $link = $entry->getLink();
        if (!(new UriValidator())->isValid($link)) {
            return;
        }

        if (BlogEntry::find()->andWhere(['uuid' => $uuid])->count()) {
            return;
        }

        $model = new BlogEntry();
        $model->attributes = [
            'uuid'  => $uuid,
            'url'   => $link,
            'title' => $entry->getTitle(),
            'at'    => $entry->getDateCreated()->format('Y-m-d\TH:i:sP'),
        ];
        if (!$model->save()) {
            echo "Could not create new blog entry\n";
            var_dump($model->attributes);
            var_dump($model->getErrors());
            throw new \Exception('Could not create new blog entry');
        }
        echo "Registered new blog entry\n";
        printf("  #%d, %s %s\n", $model->id, $model->url, $model->title);
    }
}
