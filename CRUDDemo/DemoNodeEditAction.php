<?php

namespace CRUDDemo;

use OLOG\BT\BT;
use OLOG\CRUD\CRUDFormRowHtml;
use OLOG\CRUD\CRUDFormWidgetDate;
use OLOG\CRUD\CRUDFormWidgetDateTime;
use OLOG\CRUD\CRUDFormWidgetHtml;
use OLOG\CRUD\CRUDFormWidgetInput;
use OLOG\CRUD\CRUDFormWidgetMediumEditor;
use OLOG\CRUD\CRUDFormWidgetOptions;
use OLOG\CRUD\CRUDFormWidgetRadios;
use OLOG\Sanitize;
use OLOG\CRUD\CRUDFormRow;
use OLOG\CRUD\CRUDFormWidgetTextarea;
use OLOG\CRUD\CRUDFormWidgetTimestamp;
use OLOG\CRUD\CRUDFormVerticalRow;
use OLOG\CRUD\CRUDFormWidgetAceTextarea;

class DemoNodeEditAction
{
    static public function getUrl($node_id = '(\d+)'){
        return '/node/' . $node_id;
    }
    
    public function action($node_id)
    {
        \OLOG\Exits::exit403If(!Auth::currentUserHasAnyOfPermissions([1]));

        $html = self::tabsHtml($node_id);
        $html .= '<div>&nbsp;</div>';

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
                new CRUDFormRowHtml('<h2>Extra fields</h2>'),
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
					'пример Medium Editor',
					new CRUDFormWidgetMediumEditor('body2')
				),
                new CRUDFormVerticalRow(
                    'пример Ace Editor',
                    new CRUDFormWidgetAceTextarea('body')
                )
            ]
        );

        DemoLayoutTemplate::render($html, 'Node ' . $node_id, self::breadcrumbsArr($node_id));
    }

    // TODO: move to library
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

    // TODO: rewrite, use single tab renderer without config
    static public function tabsHtml($node_id)
    {
        ob_start();
        self::renderTabs(
            [
                [
                    'TITLE' => 'edit',
                    'MATCH_URL' => DemoNodeEditAction::getUrl(),
                    'LINK_URL' => DemoNodeEditAction::getUrl($node_id),
                ],
                [
                    'TITLE' => 'terms',
                    'MATCH_URL' => DemoNodeTermsAction::getUrl(),
                    'LINK_URL' => DemoNodeTermsAction::getUrl($node_id)
                ]
            ]
        );
        return ob_get_clean();
    }

    static public function breadcrumbsArr($node_id){
        return array_merge(DemoNodesListAction::getBreadcrumbsArr(), [BT::a(self::getUrl($node_id), 'Node ' . $node_id)]);
    }
}