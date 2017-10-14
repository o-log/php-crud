<?php

namespace CRUDDemo;

use OLOG\ActionInterface;
use OLOG\CRUD\CRUDCreateFormScript;
use OLOG\CRUD\CRUDTable;
use OLOG\CRUD\CRUDFormRow;
use OLOG\CRUD\CRUDTableColumn;
use OLOG\CRUD\CRUDTableFilterEqualOptions;
use OLOG\CRUD\CRUDTableFilterLike;
use OLOG\CRUD\CRUDTableWidgetDelete;
use OLOG\CRUD\CRUDTableWidgetHtmlWithLink;
use OLOG\CRUD\CRUDTableWidgetText;
use OLOG\CRUD\CRUDFormWidgetInput;
use OLOG\CRUD\CRUDTableWidgetWeight;
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

		$html .= CRUDTable::html(
			DemoNode::class,
			\OLOG\CRUD\CRUDForm::html(
				new DemoNode(),
				[
					new CRUDFormRow(
						'Title',
						new CRUDFormWidgetInput('title')
					),
					new CRUDFormRow(
						'body2',
						new CRUDFormWidgetInput('body2')
					)
				],
				'',
				[],
				$form_id
			),
			[
				new CRUDTableColumn(
					'Title',
					new CRUDTableWidgetHtmlWithLink('{this->title}<br>{this->getReverseTitle()}', (new DemoNodeEditAction('{this->id}'))->url())
				),
				new CRUDTableColumn(
					'Reverse title',
					new CRUDTableWidgetText('{this->getReverseTitle()}')
				),
                new CRUDTableColumn(
                    '',
                    new CRUDTableWidgetWeight([])
                ),
                new CRUDTableColumn(
                    '',
                    new CRUDTableWidgetDelete()
                ),
			],
			[
			    new CRUDTableFilterLike('h7g98347hg934', 'Название', 'title'),
                new CRUDTableFilterEqualOptions('hk4g78gwed', 'Опубликовано', 'is_published', [0 => 'Нет', 1 => 'Да'], false, 0, false)
            ],
			'weight',
			$table_id,
            CRUDTable::FILTERS_POSITION_TOP
		);

		// Загрузка скриптов
		$html .= CRUDCreateFormScript::getHtml($form_id, $table_id);

		AdminLayoutSelector::render($html, $this);
	}
}