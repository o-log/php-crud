<?php

namespace OLOG\CRUD;

/**
 * КРУД проверяет, реализует ли модель функционал наших моделей.
 * Если умеет загружаться - круд может показывать такие модели.
 * Если умеет сохраняться - круд может редактировать такие модели.
 */
class CRUDController
{
    const URL_PREFIX = '/admin/php_crud/';

    const OPERATION_SAVE_EDITOR_FORM = 'OPERATION_SAVE_EDITOR_FORM';

    static protected $editor_context_obj = null;

    // goes to layout?
    //static public $base_breadcrumbs = array();

    /**
     * Выбрасывает исключение если контекст не сохранен. Это для того, чтобы клиентам не нужно было проверять существование контекста.
     * @return null
     * @throws \Exception
     */
    static public function getEditorContext(){
        \OLOG\Helpers::assert(self::$editor_context_obj);

        return self::$editor_context_obj;
    }

    /**
     * Выводит список моделей.
     * @param $_mode
     * @param string $bubble_key ключ конфига круда, для которого нужно вывести список моделей
     * @return string
     * @throws \Exception
     */
    static public function listAction($_mode, $bubble_key = '(\w+)')
    {
        if ($_mode == \OLOG\Router::GET_URL) return self::URL_PREFIX . $bubble_key;
        if ($_mode == \OLOG\Router::GET_METHOD) return __METHOD__;

        //

        /* TODO
        \Sportbox\Helpers::exit403If(!\Sportbox\CRUD\Helpers::currentUserHasRightsToEditModel($model_class_name));
        \Sportbox\CRUD\Helpers::exceptionIfClassNotImplementsInterface($model_class_name, 'Sportbox\Model\InterfaceLoad');
        */

        //

        $context_arr = array();
        if (array_key_exists('context_arr', $_GET)) {
            $context_arr = $_GET['context_arr'];
        }

        ob_start();
        ListTemplate::render($bubble_key, $context_arr);
        $html = ob_get_clean();

        self::renderLayout($html);

        /* TODO
        // todo: move to helper?
        $crud_model_class_screen_name_for_list = 'Список';
        if (property_exists($model_class_name, 'crud_model_class_screen_name_for_list')){
            $crud_model_class_screen_name_for_list = $model_class_name::$crud_model_class_screen_name_for_list;
        }

        echo \Sportbox\Render::template2('Sportbox/Admin/templates/layout.tpl.php', array(
                'title' => $crud_model_class_screen_name_for_list,
                'content' => $list_html,
                'breadcrumbs_arr' => self::$base_breadcrumbs
            )
        );
        */
    }

    /** TODO: rewrite
     * Выводит форму создания объекта.
     * Принимает в запросе контекст (набор полей со значениями) и передает его на экшен создания объекта.
     * @param $model_class_name
     */
    static public function addAction($_mode, $config_key = '(\w+)')
    {
        if ($_mode == \OLOG\Router::GET_URL) return self::URL_PREFIX . $config_key . '/add';
        if ($_mode == \OLOG\Router::GET_METHOD) return __METHOD__;

        //

        $model_class_name = CRUDConfigReader::getModelClassNameForKey($config_key);


        /* TODO
        \OLOG\Helpers::exit403If(!\OLOG\CRUD\Helpers::currentUserHasRightsToEditModel($model_class_name));
        */

        \OLOG\Helpers::assert($model_class_name);
        \OLOG\Model\Helper::exceptionIfClassNotImplementsInterface($model_class_name, 'OLOG\Model\InterfaceLoad');
        \OLOG\Model\Helper::exceptionIfClassNotImplementsInterface($model_class_name, 'OLOG\Model\InterfaceSave');

        ob_start();
        AddFormTemplate::render($model_class_name, $config_key);
        $html = ob_get_clean();

        self::renderLayout($html);
    }

    static public function renderLayout($html){
        // TODO: also get layout render callable from config

        DefaultLayoutTemplate::render($html);
    }

