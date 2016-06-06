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
                'Delete',
                new CRUDTableWidgetDelete()
            ),
        ],
        [],
        'title'
    );

