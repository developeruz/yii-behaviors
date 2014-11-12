<?php
namespace developeruz\behaviors;

use Yii;
use yii\behaviors\AttributeBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Html;

class SimpleFormBehavior extends AttributeBehavior
{

    public $scenario, $resultFormField, $formOption;
    public $formMethod = 'post';
    public $formAction = '';
    public $config = [
        self::TypePassword => [],
        self::TypeHidden => [],
        self::TypeFile => [],
        self::TypeBoolean => [],
        self::TypeDropDown => []

    ];
    public $widget = [];

    const TypeString = 'string';
    const TypeText = 'text';
    const TypeHidden = 'hidden';
    const TypePassword = 'password';
    const TypeFile = 'file';
    const TypeBoolean = 'boolean';
    const TypeDropDown = 'dropDown';

    private $form, $schema;

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_FIND => 'generateForm',
            ActiveRecord::EVENT_INIT => 'generateForm',
            ActiveRecord::EVENT_AFTER_VALIDATE => 'generateForm',
        ];
    }

    public function generateForm()
    {
        $formFields = array();

        $this->owner->setScenario($this->scenario);
        $this->form = Yii::createObject('developeruz\behaviors\components\DevActiveForm');
        $this->schema = $this->owner->getTableSchema();

        foreach ($this->owner->activeAttributes() as $field) {
            $formFields[] = $this->renderField($field);
        }

        $result = $this->form->beginForm($this->formAction, $this->formMethod, $this->formOption);
        $result .= implode(' ', $formFields);
        $result .= $this->form->endForm();

        $this->owner->{$this->resultFormField} = $result;
    }

    private function renderField($fieldName)
    {
        $type = $this->checkCustomType($fieldName);
        if (!$type) {
            $type = $this->schema->columns[$fieldName]->type;
        }

        $field = $this->form->field($this->owner, $fieldName);

        switch ($type) {
            case self::TypeHidden:
                $input = $field->hiddenInput();
                break;
            case self::TypePassword:
                $input = $field->passwordInput();
                break;
            case self::TypeFile:
                $input = $field->fileInput();
                break;
            case self::TypeBoolean:
                $input = $field->checkbox();
                break;
            case self::TypeDropDown:
                $input = $field->dropDownList($this->config['dropDown'][$fieldName]);
                break;
            case self::TypeText:
                $input = $field->textarea();
                break;
            default:
                $input = $field->textInput();
        }

        if (array_key_exists($fieldName, $this->widget)) {
            $input->widget($this->widget[$fieldName]['class'], $this->widget[$fieldName]['config']);
        }

        if ($type != self::TypeHidden) {
            $label = ($type == self::TypeBoolean) ? '' : Html::activeLabel($this->owner, $fieldName);
            $error = implode(', ', $this->owner->getErrors($fieldName));
            return "<div class='field {$type}'>
                    {$label}
                    {$input->parts['{input}']}
                    {$error}
                    </div>";
        } else {
            return $input->parts['{input}'];
        }
    }

    private function checkCustomType($fieldName)
    {
        foreach ($this->config as $type => $array) {
            if (in_array($fieldName, $array) || array_key_exists($fieldName, $array)) {
                return $type;
            }
        }
        return false;
    }
}