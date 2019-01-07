<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG\CRUD;

interface TFInterface
{
    public function getTitle();
    public function sqlConditionAndPlaceholderValue();
    public function getHtml();
}
