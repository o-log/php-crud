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
use OLOG\CRUD\CRUDFormRow;
use OLOG\CRUD\CRUDFormWidgetTextarea;
use OLOG\CRUD\CRUDFormWidgetTimestamp;
use OLOG\CRUD\CRUDFormVerticalRow;
use OLOG\CRUD\CRUDFormWidgetAceTextarea;
use CRUDDemo\DemoNodeBase;
use OLOG\Layouts\AdminLayoutSelector;
use OLOG\Layouts\PageTitleInterface;
use OLOG\Layouts\TopActionObjInterface;
use OLOG\MaskActionInterface;

class DemoNodeEditAction
    extends DemoNodeBase
    implements MaskActionInterface, PageTitleInterface, TopActionObjInterface
{
    static public function mask(){
        return '/node/(\d+)';
    }

    public function url(){
        return '/node/' . $this->node_id;
    }

    public function action()
    {
        \OLOG\Exits::exit403If(!CRUDDemoAuth::currentUserHasAnyOfPermissions([1]));

        $node_id = $this->node_id;

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
                    new CRUDFormWidgetTextarea('title', true)
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
					new CRUDFormWidgetMediumEditor('body2', '', 'placeholder: false, toolbar: {buttons: ["bold","anchor"]}')
				),
                new CRUDFormVerticalRow(
                    'пример Ace Editor',
                    new CRUDFormWidgetAceTextarea('body')
                )
            ]
        );

        AdminLayoutSelector::render($html, $this);
    }
}