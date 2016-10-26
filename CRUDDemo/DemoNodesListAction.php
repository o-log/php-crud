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
		?>
		<script>
			var CRUD = CRUD || {};

			CRUD.CreateForm = CRUD.CreateForm || {

					init: function (form_elem, table_elem) {
						var $table = $(table_elem);
						var $form = $(form_elem);

						$form.on('submit', function (e) {
							e.preventDefault();
							var url = $form.attr('action');
							var data = $form.serializeArray();

							CRUD.CreateForm.requestAjax(table_elem, url, data);
						});

						console.log($table, $form);
					},

					requestAjax: function (table_elem, query, data) {

						CRUD.Table.preloader.show();

						$.ajax({
							type: "POST",
							url: query,
							data: data
						}).success(function (received_html) {
							CRUD.Table.preloader.hide();

							var received_table_html = $(received_html).find(table_elem).html();
							$(table_elem).html(received_table_html);
						}).fail(function () {
							CRUD.Table.preloader.hide();
						});
					}
				};

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