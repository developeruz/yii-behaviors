Полезные поведения для Yii2
=============

###Установка:###
```bash
$ php composer.phar require developeruz/yii-behaviors "*"
```

DateTimeBehavior
-------------

Часто в проектах бывает нужно отобразить дату, хранимую в БД как `datetime` в привычном для пользователя формате.
Данное поведение конвертирует дату в заданный формат после получения модели из БД, и возвращает его в правильный для БД формат перед валидацией

Пример использования в моделе:
```php
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
```

SimpleFormBehavior
-------------

Поведение генерирует форму для модели и сохраняет ее в переменной класса модели

Пример использования в модели:
```php
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
```

После привязки поведения к модели, в представлении можно использовать переменную `$form` для вывода готовой формы
```php
<?= $model->form ?>
```
В `config` указываются нестандартные типы полей (`password`, `hidden`, `file` и т.п.). Типы "строка" или "текст" указывать не обязательно.
Также есть возможность добавить виджет к любому из полей формы.


ThumbBehavior
-------------

Поведение для загрузки изображений и генерации превью-картинок.

Подцепляем к модели:
```php
 use developeruz\behaviors\ThumbBehavior;

 public function behaviors()
    {
        return  [
            'thumbBehavior' => [
                'class' => ThumbBehavior::className(),
                'fileAttribute' => 'thumb', //атрибут модели для картинки
                'saveDir' => '/../frontend/web/upload/', //путь для сохранения картинок
                'previewSize' => [[100, 100], [250, 250]] //размеры генерируемых превью
            ],
        ];
    }
```

Теперь перед валидацией модели, поведение сохранит картинку, если она была загружена. Присвоит атрибуту модели имя сохранненного файла и сгенерирует превью для заданных размеров.
А перед удалением модели - удалит картинку и все превью.
Атрибуту модели, указанному в fileAttribute будет присвоено уникальное имя сохраненного файла.
Получить доступ к имени картинки и превью можно через методы getImage() и getPreview($size)
```php
    $post = Post::findOne(1);
    $post->getImage(); // возвращает имя картинки, без полного пути до директории сохранения
    $post->getPreview([100,100]); // возвращает имя превью задданого размера, без полного пути до директории сохранения
```
Если превью указанного размера не существует, метод getPreview возвращает false.
Важно: saveDir - путь для сохранения картинок указывается относительно корня текущего приложения Yii::$app->basePath.