<?php

namespace CRUDDemo;

use OLOG\BT;
use OLOG\CRUD\CRUDEditorForm;
use OLOG\CRUD\CRUDFormRow;
use OLOG\CRUD\CRUDTableColumn;
use OLOG\CRUD\CRUDTableWidgetDelete;
use OLOG\CRUD\CRUDTableWidgetText;
use OLOG\CRUD\CRUDTableWidgetTextWithLink;
use OLOG\CRUD\CRUDWidgetInput;
use OLOG\CRUD\CRUDEditorWidgetTextarea;

class TermsListAction
{
    static public function getUrl()
    {
        return '/terms';
    }

    static public function action()
    {
        \OLOG\Exits::exit403If(!Auth::currentUserHasAnyOfPermissions([1]));

        $html = '';

        $html .= CRUDEditorForm::html(
            new Term,
            [
                new CRUDFormRow(
                    'Title',
                    new CRUDWidgetInput('title')
                )
            ]
        );

        $html .= \OLOG\CRUD\CRUDList::html(
            \CRUDDemo\Term::class,
            [
                new CRUDTableColumn(
                    'Edit',
                    new CRUDTableWidgetTextWithLink(
                        '{this->title}',
                        TermEditAction::getUrl('{this->id}')
                        )
                ),
                new CRUDTableColumn(
                    'Edit',
                    new CRUDTableWidgetText(
                        '{\CRUDDemo\Term.{this->parent_id}->title}'
                    )
                ),
                new CRUDTableColumn(
                    'Edit',
                    new CRUDTableWidgetDelete()
                )
            ]
        );

        LayoutTemplate::render($html, 'Термы', self::breadcrumbsArr());
    }
    
    static public function breadcrumbsArr(){
        return array_merge(MainPageAction::breadcrumbsArr(), [BT::a(self::getUrl(), 'Terms')]);
    } 

}