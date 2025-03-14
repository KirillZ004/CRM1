<?php
function inputDefaultValue($field,$defaultValue){
    if(isset($_GET[$field])){
        $input = !empty($_GET[$field]) ? $_GET[$field] : $defaultValue;
        echo "value='$input'"; 
    }else{
        echo '';
    }
};
function selectDefaultValue($field,$options,$defaultValue){


    foreach($options as $key => $option){
        $key = $option['key'];
        $value = $option['value'];
        $selected = '';
        if(isset($_GET[$field]) && $_GET[$field] == $key){
            $selected = 'selected';
    }
    if(isset($_GET[$field]) && $key === $defaultValue){
        $selected = 'selected';
}
    echo "<option $selected value='$key'>$value</option>";

};
}
?>