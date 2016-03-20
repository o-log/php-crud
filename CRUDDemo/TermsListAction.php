<?php

namespace CRUDDemo;

use OLOG\CRUD\CRUDEditorForm;
use OLOG\CRUD\CRUDFormRow;
use OLOG\CRUD\CRUDWidgetTextarea;

class TermsListAction
{
    static public function getUrl()
    {
        return '/terms';
    }

    static public function action()
    {
        \OLOG\Exits::exit403If(!Auth::currentUserHasAnyOfPermissions([1]));

        ob_start();
        \OLOG\CRUD\CRUDList::render(
            \CRUDDemo\Term::class,
            CRUDEditorForm::html(
                Term::class,
                null,
                [
                    CRUDFormRow::html(
                        CRUDWidgetTextarea::html('title'),
                        'Title'
                    )
                ]
            ),
            [
                [
                    'COLUMN_TITLE' => 'id',
                    'WIDGET' => [
                        'WIDGET_TYPE' => 'TEXT',
                        'TEXT' => '{this->id}'
                    ]
                ],
                [
                    'COLUMN_TITLE' => 'title',
                    'WIDGET' => [
                        'WIDGET_TYPE' => \OLOG\CRUD\CRUDWidgets::WIDGET_TEXT_WITH_LINK,
                        'LINK_URL' => TermEditAction::getUrl('{this->id}'),
                        'TEXT' => '{this->title}'
                    ]
                ],
                [
                    'COLUMN_TITLE' => 'parent',
                    'WIDGET' => [
                        'WIDGET_TYPE' => 'TEXT',
                        'TEXT' => '{\CRUDDemo\Term.{this->parent_id}->title}'
                    ]
                ],
                [
                    'COLUMN_TITLE' => 'delete',
                    'WIDGET' => [
                        'WIDGET_TYPE' => 'DELETE',
                        'TEXT' => 'X'
                    ]
                ]
            ]
        );

        $html = ob_get_clean();

        LayoutTemplate::render($html, 'Термы', self::breadcrumbsArr());
    }
    
    static public function breadcrumbsArr(){
        return array_merge(MainPageAction::breadcrumbsArr(), [BT::a(self::getUrl(), 'Terms')]);
    } 

}