<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace CRUDDemo;

use OLOG\ActionInterface;
use OLOG\CRUD\TCol;
use OLOG\CRUD\TFEqualHidden;
use OLOG\CRUD\TFEqualOptionsInline;
use OLOG\CRUD\TFLikeInline;
use OLOG\CRUD\TWReferenceSelect;
use OLOG\CRUD\TWText;

class DemoAjaxTermsListAction
    extends CRUDDemoABase
    implements ActionInterface
{
    public function url()
    {
        return '/ajax_terms';
    }

    public function action()
    {
        \OLOG\Exits::exit403If(!CRUDDemoAuth::currentUserHasAnyOfPermissions([1]));

        $html = '';

        $html .= \OLOG\CRUD\CTable::html(
            DemoTerm::class,
            '',
            [
                new TCol(
                    '',
                    new TWReferenceSelect(DemoTerm::_TITLE)
                ),
                new TCol(
                    'Edit',
                    new TWText(
                        DemoTerm::_TITLE
                    )
                ),
                new TCol(
                    'Parent',
                    new TWText(
                        function (DemoTerm $term) {
                            return $term->parent() ? $term->parent()->title : '';
                        }
                    )
                )
            ],
            [
                new TFEqualHidden('parent_id', null),
                new TFEqualOptionsInline('34785ty8y45t8', 'Словарь', 'vocabulary_id', DemoTerm::VOCABULARIES_ARR, false, null, true),
                new TFEqualOptionsInline('345634g3tg534', '', 'gender', DemoTerm::GENDER_ARR, false, null, true, 'М. и Ж.'),
                //new CRUDTableFilter('title', CRUDTableFilter::FILTER_LIKE, '')
                new TFLikeInline('3748t7t45gdfg', '', 'title', 'Название содержит')
            ],
            '',
            25683745
        );

        echo $html;
    }

}
