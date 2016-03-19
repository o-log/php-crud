<?php

namespace CRUDDemo;

use OLOG\CRUD\CRUDElementFormRow;
use OLOG\CRUD\CRUDElementVerticalFormRow;
use OLOG\CRUD\CRUDElements;
use OLOG\CRUD\CRUDListTemplate;
use OLOG\CRUD\CRUDWidgetTextarea;
use OLOG\CRUD\Sanitize;

class NodeCrudController
{
    static public function nodeTermsAction($_mode, $node_id = '(\d+)')
    {
        if ($_mode == \OLOG\Router::GET_URL) return '/admin/node/' . $node_id . '/terms';
        if ($_mode == \OLOG\Router::GET_METHOD) return __METHOD__;

        //

        \OLOG\Exits::exit403If(!Auth::currentUserHasAnyOfPermissions([1]));

        $html = self::getTabsHtml($node_id);

        ob_start();
        CRUDListTemplate::render(
            TermToNode::class,
            [
                'CREATE_FORM' => [
                    'ELEMENTS' => [
                        [
                            CRUDElements::KEY_ELEMENT_CLASS => \OLOG\CRUD\CRUDElementFormRow::class,
                            CRUDElementFormRow::KEY_FORM_ROW_FIELD_NAME => 'node_id',
                            'WIDGET' => [
                                'WIDGET_TYPE' => 'WIDGET_INPUT'
                            ]
                        ],
                        [
                            CRUDElements::KEY_ELEMENT_CLASS => \OLOG\CRUD\CRUDElementFormRow::class,
                            CRUDElementFormRow::KEY_FORM_ROW_FIELD_NAME => 'term_id',
                            'WIDGET' => [
                                'WIDGET_TYPE' => 'WIDGET_REFERENCE',
                                'REFERENCED_CLASS' => \CRUDDemo\Term::class,
                                'REFERENCED_CLASS_TITLE_FIELD' => 'title'
                            ]
                        ]
                    ]
                ],
                CRUDListTemplate::KEY_LIST_COLUMNS => [
                    [
                        'COLUMN_TITLE' => 'node',
                        'WIDGET' => [
                            'WIDGET_TYPE' => 'TEXT',
                            'TEXT' => '{\CRUDDemo\Node.{this->node_id}->title}'
                        ]
                    ],
                    [
                        'COLUMN_TITLE' => 'term',
                        'WIDGET' => [
                            'WIDGET_TYPE' => 'TEXT',
                            'TEXT' => '{\CRUDDemo\Term.{this->term_id}->title}'
                        ]
                    ]
                ]
            ],
            [
                'node_id' => $node_id
            ]
        );
        $html .= ob_get_clean();

        LayoutTemplate::render($html, '<a href="' . self::nodesListAction(\OLOG\Router::GET_URL) . '">Nodes</a> / Node ' . $node_id);
    }

    static public function nodeEditAction($_mode, $node_id = '(\d+)')
    {
        if ($_mode == \OLOG\Router::GET_URL) return '/admin/node/' . $node_id;
        if ($_mode == \OLOG\Router::GET_METHOD) return __METHOD__;

        //

        \OLOG\Exits::exit403If(!Auth::currentUserHasAnyOfPermissions([1]));

        $html = self::getTabsHtml($node_id);

        $node_obj = Node::factory($node_id);

        $html .= \OLOG\CRUD\CRUDEditorForm::getHtml(
            Node::class, $node_id,
            [
                CRUDElementFormRow::getHtml(
                    CRUDWidgetTextarea::getHtml('id', $node_obj->getId()),
                    'Id'
                ),
                CRUDElementFormRow::getHtml(
                    CRUDWidgetTextarea::getHtml('title', $node_obj->getTitle()),
                    'Title'
                ),
                CRUDElementFormRow::getHtml(
                    CRUDWidgetTextarea::getHtml('state_code', $node_obj->getStateCode()),
                    'State code'
                ),
                /*
                ],
                [
                    CRUDElements::KEY_ELEMENT_CLASS => CRUDElementVerticalFormRow::class,
                    CRUDElementVerticalFormRow::KEY_FORM_ROW_FIELD_NAME => 'body',
                    CRUDElementVerticalFormRow::KEY_FORM_ROW_TITLE => 'Текст',
                    'WIDGET' => [
                        'WIDGET_TYPE' => 'WIDGET_ACE_TEXTAREA'
                    ]
                ]*/
            ]
        );

        LayoutTemplate::render($html, '<a href="' . self::nodesListAction(\OLOG\Router::GET_URL) . '">Nodes</a> / Node ' . $node_id);
    }

