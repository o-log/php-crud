<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace CRUDDemo;

use OLOG\CRUD\FGroupHtml;
use OLOG\CRUD\FWDate;
use OLOG\CRUD\FWDateTime;
use OLOG\CRUD\FWHtml;
use OLOG\CRUD\FWInput;
use OLOG\CRUD\FWMediumEditor;
use OLOG\CRUD\FWOptions;
use OLOG\CRUD\FWRadios;
use OLOG\CRUD\FRow;
use OLOG\CRUD\FWTextarea;
use OLOG\CRUD\FWTimestamp;
use OLOG\CRUD\FGroup;
use OLOG\CRUD\FWAceTextarea;
use OLOG\Layouts\PageTitleInterface;
use OLOG\Layouts\TopActionObjInterface;
use OLOG\MaskActionInterface;

class DemoNodeEditAction
    extends DemoNodeABase
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
        $node_id = $this->node_id;

        $html = self::tabsHtml($node_id);
        $html .= '<div>&nbsp;</div>';

        $node_obj = DemoNode::factory($node_id);

        $html .= \OLOG\CRUD\CForm::html(
            $node_obj,
            [
                new FRow(
                    'Id',
                    new FWInput('id')
                ),
                new FRow(
                    'Title',
                    new FWTextarea('title', true)
                ),
                new FRow(
                    'image_path_in_images nullable',
                    new FWInput('image_path_in_images', true)
                ),
                new FRow(
                    'Date',
                    new FWTimestamp('created_at_ts')
                ),
                new FRow(
                    'is_published',
                    new FWRadios('is_published', [0 => 'no', 1 => 'yes'])
                ),
                new FRow(
                    'published_at_datetime_str',
                    new FWDateTime('published_at_datetime_str')
                ),
                new FGroupHtml('<h2>Extra fields</h2>'),
                new FRow(
                    'expiration_date nullable',
                    new FWDate('expiration_date')
                ),
                new FRow(
                    'State code',
                    new FWOptions('state_code',
                        [
                            1 => 'announce',
                            2 => 'live',
                            3 => 'archive'
                        ]
                    )
                ),
                new FRow(
                    'State code',
                    new FWHtml('<ul><li>html widget - line 1</li><li>html widget - line 2</li></ul>')
                ),
				new FGroup(
					'пример Medium Editor',
					new FWMediumEditor('body2', '', 'placeholder: false, toolbar: {buttons: ["bold","anchor"]}')
				),
                new FGroup(
                    'пример Ace Editor',
                    new FWAceTextarea('body')
                ),
                new FGroup(
                    'created at ts',
                    new FWTimestamp(DemoNode::_CREATED_AT_TS)
                )
            ]
        );

        $this->renderInLayout($html);
    }
}
