<?php

namespace CRUDDemo;

use OLOG\CRUD\CRUDFormRow;
use OLOG\CRUD\CRUDWidgetReference;
use OLOG\CRUD\CRUDWidgetTextarea;

class TermEditAction
{
    static public function getUrl($term_id = '(\d+)'){
        return '/admin/term/' . $term_id;
    }
    
    static public function action($term_id){
        \OLOG\Exits::exit403If(!Auth::currentUserHasAnyOfPermissions([1]));

        $html = '';

        $term_obj = Term::factory($term_id);
        
        $html .= \OLOG\CRUD\CRUDEditorForm::html(
            Term::class,
            $term_id,
            [
                CRUDFormRow::html(
                    CRUDWidgetTextarea::html('title', $term_obj->getTitle()),
                    'Title'
                ),
                CRUDFormRow::html(
                    CRUDWidgetReference::html('parent_id', $term_obj->getParentId(), Term::class, 'title'),
                    'Parent id'
                )
            ]
        );

        $html .= '<h2>Child terms</h2>';

        $context_obj = new \stdClass();
        $context_obj->parent_id = $term_id;

        ob_start();
        \OLOG\CRUD\CRUDList::render(
            \CRUDDemo\Term::class,
            /*
                [
                    'CREATE_FORM' => [
                        'ELEMENTS' => [
                            [
                                CRUDElements::KEY_ELEMENT_TYPE => \OLOG\CRUD\CRUDElements::ELEMENT_FORM_ROW,
                                CRUDElements::KEY_FORM_ROW_FIELD_NAME => 'title',
                                CRUDElements::KEY_FORM_ROW_TITLE => 'Название',
                                'WIDGET' => [
                                    'WIDGET_TYPE' => 'WIDGET_INPUT'
                                ]
                            ]
                        ]
                    ],
            */
            '',
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
            ],
            $context_obj
        );

        $html .= ob_get_clean();

        LayoutTemplate::render($html, 'Term ' . $term_id, self::breadcrumbsArr($term_id));
    }

    static public function breadcrumbsArr($term_id){
        return array_merge(TermsListAction::breadcrumbsArr(), [BT::a(self::getUrl($term_id), 'Term ' . $term_id)]);
    }

}