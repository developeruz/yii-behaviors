<?php
namespace elle\behaviors;

use yii\behaviors\AttributeBehavior;
use yii\db\ActiveRecord;

class DateTimeBehavior extends AttributeBehavior {
    public $dateTimeFields;
    public $format = 'd-m-Y H:i:s';

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_FIND      => 'convertDate',
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'convertDateToDB',
        ];
    }

    public function convertDate()
    {
        $this->owner->{$this->dateTimeFields} = date($this->owner->{$this->dateTimeFields}, $this->format);
    }

    public function convertDateToDB()
    {
        $this->owner->{$this->dateTimeFields} = date_create_from_format($this->format, $this->owner->{$this->dateTimeFields})->format('Y-m-d H:i:s');
    }
}