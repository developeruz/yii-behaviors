Полезные поведения для Yii2
=============

###Установка:###

php composer.phar require developeruz/yii-behaviors "*"

DateTimeBehavior
-------------

Часто в проектах бывает нужно отобразить дату, хранимую в БД как datetime в привычном для пользователя формате.
Данное поведение конвертирует дату в заданный формат после получения модели из БД, и возвращет его в правильный для БД формат перед валидацией

Пример использования в моделе:

    use developeruz\behaviors\DateTimeBehavior;

    public function behaviors()
    {
        return  [
            'dateTimeStampBehavior' => [
                'class' => DateTimeBehavior::className(),
                'dateTimeFields' => 'date_create', //атрибут модели, который будем менять
                'format'         => 'd/m/Y H:i',   //формат вывода даты для пользователя
            ]
        ]
    }


SimpleFormBehavior
-------------

Поведение генерирует форму для модели и сохраняет ее в переменной класса модели

Пример использования в моделе:

    use developeruz\behaviors\SimpleFormBehavior;

    public $form;

    public function behaviors()
    {
        return  [
           'SimpleFormBehavior' => [
                'class'    => SimpleFormBehavior::className(),
                'resultFormField' => 'form', // поле в модели, в котором хранится готовая форма
                'scenario'        => 'test', // сценарий, порядок полей в форме будет соотвествовать порядку перечисления полей в сценарии
                'config' =>
                         [
                            SimpleFormBehavior::TypeHidden   => ['parent_id'],
                            SimpleFormBehavior::TypeBoolean  => ['moderation'],
                         ],
                'formOption' => [ 'enctype'=> 'multipart/form-data'],
                'widget' => [
                         'content' => [
                             'class'  => TinyMce::className(),
                             'config' => [
                                    'options' => ['rows' => 6],
                                    'language' => 'ru',
                             ]
                         ]
           ],
        ];
    }

После привязки поведения к модели, в представлении можно использовать переменную $form для вывода готовой формы

    <?=$model->form;?>

В config указываются нестандартные типы полей (password, hidden, file и тп). Типы "строка" или "текст" указывать не обязательно.
Так же есть возможность добавить виджет к любому из полей формы.

