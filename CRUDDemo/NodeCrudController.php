<?php

namespace CRUDDemo;

use OLOG\CRUD\CRUDElements;
use \OLOG\CRUD\CRUDListTemplate;
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
                            CRUDElements::KEY_ELEMENT_TYPE => \OLOG\CRUD\CRUDElements::ELEMENT_FORM_ROW,
                            'FIELD_NAME' => 'node_id',
                            'WIDGET' => [
                                'WIDGET_TYPE' => 'WIDGET_INPUT'
                            ]
                        ],
                        [
                            CRUDElements::KEY_ELEMENT_TYPE => \OLOG\CRUD\CRUDElements::ELEMENT_FORM_ROW,
                            'FIELD_NAME' => 'term_id',
                            'WIDGET' => [
                                'WIDGET_TYPE' => 'WIDGET_INPUT'
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

        DefaultLayoutTemplate::render($html, '<a href="' . self::nodesListAction(\OLOG\Router::GET_URL) . '">Nodes</a> / Node ' . $node_id);
    }

    static public function renderTabs(array $tabs_arr)
    {
        echo '<ul class="nav nav-tabs">';

        foreach ($tabs_arr as $tab_arr) {
            $classes = '';

            // TODO: код из Router::match3() - использовать общую реализацию?

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

    static public function getTabsHtml($node_id){
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

    static public function nodeEditAction($_mode, $node_id = '(\d+)')
    {
        if ($_mode == \OLOG\Router::GET_URL) return '/admin/node/' . $node_id;
        if ($_mode == \OLOG\Router::GET_METHOD) return __METHOD__;

        //

        \OLOG\Exits::exit403If(!Auth::currentUserHasAnyOfPermissions([1]));

        $html = self::getTabsHtml($node_id);

        ob_start();
        \OLOG\CRUD\CRUDElements::renderEditorForm(
            Node::class, $node_id,
            [
                'ELEMENTS' => [
                    [
                        CRUDElements::KEY_ELEMENT_TYPE => \OLOG\CRUD\CRUDElements::ELEMENT_FORM_ROW,
                        'FIELD_NAME' => 'title',
                        'WIDGET' => [
                            'WIDGET_TYPE' => 'WIDGET_TEXTAREA'
                        ]
                    ],
                    [
                        CRUDElements::KEY_ELEMENT_TYPE => \OLOG\CRUD\CRUDElements::ELEMENT_FORM_ROW,
                        'FIELD_NAME' => 'id',
                        'WIDGET' => [
                            'WIDGET_TYPE' => 'WIDGET_INPUT'
                        ]
                    ]
                ]
            ]
        );
        $html .= ob_get_clean();

        DefaultLayoutTemplate::render($html, '<a href="' . self::nodesListAction(\OLOG\Router::GET_URL) . '">Nodes</a> / Node ' . $node_id);
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
                            CRUDElements::KEY_ELEMENT_TYPE => \OLOG\CRUD\CRUDElements::ELEMENT_FORM_ROW,
                            'FIELD_NAME' => 'title',
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
                            'TEXT' => '{this.title}'
                        ]
                    ],
                    [
                        'COLUMN_TITLE' => 'Edit',
                        'WIDGET' => [
                            'WIDGET_TYPE' => 'TEXT_WITH_LINK',
                            'LINK_URL' => NodeCrudController::nodeEditAction(\OLOG\Router::GET_URL, '{this->id}'),
                            'TEXT' => '{\CRUDDemo\Node.{this->id}->title}'
                        ]
                    ]
                ]
            ]
        );
        $html = ob_get_clean();

        DefaultLayoutTemplate::render($html, 'Nodes');
    }
}