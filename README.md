Библиотека умеет делать две вещи: выводить список объектов и выводить форму редактирования одного объекта. При этом она генерирует не готовую страницу, а только html-код таблицы или формы. Выходной html-код совместим с twitter bootstrap.

Все остальное - проверка прав доступа, вывод таблицы или формы в нужное место страницы - нужно сделать отдельно. Для этого можно использовать готовые модули:
- php-auth: авторизация пользователей и проверка разрешений
- php-bt: содержит готовые шаблоны страниц

Объекты загружаются из БД через методы \OLOG\Model\InterfaceFactory, соответственно класс объекта должен реализовать этот интерфейс.

Объекты сохраняются в БД через методы \OLOG\Model\InterfaceSave, соответственно для редактирования класс объекта должен реализовать этот интерфейс.

# Установка демо проекта

В папке, где мы хотим развернуть демо проект, выполняем в консоли следующие команды:

    git clone https://github.com/o-log/php-crud.git
    cd php-crud
    composer update
    
После этого нужно создать пустую БД для проекта и указаты параметры доступа к ней в файле CRUDDemo/Config.php

Теперь создаем таблицы в БД, для этого выполняем скрипт миграции структуры БД:

    php cli.php
    
Выбираем пункт 1, затем отвечаем на вопросы скрипта.

После создания таблиц запускаем локальный сервер и проверяем работу:

    ./run.sh
    
Открываем в браузере адрес localhost:8000

# Подключение библиотеки к проекту

Включаем в composer.json проект такие строки:

	"require" : {
		"o-log/php-crud" : "dev-master"
    }

# Вывод списка объектов

Вот пример кода, который генерирует html таблицы объектов:

    $html .= CRUDTable::html(
        DemoNode::class,
        \OLOG\CRUD\CRUDForm::html(
            new DemoNode(),
            [
                new CRUDFormRow(
                    'Title',
                    new CRUDFormWidgetInput('title')
                )
            ]
        ),
        [
            new CRUDTableColumn(
                'Edit',
                new CRUDTableWidgetText('{this->title}')
            ),
            new CRUDTableColumn(
                'Reverse title',
                new CRUDTableWidgetText('{this->getReverseTitle()}')
            ),
            new CRUDTableColumn(
                'Edit',
                new CRUDTableWidgetTextWithLink(
                    '{this->title}',
                    DemoNodeEditAction::getUrl('{this->id}')
                )
            ),
            new CRUDTableColumn(
                'Weight',
                new CRUDTableWidgetWeight(
                    [
                        'parent_id' => '{this->parent_id}'
                    ]
                )
            ),
            new CRUDTableColumn(
                'Delete',
                new CRUDTableWidgetDelete()
            ),
        ],
        [],
        'title'
    );

## Вывод данных в таблице

При вызове виджета таблицы мы передаем ему строку: что выводить. В этой строке можно обращаться к поля и методам объекта, который выводится в текущей строке таблицы, а также к другим объектам.

Объект, для которого выводится текущая строка таблицы, доступен через ключевое слово this.

Примеры вывода полей и вызова методов текущего объекта:

new CRUDTableWidgetText('{this->title}')
new CRUDTableWidgetText('{this->getReverseTitle()}')
new CRUDTableWidgetHtml('{this->id}<br/>{this->title}')

К другим объектам, на которые ссылается текущий, можно обратиться следующим образом:

new CRUDTableWidgetText('{DemoTerm.{this->term_id}->title}')

Здесь мы выводим название рубрики, связанной с новостью: создаем объект класса DemoTerm с идентификатором, который берем из поля term_id текущего объекта, и выводит его название.

На практике для указания имени класса вместо скаляра стоит использовать автоматические константы, поэтому окончательная запись будет выглядеть так:

new CRUDTableWidgetText('{' . DemoTerm::class . '.{this->term_id}->title}')

В виджете весов иногда необходимо учитывать дополнительные параметры по котороым вычисляются веса объектов
 в таблице. Для этого мы передаем массив контекстов

Например

new CRUDTableWidgetWeight([ 'parent_id' => '{this->parent_id}' ]) 

В этом случае при обработки изменения весов веса будут считаться для записей с parent_id  таким же как и у текущего объекта 
     
 
# Вывод редактора объекта

Вот пример кода, который генерирует html редактора объекта:

        $node_obj = DemoNode::factory($node_id);

        $html .= \OLOG\CRUD\CRUDForm::html(
            $node_obj,
            [
                new CRUDFormRow(
                    'Id',
                    new CRUDFormWidgetInput('id')
                ),
                new CRUDFormRow(
                    'Title',
                    new CRUDFormWidgetTextarea('title')
                ),
                new CRUDFormRow(
                    'image_path_in_images nullable',
                    new CRUDFormWidgetInput('image_path_in_images', true)
                ),
                new CRUDFormRow(
                    'Date',
                    new CRUDFormWidgetTimestamp('created_at_ts')
                ),
                new CRUDFormRow(
                    'is_published',
                    new CRUDFormWidgetRadios('is_published', [0 => 'no', 1 => 'yes'])
                ),
                new CRUDFormRow(
                    'published_at_datetime_str',
                    new CRUDFormWidgetDateTime('published_at_datetime_str')
                ),
                new CRUDFormRow(
                    'expiration_date nullable',
                    new CRUDFormWidgetDate('expiration_date')
                ),
                new CRUDFormRow(
                    'State code',
                    new CRUDFormWidgetOptions('state_code',
                        [
                            1 => 'announce',
                            2 => 'live',
                            3 => 'archive'
                        ]
                    )
                ),
                new CRUDFormRow(
                    'State code',
                    new CRUDFormWidgetHtml('<ul><li>html widget - line 1</li><li>html widget - line 2</li></ul>')
                ),
                new CRUDFormVerticalRow(
                    'Body',
                    new CRUDFormWidgetAceTextarea('body')
                )
            ]
        );