    static public function nodesListAction($_mode)
    {
        if ($_mode == \OLOG\Router::GET_URL) return '/admin/nodes';
        if ($_mode == \OLOG\Router::GET_METHOD) return __METHOD__;

        //

        \OLOG\Exits::exit403If(!Auth::currentUserHasAnyOfPermissions([1]));

        ob_start();
        CRUDListTemplate::render(
            Node::class,
            [
                'CREATE_FORM' => [
                    'ELEMENTS' => [
                        [
                            CRUDElements::KEY_ELEMENT_CLASS => CRUDElementFormRow::class,
                            CRUDElementFormRow::KEY_FORM_ROW_FIELD_NAME => 'title',
                            CRUDElementFormRow::KEY_FORM_ROW_TITLE => 'Название',
                            'WIDGET' => [
                                'WIDGET_TYPE' => 'WIDGET_INPUT'
                            ]
                        ]
                    ]
                ],
                CRUDListTemplate::KEY_LIST_COLUMNS => [
                    [
                        'COLUMN_TITLE' => 'Edit',
                        'WIDGET' => [
                            'WIDGET_TYPE' => 'TEXT',
                            'TEXT' => '{this->title}'
                        ]
                    ],
                    [
                        'COLUMN_TITLE' => 'Edit',
                        'WIDGET' => [
                            'WIDGET_TYPE' => \OLOG\CRUD\CRUDWidgets::WIDGET_TEXT_WITH_LINK,
                            'LINK_URL' => NodeCrudController::nodeEditAction(\OLOG\Router::GET_URL, '{this->id}'),
                            'TEXT' => '{\CRUDDemo\Node.{this->id}->title}'
                        ]
                    ]
                ]
            ]
        );
        $html = ob_get_clean();

        LayoutTemplate::render($html, 'Nodes');
    }

    static public function renderTabs(array $tabs_arr)
    {
        echo '<ul class="nav nav-tabs">';

        foreach ($tabs_arr as $tab_arr) {
            $classes = '';

            // TODO: код взят из Router::match3() - использовать общую реализацию?

            $url_regexp = '@^' . $tab_arr['MATCH_URL'] . '$@';
            $matches_arr = array();
            $current_url = \OLOG\Router::uri_no_getform();
            if (preg_match($url_regexp, $current_url, $matches_arr)) {
                $classes .= ' active ';
            }

            echo '<li role="presentation" class="' . Sanitize::sanitizeAttrValue($classes) . '"><a href="' . Sanitize::sanitizeUrl($tab_arr['LINK_URL']) . '">' . Sanitize::sanitizeTagContent($tab_arr['TITLE']) . '</a></li>';
        }

        echo '</ul>';
    }

    static public function getTabsHtml($node_id)
    {
        ob_start();
        self::renderTabs(
            [
                [
                    'TITLE' => 'edit',
                    'MATCH_URL' => self::nodeEditAction(\OLOG\Router::GET_URL),
                    'LINK_URL' => self::nodeEditAction(\OLOG\Router::GET_URL, $node_id),
                ],
                [
                    'TITLE' => 'terms',
                    'MATCH_URL' => self::nodeTermsAction(\OLOG\Router::GET_URL),
                    'LINK_URL' => self::nodeTermsAction(\OLOG\Router::GET_URL, $node_id)
                ]
            ]
        );
        return ob_get_clean();
    }

}