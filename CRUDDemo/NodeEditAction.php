<?php

namespace CRUDDemo;

use OLOG\CRUD\Sanitize;
use OLOG\CRUD\CRUDFormRow;
use OLOG\CRUD\CRUDWidgetTextarea;
use OLOG\CRUD\CRUDElementVerticalFormRow;
use OLOG\CRUD\CRUDWidgetAceTextarea;

class NodeEditAction
{
    static public function getUrl($node_id = '(\d+)'){
        return '/node/' . $node_id;
    }
    
    static public function action($node_id)
    {
        \OLOG\Exits::exit403If(!Auth::currentUserHasAnyOfPermissions([1]));

        $html = self::tabsHtml($node_id);

        $node_obj = Node::factory($node_id);

        $html .= \OLOG\CRUD\CRUDEditorForm::html(
            Node::class,
            $node_id,
            [
                CRUDFormRow::html(
                    CRUDWidgetTextarea::html('id', $node_obj->getId()),
                    'Id'
                ),
                CRUDFormRow::html(
                    CRUDWidgetTextarea::html('title', $node_obj->getTitle()),
                    'Title'
                ),
                CRUDFormRow::html(
                    CRUDWidgetTextarea::html('state_code', $node_obj->getStateCode()),
                    'State code'
                ),
                CRUDElementVerticalFormRow::generateHtml(
                    CRUDWidgetAceTextarea::generateHtml('body', $node_obj->getBody()),
                    'Body'
                )
            ]
        );

        LayoutTemplate::render($html, 'Node ' . $node_id, self::breadcrumbsArr($node_id));
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
                    'MATCH_URL' => NodeEditAction::getUrl(),
                    'LINK_URL' => NodeEditAction::getUrl($node_id),
                ],
                [
                    'TITLE' => 'terms',
                    'MATCH_URL' => NodeTermsAction::getUrl(),
                    'LINK_URL' => NodeTermsAction::getUrl($node_id)
                ]
            ]
        );
        return ob_get_clean();
    }

    static public function breadcrumbsArr($node_id){
        return array_merge(NodesListAction::getBreadcrumbsArr(), [BT::a(self::getUrl($node_id), 'Node ' . $node_id)]);
    }
}