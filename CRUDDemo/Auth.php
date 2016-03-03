<?php

namespace CRUDDemo;

/**
 * Класс предоставляет заглушки методов проверки прав для круда.
 * Чтобы использовать круд в реальном проекте - нужно чтобы в проекте был класс с аналогичным функционалом.
 * Class Auth
 * @package CRUDDemo
 */
class Auth
{
    static public function currentUserHasAnyOfPermissions(array $permission_codes_arr){
        return true;
    }
}
