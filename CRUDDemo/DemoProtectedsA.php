<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace CRUDDemo;

use OLOG\ActionInterface;
use OLOG\CRUD\CTable;
use OLOG\CRUD\FRow;
use OLOG\CRUD\TCol;
use OLOG\CRUD\TFEqualOptionsInline;
use OLOG\CRUD\TFLikeInline;
use OLOG\CRUD\TWDelete;
use OLOG\CRUD\TWHtmlWithLink;
use OLOG\CRUD\TWRowNumber;
use OLOG\CRUD\TWText;
use OLOG\CRUD\FWInput;
use OLOG\CRUD\TWTimestamp;
use OLOG\CRUD\TWWeight;
use OLOG\Layouts\PageTitleInterface;
use OLOG\Layouts\TopActionObjInterface;

class DemoProtectedsA
    extends CRUDDemoABase
    implements ActionInterface, TopActionObjInterface, PageTitleInterface
{
    public function pageTitle()
    {
        return 'protecteds';
    }

    public function topActionObj()
    {
        return new DemoMainA();
    }

    public function url()
    {
        return '/protecteds';
    }

    public function action()
    {
        $html = '';

        $html .= CTable::html(
            DemoProtected::class,
            \OLOG\CRUD\CForm::html(
                new DemoProtected(),
                [
                    new FRow(
                        'String not null',
                        new FWInput(DemoProtected::_STRING_VAL_NOTNULL)
                    ),
                    new FRow(
                        'Int nullable',
                        new FWInput(DemoProtected::_INT_VAL_NULLABLE)
                    )
                ],
                '',
                [],
                '67iuguij6u'
            ),
            [
                new TCol(
                    '№',
                    new TWRowNumber()
                ),
                new TCol(
                    'String not null',
                    new TWText(DemoProtected::_STRING_VAL_NOTNULL)
                ),
                new TCol(
                    'Int null',
                    new TWText(DemoProtected::_INT_VAL_NULLABLE)
                ),
                new TCol(
                    'Created',
                    new TWTimestamp(DemoProtected::_CREATED_AT_TS),
                    DemoProtected::_CREATED_AT_TS
                ),
                new TCol(
                    '',
                    new TWDelete()
                ),
            ],
            []
        );

        $this->renderInLayout($html);
    }
}
