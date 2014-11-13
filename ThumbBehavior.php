<?php
namespace developeruz\behaviors;

/**
 * ThumbBehavior for Yii2
 *
 * @author Elle <elleuz@gmail.com>
 * @version 0.1
 * @package Behaviors for Yii2
 *
 */

use Yii;
use yii\behaviors\AttributeBehavior;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;
use yii\imagine\Image;

class ThumbBehavior extends AttributeBehavior
{

    public $fileAttribute = 'image';
    public $saveDir = '/../frontend/web/upload/';
    public $previewSize = [[50, 50], [150, 150]];

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'addImages',
            ActiveRecord::EVENT_BEFORE_DELETE => 'deleteImages'
        ];
    }

    public function addImages()
    {
        $image = UploadedFile::getInstance($this->owner, $this->fileAttribute);
        if (!empty($image)) {
            $uniqName = Yii::$app->security->generateRandomString() . '.' . $image->getExtension();
            if ($image->saveAs(Yii::$app->basePath . $this->saveDir . $uniqName)) {
                $this->owner->{$this->fileAttribute} = $uniqName;
                $this->makePreview($uniqName);
            }
        }
    }

    public function deleteImages()
    {
        if (file_exists(Yii::$app->basePath . $this->saveDir . $this->owner->{$this->fileAttribute})) {
            unlink(Yii::$app->basePath . $this->saveDir . $this->owner->{$this->fileAttribute});
        }
        $this->removePreview($this->owner->{$this->fileAttribute});
    }

    public function getImage()
    {
        return $this->owner->{$this->fileAttribute};
    }

    public function getPreview($size)
    {
        if(file_exists(Yii::$app->basePath.$this->saveDir.$size[0].'x'.$size[1].$this->owner->{$this->fileAttribute}))
            return $size[0].'x'.$size[1].$this->owner->{$this->fileAttribute};
        else return false;
    }

    protected function makePreview($file)
    {
        foreach ($this->previewSize as $size) {
            Image::thumbnail(Yii::$app->basePath . $this->saveDir . $file, $size[0], $size[1])
                ->save(Yii::$app->basePath . $this->saveDir . $size[0] . 'x' . $size[1] . $file);
        }
    }

    protected function removePreview($file)
    {
        foreach ($this->previewSize as $size) {
            if (file_exists(Yii::$app->basePath . $this->saveDir . $size[0] . 'x' . $size[1] . $file)) {
                unlink(Yii::$app->basePath . $this->saveDir . $size[0] . 'x' . $size[1] . $file);
            }
        }
    }
}