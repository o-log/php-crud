<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG\CRUD;

interface TW2Interface
{
    public function html($obj, TWContext $context);
}
