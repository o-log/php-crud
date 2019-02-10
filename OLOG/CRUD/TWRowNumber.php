<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG\CRUD;

class TWRowNumber implements TW2Interface
{
    public function html($obj, TWContext $context){
        return ((int) $context->row_index) + 1;
    }
}
