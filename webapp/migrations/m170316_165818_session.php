<?php
use yii\db\Migration;

class m170316_165818_session extends Migration
{
    public function up()
    {
        $this->createTable('session', [
            'id' => 'CHAR(40) NOT NULL PRIMARY KEY',
            'expire' => 'INTEGER',
            'data' => 'BYTEA',
        ]);
    }

    public function down()
    {
        $this->dropTable('session');
    }
}
