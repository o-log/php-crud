<?php
/**
 * @var $obj object
 */

\Sportbox\Helpers::assert($obj);
$model_class_name = get_class($obj);

$reflect = new \ReflectionClass($model_class_name);

$props_arr = array();

foreach ($reflect->getProperties() as $prop_obj) {
    if (!$prop_obj->isStatic()) { // игнорируем статические свойства класса - они относятся не к объекту, а только к классу (http://www.php.net/manual/en/language.oop5.static.php), и в них хранятся настройки ActiveRecord и CRUD
        $prop_obj->setAccessible(true);
        $props_arr[] = $prop_obj;
    }
}

$crud_editor_fields_arr = \Sportbox\CRUD\Helpers::getCrudEditorFieldsArrForClass($model_class_name);
if ($crud_editor_fields_arr) {
    foreach ($props_arr as $delta => $property_obj) {
        if (!array_key_exists($property_obj->getName(), $crud_editor_fields_arr)) {
            unset($props_arr[$delta]);
        }
    }
}

//
// форма редактирования объекта
//
echo \Sportbox\EditorTabs\Render::renderForObj($obj);

/*
if (property_exists($model_class_name, 'crud_extra_tabs') && count($model_class_name::$crud_extra_tabs) > 0) {
    ?>
    <ul class="nav nav-tabs" role="tablist">
        <?php
        foreach ($model_class_name::$crud_extra_tabs as $tab_pathname => $tab_title) {
            //$tab_url = $tab_pathname.'?full_object_id='.\Sportbox\Helpers::getFullObjectId($obj);
            $tab_pathname = str_replace('#MODEL_ID#', $obj->getId(), $tab_pathname);
            $tab_pathname = str_replace('#MODEL_CLASS_NAME#', urlencode($model_class_name), $tab_pathname);

            $li_class = '';
            if ($tab_pathname == \SportboxRu\Helpers::uri_no_getform()){
                $li_class .= ' active ';
            }

            echo '<li class="' . $li_class . '"><a href="' . $tab_pathname . '">'.$tab_title.'</a></li>';
        }
        ?>
    </ul>
<?php
}
*/

if ($obj instanceof \Sportbox\Model\InterfaceSave) {
    ?>

    <div class="spb_admin_section">

        <form id="form" style="background-color: #eee; padding: 10px; border-radius: 2px;" class="form-horizontal" role="form"
              method="post"
              action="/crud/save/<?php echo urlencode($model_class_name) ?>/<?php echo $obj->getId(); ?>">
            <?php
            foreach ($props_arr as $prop_obj) {
                $editor_title = \Sportbox\CRUD\Helpers::getTitleForField($model_class_name, $prop_obj->getName());
                $editor_description = \Sportbox\CRUD\Helpers::getDescriptionForField($model_class_name, $prop_obj->getName());
                $required = \Sportbox\CRUD\Helpers::isRequiredField($model_class_name, $prop_obj->getName());
                ?>
                <div class="form-group <?=( ($required) ? 'required' : '' )?>">
                    <label for="<?php echo $prop_obj->getName() ?>"
                           class="col-sm-4 text-right control-label"><?= $editor_title ?></label>

                    <div class="col-sm-8">
                        <?php
                        echo \Sportbox\CRUD\Widgets::renderFieldWithWidget($prop_obj->getName(), $obj);

                        if ($editor_description) {
                            ?>
                            <span class="help-block">
                            <?= $editor_description ?>
                        </span>
                        <?php } ?>
                    </div>
                </div>

            <?php } ?>
            <?php
            if (array_key_exists("destination_url", $_REQUEST)) {
	            echo '<input type="hidden" name="destination" value="' . $_REQUEST["destination_url"] . '">';
            }
            ?>
            <div class="row">
                <div class="col-sm-8 col-sm-offset-4">
                    <button style="width: 100%" type="submit" class="btn btn-primary">Сохранить</button>
                </div>
            </div>
        </form>
<script>
$('#form').on('submit', function(e) {
	$(this).find('.required').removeClass('has-error').each(function() {
		if ($(this).find('input, textarea, select').val() === '') {
			$(this).addClass('has-error');
		}
	});
	
	if ($(this).find('.required').is('.has-error')) {
		alert('Заполните обязательные поля!');
		e.preventDefault();
	}
});
</script>
    </div>

<?php
}

//
// вывод приязанных объектов
//

if (property_exists($model_class_name, 'crud_related_models_arr')) {
    foreach ($model_class_name::$crud_related_models_arr as $related_model_class_name => $related_model_data) {
        $list_title = "Связанные данные " . $related_model_class_name;
        if (!is_array($related_model_data)) { // старая форма связи, потом удалить
            $relation_field_name = $related_model_data;
        } else {
            \Sportbox\Helpers::assert(array_key_exists('link_field', $related_model_data));
            $relation_field_name = $related_model_data['link_field'];

            if (array_key_exists('list_title', $related_model_data)) {
                $list_title = $related_model_data['list_title'];
            }
        }

        echo \Sportbox\Render::template2('Sportbox/CRUD/templates/list.tpl.php', array(
            'model_class_name' => $related_model_class_name,
            'context_arr' => array($relation_field_name => $obj->getId()),
            'list_title' => $list_title
        ));
    }
}
