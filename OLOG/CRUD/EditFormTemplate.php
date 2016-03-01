<?php

namespace OLOG\CRUD;

class EditFormTemplate
{
    static public function renderTabs($bubble_key, $requested_tab_key){
        $editor_config_arr = CRUDConfigReader::getEditorConfigForKey($bubble_key);

        // TODO: respect single tab case

        echo '<ul class="nav nav-tabs">';

        foreach ($editor_config_arr as $tab_key => $tab_config){
            $active_class = '';
            if ($tab_key == $requested_tab_key){
                $active_class = 'active';
            }

            echo '<li role="presentation" class="' . $active_class . '"><a href="#">' . $tab_key . '</a></li>';
        }

        echo '</ul>';

        echo '<div>&nbsp;</div>';
    }

    static public function render($bubble_key, $requested_obj_id, $requested_tab_key){

        $editor_config_arr = CRUDConfigReader::getEditorConfigForKey($bubble_key);

        echo '<h2>' . $bubble_key . ' - ' . $requested_obj_id . ' - ' . $requested_tab_key. '</h2>';

        self::renderTabs($bubble_key, $requested_tab_key);

        $tab_config = $editor_config_arr[$requested_tab_key];
        $elements_arr = $tab_config[CRUDConfigReader::CONFIG_KEY_ELEMENTS];

        foreach ($elements_arr as $element_key => $element_config_arr){
            Elements::renderElement($element_config_arr);
        }





        /* TODO
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
        */

//
// форма редактирования объекта
//
        /* TODO
        echo \Sportbox\EditorTabs\Render::renderForObj($obj);

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
        */

//
// вывод приязанных объектов
//

        /* TODO
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
        */
    }
}