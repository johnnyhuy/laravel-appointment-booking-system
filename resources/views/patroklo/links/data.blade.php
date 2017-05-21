<?php
    if (array_key_exists('data', $attributes) == false){
        $attributes['data'] = [];
    }
    elseif (!is_array($attributes['data'])){
        $attributes['data'] = [$attributes['data']];
    }
    $data_attributes = $attributes['data'];
    unset($attributes['data']);

    if (array_key_exists('class', $attributes) != false){
        if(!is_array($attributes['class'])){
            $attributes['class'].= ' method-link';
        }
        else{
            $attributes['class'][] = 'method-link';
        }
    }
?>
{{ Html::link($url, $title,
            array_merge([
                          'data-method' => $method,
                          'data-csrf' => csrf_token(),
                          'class' => 'method-link',
                          'data-attributes' => json_encode($data_attributes)
                        ], $attributes),
            $secure, $escape)
}}