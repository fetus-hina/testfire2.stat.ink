<?php
/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;

/**
 * This is the model class for table "language_charset".
 *
 * @property integer $language_id
 * @property integer $charset_id
 * @property boolean $is_win_acp
 *
 * @property Charset $charset
 * @property Language $language
 */
class LanguageCharset extends \yii\db\ActiveRecord
{
    public static function find()
    {
        return parent::find()
            ->innerJoinWith('charset')
            ->orderBy('{{charset}}.[[order]] ASC');
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'language_charset';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['language_id', 'charset_id', 'is_win_acp'], 'required'],
            [['language_id', 'charset_id'], 'integer'],
            [['is_win_acp'], 'boolean'],
            [['charset_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Charset::class,
                'targetAttribute' => ['charset_id' => 'id']],
            [['language_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Language::class,
                'targetAttribute' => ['language_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'language_id' => 'Language ID',
            'charset_id' => 'Charset ID',
            'is_win_acp' => 'Is Win Acp',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCharset()
    {
        return $this->hasOne(Charset::class, ['id' => 'charset_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguage()
    {
        return $this->hasOne(Language::class, ['id' => 'language_id']);
    }
}
