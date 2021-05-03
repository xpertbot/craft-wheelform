<?php
namespace wheelform\db;

use craft\helpers\StringHelper;
use craft\helpers\Db;
use yii\db\ActiveRecord;

class BaseActiveRecord extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        $this->prepareForDb();
        return parent::beforeSave($insert);
    }

    /**
     * Sets the `dateCreated`, `dateUpdated`, and `uid` attributes on the record.
     *
     * @since 3.1.0
     */
    protected function prepareForDb()
    {
        $now = Db::prepareDateForDb(new \DateTime());

        if ($this->getIsNewRecord()) {
            if ($this->hasAttribute('dateCreated') && (!isset($this->dateCreated) || false === $this->dateCreated)) {
                $this->dateCreated = $now;
            }

            if ($this->hasAttribute('dateUpdated') && !isset($this->dateUpdated)) {
                $this->dateUpdated = $now;
            }

            if ($this->hasAttribute('uid') && !isset($this->uid)) {
                $this->uid = StringHelper::UUID();
            }
        } else if (
            !empty($this->getDirtyAttributes()) &&
            $this->hasAttribute('dateUpdated')
        ) {
            if (!$this->isAttributeChanged('dateUpdated')) {
                $this->dateUpdated = $now;
            } else {
                $this->markAttributeDirty('dateUpdated');
            }
        }
    }
}
