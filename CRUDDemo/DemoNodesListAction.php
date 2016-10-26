<?php

namespace CRUDDemo;

use OLOG\BT\BT;
use OLOG\CRUD\CRUDTable;
use OLOG\CRUD\CRUDFormRow;
use OLOG\CRUD\CRUDTableColumn;
use OLOG\CRUD\CRUDTableWidgetDelete;
use OLOG\CRUD\CRUDTableWidgetText;
use OLOG\CRUD\CRUDTableWidgetTextWithLink;
use OLOG\CRUD\CRUDFormWidgetInput;
use OLOG\CRUD\templates\CRUDCreateFormScript;
use OLOG\Render;

class DemoNodesListAction
{
	static public function getUrl()
	{
		return '/nodes';
	}

	public function action()
	{
		\OLOG\Exits::exit403If(!Auth::currentUserHasAnyOfPermissions([1]));

		$table_id = 'tableContainer_NodeList';
		$form_id = 'formElem_NodeList';

		ob_start();
		CRUDCreateFormScript::render();
		?>
		<script>
			CRUD.CreateForm.init('#<?= $form_id ?>','.<?= $table_id ?>');
		</script>
		<?php
		$script = ob_get_clean();

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
					new CRUDTableWidgetText('{this->title}')
				),
				new CRUDTableColumn(
					'Reverse title',
					new CRUDTableWidgetText('{this->getReverseTitle()}')
				),
				new CRUDTableColumn(
					'',
					new CRUDTableWidgetTextWithLink(
						'Edit',
						DemoNodeEditAction::getUrl('{this->id}'),
						'btn btn-xs btn-default'
					)
				),
				new CRUDTableColumn(
					'',
					new CRUDTableWidgetDelete()
				),
			],
			[],
			'title',
			$table_id
		);

		$html .= $script;

		DemoLayoutTemplate::render($html, 'Nodes', self::getBreadcrumbsArr());
	}

	static public function getBreadcrumbsArr()
	{
		return array_merge(DemoMainPageAction::breadcrumbsArr(), [BT::a(self::getUrl(), 'Nodes')]);
	}
}