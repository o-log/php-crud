<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace CRUDDemo;

/**
 * Класс предоставляет заглушки методов проверки прав для круда.
 * Чтобы использовать круд в реальном проекте - нужно чтобы в проекте был класс с аналогичным функционалом.
 * Class Auth
 * @package CRUDDemo
 */
class CRUDDemoAuth
{
    static public function currentUserHasAnyOfPermissions(array $permission_codes_arr){
        return true;
    }
}