    /**
     * @param $_mode
     * @param string $bubble_key
     * @param string $object_id
     * @param string $tab_key
     * @return string
     */
    static public function editAction($_mode, $bubble_key = '(\w+)', $object_id = '(\d+)', $tab_key = '(\w+)')
    {
        if ($_mode == \OLOG\Router::GET_URL) return self::URL_PREFIX . $bubble_key . '/' . $object_id . '/' . $tab_key;
        if ($_mode == \OLOG\Router::GET_METHOD) return __METHOD__;

        // сохранение контекста

        self::$editor_context_obj = new EditorContext($bubble_key, $object_id, $tab_key);

        // операции

        Operations::matchOperation(self::OPERATION_SAVE_EDITOR_FORM, function() use($bubble_key, $object_id, $tab_key) {
            //
            // чтение данных из формы
            //

            $model_class_name = CRUDConfigReader::getModelClassNameForKey($bubble_key);

            $new_prop_values_arr = array();
            $reflect = new \ReflectionClass($model_class_name);

            foreach ($reflect->getProperties() as $prop_obj) {
                if (!$prop_obj->isStatic()) { // игнорируем статические свойства класса - они относятся не к объекту, а только к классу (http://www.php.net/manual/en/language.oop5.static.php), и в них хранятся настройки ActiveRecord и CRUD
                    $prop_name = $prop_obj->getName();
                    if (array_key_exists($prop_name, $_POST)) {
                        // Проверка на заполнение обязательных полей
                        /* TODO
                        if ( (($_POST[$prop_name] == '') && (\Sportbox\CRUD\Helpers::isRequiredField($model_class_name, $prop_obj->getName())) ) ) {
                            throw new \Exception('поле ' . $prop_obj->getName() . ' обязательно для заполнения');
                        }
                        */
                        $new_prop_values_arr[$prop_name] = $_POST[$prop_name];
                    }
                }
            }

            //
            // сохранение
            //

            $obj = ObjectLoader::createAndLoadObject($model_class_name, $object_id);

            $obj = FieldsAccess::setObjectFieldsFromArray($obj, $new_prop_values_arr);
            $obj->save();

            /* TODO
            \Sportbox\Logger\Logger::logObjectEvent($obj, 'CRUD сохранение');
            $redirect_url = \Sportbox\CRUD\ControllerCRUD::getEditUrlForObj($obj);

            if (array_key_exists('destination', $_POST)){
                $redirect_url = $_POST['destination'];
            }

            \Sportbox\Helpers::redirect($redirect_url);
            */

            \OLOG\Helpers::redirectToSelfNoGetForm();
        });

        /* TODO
        \Sportbox\Helpers::exit403If(!\Sportbox\CRUD\Helpers::currentUserHasRightsToEditModel($model_class_name));
        */

        /*
        \Sportbox\Helpers::assert($model_class_name);
        \Sportbox\Helpers::assert($obj_id);
        \Sportbox\CRUD\Helpers::exceptionIfClassNotImplementsInterface($model_class_name, 'Sportbox\Model\InterfaceLoad');

        $edited_obj = \Sportbox\CRUD\Helpers::createAndLoadObject($model_class_name, $obj_id);

        $html = \Sportbox\Render::template2('Sportbox/CRUD/templates/edit_form.tpl.php', array(
                'obj' => $edited_obj
            )
        );
        */

        ob_start();
        EditFormTemplate::render($bubble_key, $object_id, $tab_key);
        $html = ob_get_clean();

        self::renderLayout($html);

        /* TODO
        $breadcrumbs_arr = self::$base_breadcrumbs;

        if (!property_exists($model_class_name, 'show_models_list_link')) {
            $show_models_list_link = true;
        } else {
            $show_models_list_link = $model_class_name::$show_models_list_link;
        }

        if ($show_models_list_link) {
            $crud_model_class_screen_name_for_list = $model_class_name;
            if (property_exists($model_class_name, 'crud_model_class_screen_name_for_list')){
                $crud_model_class_screen_name_for_list = $model_class_name::$crud_model_class_screen_name_for_list;
            }

            $breadcrumbs_arr = array_merge(
                $breadcrumbs_arr,
                array(
                    $crud_model_class_screen_name_for_list => '/crud/list/' . urlencode($model_class_name)
                )
            );
        }
        */

        /* REMOVE?
        $container_obj = \Sportbox\CRUD\Helpers::getObjContainerObj($edited_obj);
        if ($container_obj) {
            $container_obj_url = self::getEditUrlForObj($container_obj);
            $container_obj_full_title = \Sportbox\CRUD\Helpers::getFullObjectTitle($container_obj);
            $breadcrumbs_arr[$container_obj_full_title] = $container_obj_url;
        }
        */

        /* TODO
        echo \Sportbox\Render::template2('Sportbox/Admin/templates/layout.tpl.php', array(
                'title' => \Sportbox\CRUD\Helpers::getModelTitleForObj($edited_obj),
                'content' => $html,
                'breadcrumbs_arr' => $breadcrumbs_arr
            )
        );
        */
    }

