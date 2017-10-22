<?php

namespace CRUDDemo;

use OLOG\ActionInterface;
use OLOG\CRUD\CRUDForm;
use OLOG\CRUD\CRUDFormRow;
use OLOG\CRUD\CRUDFormWidgetRadios;
use OLOG\CRUD\CRUDFormWidgetOptions;
use OLOG\CRUD\CRUDFormWidgetReferenceAjax;
use OLOG\CRUD\CRUDTable;
use OLOG\CRUD\CRUDTableColumn;
use OLOG\CRUD\CRUDTableFilterEqualInline;
use OLOG\CRUD\CRUDTableFilterEqualInvisible;
use OLOG\CRUD\CRUDTableFilterEqualOptionsInline;
use OLOG\CRUD\CRUDTableFilterLikeInline;
use OLOG\CRUD\CRUDTableWidgetDelete;
use OLOG\CRUD\CRUDTableWidgetOptions;
use OLOG\CRUD\CRUDTableWidgetOptionsEditor;
use OLOG\CRUD\CRUDTableWidgetText;
use OLOG\CRUD\CRUDTableWidgetTextEditor;
use OLOG\CRUD\CRUDTableWidgetTextWithLink;
use OLOG\CRUD\CRUDFormWidgetInput;
use OLOG\CRUD\CRUDTableWidgetWeight;
use OLOG\Layouts\AdminLayoutSelector;
use OLOG\Layouts\PageTitleInterface;
use OLOG\Layouts\TopActionObjInterface;

class DemoTermsListAction
    implements ActionInterface, PageTitleInterface, TopActionObjInterface
{
    public function topActionObj()
    {
        return new DemoMainPageAction();
    }

    public function pageTitle()
    {
        return 'Terms';
    }

    public function url()
    {
        return '/terms';
    }

    public function action()
    {
        \OLOG\Exits::exit403If(!CRUDDemoAuth::currentUserHasAnyOfPermissions([1]));

        $html = '';

        $table_id = '8726438755234';

        $html .= \OLOG\CRUD\CRUDTable::html(
            \CRUDDemo\DemoTerm::class,
            CRUDForm::html(
                new DemoTerm,
                [
                    new CRUDFormRow(
                        'Title',
                        new CRUDFormWidgetInput('title', false, true)
                    ),
                    new CRUDFormRow(
                        'Chooser',
                        new CRUDFormWidgetRadios('chooser', [
                            1 => 'one',
                            2 => 'two'
                        ], true, true)
                    ),
                    new CRUDFormRow(
                        'Options',
                        new CRUDFormWidgetOptions('options', [
                            1 => 'one',
                            2 => 'two'
                        ], false, true)
                    ),
                    new CRUDFormRow(
                        'Parent id',
                        new CRUDFormWidgetReferenceAjax(
                            'parent_id',
                            DemoTerm::class,
                            'title',
                            (new DemoAjaxTermsListAction())->url(),
                            (new DemoTermEditAction(CRUDFormWidgetReferenceAjax::REFERENCED_ID_PLACEHOLDER))->url()

                        )
                    )

                ]
            ),
            [
                new CRUDTableColumn(
                    '',
                    new CRUDTableWidgetTextWithLink(
                        '{this->title}',
                        (new DemoTermEditAction('{this->id}'))->url()
                    )
                ),
                new CRUDTableColumn(
                    '',
                    new CRUDTableWidgetTextEditor('title', '{this->title}', $table_id)
                ),
	            new CRUDTableColumn(
		            '',
		            new CRUDTableWidgetOptions(
			            '{this->vocabulary_id}',
			            DemoTerm::VOCABULARIES_ARR
		            )
	            ),
	            new CRUDTableColumn(
		            '',
		            new CRUDTableWidgetOptionsEditor('vocabulary_id', DemoTerm::VOCABULARIES_ARR, $table_id)
	            ),
                new CRUDTableColumn(
                    '',
                    new CRUDTableWidgetText(
                        '{' . DemoTerm::class . '.{this->parent_id}->title}'
                    )
                ),
                new CRUDTableColumn('', new CRUDTableWidgetWeight(['parent_id' => null])),
                new CRUDTableColumn('', new CRUDTableWidgetDelete())
            ],
            [
                new CRUDTableFilterEqualInvisible('parent_id', null),
                //new CRUDTableFilter('vocabulary_id', CRUDTableFilter::FILTER_EQUAL, DemoTerm::VOCABULARY_MAIN, new CRUDFormWidgetOptions('vocabulary_id', DemoTerm::VOCABULARIES_ARR)),
                new CRUDTableFilterEqualOptionsInline('34785ty8y45t8', 'Словарь', 'vocabulary_id', DemoTerm::VOCABULARIES_ARR, false, null, true),
	            new CRUDTableFilterEqualOptionsInline('345634g3tg534', '', 'gender', DemoTerm::GENDER_ARR, false, null, true, 'М. и Ж.'),
                //new CRUDTableFilter('title', CRUDTableFilter::FILTER_LIKE, '')
                new CRUDTableFilterLikeInline('3748t7t45gdfg', '', 'title', 'Название содержит'),
                new CRUDTableFilterEqualInline('345634g2v35234', '', 'title', 'Название')
            ],
            'weight',
            $table_id,
            CRUDTable::FILTERS_POSITION_INLINE,
            true
        );

        AdminLayoutSelector::render($html, $this);
    }
    
}