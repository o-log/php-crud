<?php

namespace CRUDDemo;

use \OLOG\CRUD\CRUDElements;

class TermCrudController
{
    static public function termEditAction($_mode, $term_id = '(\d+)'){
        if ($_mode == \OLOG\Router::GET_URL) return '/admin/term/' . $term_id;
        if ($_mode == \OLOG\Router::GET_METHOD) return __METHOD__;

        //

        \OLOG\Exits::exit403If(!Auth::currentUserHasAnyOfPermissions([1]));

        $html = '';

        ob_start();
        \OLOG\CRUD\CRUDElements::renderEditorForm(
            Term::class, $term_id,
            [
                'ELEMENTS' => [
                    [
                        CRUDElements::KEY_ELEMENT_TYPE => \OLOG\CRUD\CRUDElements::ELEMENT_FORM_ROW,
                        CRUDElements::KEY_FORM_ROW_FIELD_NAME => 'title',
                        CRUDElements::KEY_FORM_ROW_TITLE => 'Название',
                        'WIDGET' => [
                            'WIDGET_TYPE' => 'WIDGET_TEXTAREA'
                        ]
                    ],
                    [
                        CRUDElements::KEY_ELEMENT_TYPE => \OLOG\CRUD\CRUDElements::ELEMENT_FORM_ROW,
                        CRUDElements::KEY_FORM_ROW_FIELD_NAME => 'id',
                        'WIDGET' => [
                            'WIDGET_TYPE' => 'WIDGET_INPUT'
                        ]
                    ],
                    [
                        CRUDElements::KEY_ELEMENT_TYPE => \OLOG\CRUD\CRUDElements::ELEMENT_FORM_ROW,
                        CRUDElements::KEY_FORM_ROW_FIELD_NAME => 'parent_id',
                        'WIDGET' => [
                            'WIDGET_TYPE' => 'WIDGET_REFERENCE'
                        ]
                    ]
                ]
            ]
        );
        $html .= ob_get_clean();

        $html .= '<h2>Child terms</h2>';

        ob_start();
        \OLOG\CRUD\CRUDListTemplate::render(
            Term::class,
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
                        ],
                        [
                        CRUDElements::KEY_ELEMENT_TYPE => \OLOG\CRUD\CRUDElements::ELEMENT_FORM_ROW,
                        CRUDElements::KEY_FORM_ROW_FIELD_NAME => 'parent_id',
                        CRUDElements::KEY_FORM_ROW_TITLE => 'parent',
                        'WIDGET' => [
                            'WIDGET_TYPE' => 'WIDGET_INPUT'
                        ]
                    ]
                    ]
                ],
                \OLOG\CRUD\CRUDListTemplate::KEY_LIST_COLUMNS => [
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
                            'LINK_URL' => TermCrudController::termEditAction(\OLOG\Router::GET_URL, '{this->id}'),
                            'TEXT' => '{this->title}'
                        ]
                    ],
                    [
                        'COLUMN_TITLE' => 'parent',
                        'WIDGET' => [
                            'WIDGET_TYPE' => 'TEXT',
                            'TEXT' => '{\CRUDDemo\Term.{this->parent_id}->title}'
                        ]
                    ]
                ]
            ],
            [
                'parent_id' => $term_id
            ]
        );
        $html .= ob_get_clean();

        LayoutTemplate::render($html, '<a href="' . self::termsListAction(\OLOG\Router::GET_URL) . '">Terms</a> / Term ' . $term_id);
    }

    static public function termsListAction($_mode){
        if ($_mode == \OLOG\Router::GET_URL) return '/admin/terms';
        if ($_mode == \OLOG\Router::GET_METHOD) return __METHOD__;

        \OLOG\Exits::exit403If(!Auth::currentUserHasAnyOfPermissions([1]));

        ob_start();
        \OLOG\CRUD\CRUDListTemplate::render(
            \CRUDDemo\Term::class,
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
                \OLOG\CRUD\CRUDListTemplate::KEY_LIST_COLUMNS => [
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
                            'LINK_URL' => TermCrudController::termEditAction(\OLOG\Router::GET_URL, '{this->id}'),
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
            ]
            );

        $html = ob_get_clean();

        LayoutTemplate::render($html, 'Термы');
    }

}