    /**
     * генерирует ссылку на редактор объекта
     */
    /*
    public static function getEditUrl($model_class_name, $obj_id)
    {
        // TODO: fix
        return '/crud/edit/' . urlencode($model_class_name) . '/' . $obj_id;
    }
    */

    /**
     * генерирует ссылку на редактор объекта
     */
    /*
    static public function getEditUrlForObj($obj)
    {
        // добавляем \ в начале имени класса - мы всегда работаем с классами в глобальном неймспейсе
        $obj_class_name = '\\' . get_class($obj);
        \Sportbox\CRUD\Helpers::exceptionIfClassNotImplementsInterface($obj_class_name, 'Sportbox\Model\InterfaceLoad');

        $obj_id = $obj->getId();

        return self::getEditUrl($obj_class_name, $obj_id);
    }

    static public function getDeleteUrl($model_class_name, $obj_id)
    {
        // TODO: fix
        return '/crud/delete/' . urlencode($model_class_name) . '/' . $obj_id;
    }
    */

	/**
	 * генерирует ссылку на удаление объекта
	 */
    /*
	static public function getDeleteUrlForObj($obj)
	{
		// добавляем \ в начале имени класса - мы всегда работаем с классами в глобальном неймспейсе
		$obj_class_name = '\\' . get_class($obj);
		\Sportbox\CRUD\Helpers::exceptionIfClassNotImplementsInterface($obj_class_name, 'Sportbox\Model\InterfaceLoad');

		$obj_id = $obj->getId();

		return self::getDeleteUrl($obj_class_name, $obj_id);
	}

	static public function getListUrl($model_class_name)
	{
		return '/crud/list/' . urlencode($model_class_name);
	}

    static public function getCreateUrl($model_class_name)
    {
        return '/crud/create/' . urlencode($model_class_name);
    }
    */

    /*
    public function saveAction($model_class_name, $obj_id)
    {
        //
        // проверка
        //

        \Sportbox\Helpers::exit403If(!\Sportbox\CRUD\Helpers::currentUserHasRightsToEditModel($model_class_name));

        \Sportbox\Helpers::assert($model_class_name);
        \Sportbox\Helpers::assert($obj_id);
        \Sportbox\CRUD\Helpers::exceptionIfClassNotImplementsInterface($model_class_name, 'Sportbox\Model\InterfaceLoad');
        \Sportbox\CRUD\Helpers::exceptionIfClassNotImplementsInterface($model_class_name, 'Sportbox\Model\InterfaceSave');

        //
        // чтение данных из формы
        //

        $new_prop_values_arr = array();
        $reflect = new \ReflectionClass($model_class_name);

        foreach ($reflect->getProperties() as $prop_obj) {
            if (!$prop_obj->isStatic()) { // игнорируем статические свойства класса - они относятся не к объекту, а только к классу (http://www.php.net/manual/en/language.oop5.static.php), и в них хранятся настройки ActiveRecord и CRUD
                $prop_name = $prop_obj->getName();
                if (array_key_exists($prop_name, $_POST)) {
                	// Проверка на заполнение обязательных полей
                	if ( (($_POST[$prop_name] == '') && (\Sportbox\CRUD\Helpers::isRequiredField($model_class_name, $prop_obj->getName())) ) ) {
	                	throw new \Exception('поле ' . $prop_obj->getName() . ' обязательно для заполнения');
                	}
                    $new_prop_values_arr[$prop_name] = $_POST[$prop_name];
                }
            }
        }

        //
        // сохранение
        //

        $obj = \Sportbox\CRUD\Helpers::createAndLoadObject($model_class_name, $obj_id);

        $obj = \Sportbox\CRUD\Helpers::setObjectFieldsFromArray($obj, $new_prop_values_arr);
        $obj->save();

        \Sportbox\Logger\Logger::logObjectEvent($obj, 'CRUD сохранение');
        $redirect_url = \Sportbox\CRUD\ControllerCRUD::getEditUrlForObj($obj);

        if (array_key_exists('destination', $_POST)){
            $redirect_url = $_POST['destination'];
        }

        \Sportbox\Helpers::redirect($redirect_url);
    }
    */

