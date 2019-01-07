<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG\CRUD;

interface TFHiddenInterface
{
    public function sqlConditionAndPlaceholderValue();
}
