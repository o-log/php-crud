<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace CRUDDemo;

use OLOG\BT\LayoutBootstrap4;
use OLOG\Layouts\RenderInLayoutInterface;

class CRUDDemoABase implements RenderInLayoutInterface
{
    public function renderInLayout($html_or_callable){
        LayoutBootstrap4::render($html_or_callable, $this);
    }
}
