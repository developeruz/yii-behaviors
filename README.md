Полезные поведения для Yii2
=============

###Установка:###

php composer.phar require --prefer-dist developerUz/yii2-behaviors "*"

DateTimeBehavior
-------------

Часто в проектах бывает нужно отобразить дату, хранимую в БД как datetime в привычном для пользователя формате.
Данное поведение конвертирует дату в заданный формат после получения модели из БД, и возвращет его в правильный для БД формат перед валидацией

Пример использования в моделе:

    public function behaviors()
    {
        return  [
            'dateTimeStampBehavior' => [
                'class' => developerUz\behaviors\DateTimeBehavior::className(),
                'dateTimeFields' => 'date_create',
                'format'         => 'd/m/Y H:i',
            ]
        ]
    }