    /*
    public function createAction($model_class_name)
    {
        //
        // проверка
        //

        \Sportbox\Helpers::exit403If(!\Sportbox\CRUD\Helpers::currentUserHasRightsToEditModel($model_class_name));

        \Sportbox\Helpers::assert($model_class_name);
	    \Sportbox\CRUD\Helpers::exceptionIfClassNotImplementsInterface($model_class_name, 'Sportbox\Model\InterfaceLoad');
        \Sportbox\CRUD\Helpers::exceptionIfClassNotImplementsInterface($model_class_name, 'Sportbox\Model\InterfaceSave');

        //
        //
        //

        $new_prop_values_arr = array();
        $reflect = new \ReflectionClass($model_class_name);

        foreach ($reflect->getProperties() as $prop_obj) {
            if (!$prop_obj->isStatic()) { // игнорируем статические свойства класса - они относятся не к объекту, а только к классу (http://www.php.net/manual/en/language.oop5.static.php), и в них хранятся настройки ActiveRecord и CRUD
                $prop_name = $prop_obj->getName();
                if (array_key_exists($prop_name, $_POST)) {
                	// Проверка на заполнение обязательных полей
                	if ( (($_POST[$prop_name] == '') && (\Sportbox\CRUD\Helpers::isRequiredField($model_class_name, $prop_obj->getName())) ) ) {
	                	throw new \Exception('поле ' . $prop_obj->getName() . ' обязательно для заполнения');
                	}
                    $new_prop_values_arr[$prop_name] = $_POST[$prop_name];
                }
            }
        }

        $obj = new $model_class_name;
        $obj = \Sportbox\CRUD\Helpers::setObjectFieldsFromArray($obj, $new_prop_values_arr);

        $obj->save();

        \Sportbox\Logger\Logger::logObjectEvent($obj, 'CRUD создание');
        $redirect_url = \Sportbox\CRUD\ControllerCRUD::getEditUrl($model_class_name, $obj->getId());

        if (array_key_exists('destination', $_POST)){
            $redirect_url = $_POST['destination'];
	        $separator = '?';
	        if (mb_strpos($redirect_url, '?'))
	        {
		        $separator = '&';
	        }
	        $redirect_url .= $separator.'crud_obj_model_class='.urlencode($model_class_name).'&crud_obj_id='.$obj->getId();
        }

        \Sportbox\Helpers::redirect($redirect_url);
    }
    */

    /*
    public function deleteAction($model_class_name, $obj_id)
    {
        //
        // проверка
        //

        \Sportbox\Helpers::exit403If(!\Sportbox\CRUD\Helpers::currentUserHasRightsToEditModel($model_class_name));

        \Sportbox\Helpers::assert($model_class_name);
        \Sportbox\Helpers::assert($obj_id);
        \Sportbox\CRUD\Helpers::exceptionIfClassNotImplementsInterface($model_class_name, 'Sportbox\Model\InterfaceDelete');

        //
        // проверка связанных объектов
        //

        \Sportbox\CRUD\Helpers::exceptionIfClassNotImplementsInterface($model_class_name, 'Sportbox\Model\InterfaceDelete');

        if (property_exists($model_class_name, 'crud_related_models_arr')) {

            foreach ($model_class_name::$crud_related_models_arr as $related_model_class_name => $related_model_data) {
                \Sportbox\Helpers::assert(array_key_exists('link_field', $related_model_data));
                $related_objs_ids_arr = \Sportbox\CRUD\Helpers::getObjIdsArrayForModel($related_model_class_name, array($related_model_data['link_field'] => $obj_id));
                if (count($related_objs_ids_arr) > 0) {
                    throw new \Exception('Related model exists, can\'t delete entity. Delete related entities first.');
                }

            }
        }

        //
        // удаление объекта
        //

        $obj = \Sportbox\CRUD\Helpers::createAndLoadObject($model_class_name, $obj_id);
        $obj->delete();
        \Sportbox\Logger\Logger::logObjectEvent($obj, 'CRUD удаление');

        //
        // редирект
        //

        $redirect_url = '';
        if (array_key_exists('destination', $_GET)) {
            $redirect_url = $_GET['destination'];
        }

        \Sportbox\Helpers::assert($redirect_url);
        \Sportbox\Helpers::redirect($redirect_url);
    }
    */
}