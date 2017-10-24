<?php

namespace CRUDDemo;

use OLOG\ActionInterface;
use OLOG\CRUD\CRUDCreateFormScript;
use OLOG\CRUD\CTable;
use OLOG\CRUD\FRow;
use OLOG\CRUD\TCol;
use OLOG\CRUD\TFEqualOptions;
use OLOG\CRUD\TFLike;
use OLOG\CRUD\TWDelete;
use OLOG\CRUD\TWHtmlWithLink;
use OLOG\CRUD\TWText;
use OLOG\CRUD\FWInput;
use OLOG\CRUD\TWWeight;
use OLOG\Layouts\AdminLayoutSelector;
use OLOG\Layouts\PageTitleInterface;
use OLOG\Layouts\TopActionObjInterface;

class DemoNodesListAction
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
		\OLOG\Exits::exit403If(!CRUDDemoAuth::currentUserHasAnyOfPermissions([1]));

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
					new TWHtmlWithLink('{this->title}<br>{this->getReverseTitle()}', (new DemoNodeEditAction('{this->id}'))->url())
				),
				new TCol(
					'Reverse title',
					new TWText('{this->getReverseTitle()}')
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
			    new TFLike('h7g98347hg934', 'Название', 'title'),
                new TFEqualOptions('hk4g78gwed', 'Опубликовано', 'is_published', [0 => 'Нет', 1 => 'Да'], false, 0, false)
            ],
			'weight',
			$table_id,
            CTable::FILTERS_POSITION_TOP
		);

		// Загрузка скриптов
		$html .= CRUDCreateFormScript::getHtml($form_id, $table_id);

		AdminLayoutSelector::render($html, $this);
	}
}