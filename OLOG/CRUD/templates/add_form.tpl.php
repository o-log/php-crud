<?php

$context_arr = array();
if (array_key_exists('context_arr', $_GET)) {
    $context_arr = $_GET['context_arr'];
}

$obj = new $model_class_name;

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

$context_arr_fields = $context_arr;

?>
<form id="form" role="form" method="post" style="background-color: #eee; padding: 10px; border-radius: 2px;"
      class="form-horizontal" action="/crud/create/<?php echo urlencode($model_class_name) ?>">

    <?php foreach ($props_arr as $prop_obj):
        $editor_title = \Sportbox\CRUD\Helpers::getTitleForField($model_class_name, $prop_obj->getName());
        $value = $prop_obj->getValue($obj);
        if (array_key_exists($prop_obj->getName(), $context_arr_fields)) {
            $value = $context_arr_fields[$prop_obj->getName()];
            unset($context_arr_fields[$prop_obj->getName()]);
        }
        $required = \Sportbox\CRUD\Helpers::isRequiredField($model_class_name, $prop_obj->getName());
        $editor_description = \Sportbox\CRUD\Helpers::getDescriptionForField($model_class_name, $prop_obj->getName());
        ?>
        <div class="form-group <?=( ($required) ? 'required' : '' )?>">
            <label class="col-sm-4 text-right control-label"
                   for="<?php echo $prop_obj->getName() ?>"><?php echo $editor_title ?></label>

            <div class="col-sm-8">
                <?php
                echo \Sportbox\CRUD\Widgets::renderFieldWithWidget($prop_obj->getName(), $obj, $value);

                if ($editor_description) {
                    ?>
                    <span class="help-block">
                        <?= $editor_description ?>
                    </span>
                <?php } ?>
            </div>
        </div>
    <?php endforeach; ?>
    <?php foreach ($context_arr_fields as $field_name => $field_value): ?>
        <input type="hidden" name="<?php echo $field_name ?>" value="<?php echo $field_value ?>">
    <?php endforeach ?>
    <?php
    if (array_key_exists("destination_url", $_REQUEST)) {
        echo '<input type="hidden" name="destination" value="' . $_REQUEST["destination_url"] . '">';
    }
    ?>
    <div class="row">
        <div class="col-sm-8 col-sm-offset-4">
            <button type="submit" class="btn btn-default" style="width: 100%">Сохранить</button>
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