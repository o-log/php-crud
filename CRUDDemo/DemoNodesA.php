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
use OLOG\CRUD\TWText;
use OLOG\CRUD\FWInput;
use OLOG\CRUD\TWTimestamp;
use OLOG\CRUD\TWWeight;
use OLOG\Layouts\PageTitleInterface;
use OLOG\Layouts\TopActionObjInterface;

class DemoNodesA
    extends CRUDDemoABase
    implements ActionInterface, TopActionObjInterface, PageTitleInterface
{
    public function pageTitle()
    {
        return 'Nodes';
    }

    public function topActionObj()
    {
        return new DemoMainPageAction();
    }

    public function url()
	{
		return '/nodes';
	}

	public function action()
	{
		$table_id = 'tableContainer_NodeList';
		$form_id = 'formElem_NodeList';

		$html = '';

		$html .= CTable::html(
			DemoNode::class,
			\OLOG\CRUD\CForm::html(
				new DemoNode(),
				[
					new FRow(
						'Title',
						new FWInput('title')
					),
					new FRow(
						'body2',
						new FWInput('body2')
					)
				],
				'',
				[],
				$form_id
			),
			[
				new TCol(
					'Title',
					new TWHtmlWithLink(
                        function(DemoNode $node){
					        return 'DIRECT: ' . $node->getTitle() . '<br>REVERSE: ' . $node->getReverseTitle();
                        },
                        function(DemoNode $node) {
                            return (new DemoNodeEditAction($node->getId()))->url();
                        }
                    )
				),
				new TCol(
					'Reverse title',
					new TWText(function(DemoNode $node){
                        $title = $node->getTitle();
                        return 'REVERSE: ' . strrev($title);
                    })
				),
                new TCol(
                    'Current time',
                    new TWTimestamp(DemoNode::_CREATED_AT_TS, '')
                ),
                new TCol(
                    '',
                    new TWWeight([])
                ),
                new TCol(
                    '',
                    new TWDelete()
                ),
			],
			[
			    new TFLikeInline('h7g98347hg934', 'Название', 'title'),
                new TFEqualOptionsInline('hk4g78gwed', 'Опубликовано', 'is_published', [0 => 'Нет', 1 => 'Да'], false, 0, false)
            ],
			'weight',
			$table_id,
            'Nodes',
            false,
            5
		);

		// Загрузка скриптов
		//$html .= CCreateFormScript::getHtml($form_id, $table_id);

		$this->renderInLayout($html);
	}
}
