<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace CRUDDemo;

use OLOG\CRUD\CForm;
use OLOG\CRUD\FGroupHidden;
use OLOG\CRUD\FRow;
use OLOG\CRUD\CTable;
use OLOG\CRUD\TCol;
use OLOG\CRUD\TFEqualHidden;
use OLOG\CRUD\TWDelete;
use OLOG\CRUD\TWText;
use OLOG\CRUD\FWInput;
use OLOG\CRUD\FWReference;
use OLOG\Layouts\PageTitleInterface;
use OLOG\Layouts\TopActionObjInterface;
use OLOG\MaskActionInterface;

class DemoNodeTermsAction
    extends DemoNodeABase
    implements MaskActionInterface, PageTitleInterface, TopActionObjInterface
{
    static public function mask()
    {
        return '/node/(\d+)/terms';
    }

    public function url()
    {
        return '/node/' . $this->node_id . '/terms';
    }

    public function action()
    {
        $html = self::tabsHtml($this->node_id);
        $html .= '<div>&nbsp;</div>';

        $new_term_to_node = new DemoTermToNode();
        $new_term_to_node->setNodeId($this->node_id);

        $html .= CTable::html(
            DemoTermToNode::class,
            CForm::html(
                $new_term_to_node,
                [
                    new FGroupHidden(
                        new FWInput('node_id')
                    ),
                    new FRow(
                        'Term id',
                        new FWReference('term_id', DemoTerm::class, 'title'),
                        'Рубрика, с которой должен быть связан материал'
                    )
                ]
            ),
            [
                new TCol('Term', new TWText(function (DemoTermToNode $ttn){
                    return $ttn->term()->title;
                })),
                new TCol('Delete', new TWDelete())
            ],
            [new TFEqualHidden('node_id', $this->node_id)]
        );

        $this->renderInLayout($html);
    }
}
