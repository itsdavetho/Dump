<?php
function dump($data, $maxNests = 3, $nest = 1, $lastKey = -1) {
    $br = '<br />';
    if($nest == 1) {
        echo '<pre class="php-dump">';
    }
    $colors = array(
        'integer' => '#4e9a06',
        'string' => '#cc0000',
        'array' => '',
        'double' => '#f57900',
        'boolean' => '#75507b',
        'object' => '',
        'resource' => '',
        'null' => '#3465a4'
    );
    $parse = function($value) use($colors) {
        $type = gettype($value);
        $type = strtolower($type);
        $span = '<span style="color: ' . $colors[$type] . '">%s</span>';
        switch($type) {
            case 'null':
                $value = sprintf($span, 'null');
                break;
            case 'string':
                $len = strlen($value);
                if($len > 512) {
                    $value = substr($value, 0, 512);
                }
                $value = htmlentities($value);
                $value = '<span style="font-size: 85%">' . $type . '</span> '
                         . sprintf($span, "'$value'" . ($len > 512 ? '...' : ''))
                         . ' (<span style="font-style: italic">length=' . $len . '</span>)';
                break;
            case 'boolean':
                $value = '<span style="font-size: 85%">' . $type . '</span> '
                         . sprintf($span, ($value === true ? 'true' : 'false'));
                break;
            case 'object':
                $value = sprintf($span, '<span style="font-weight:bold">object</span><span style="font-style:italic">('
                                        . get_class($value) . ')[' . spl_object_hash($value) . ']</span>');
                break;
            case 'resource':
                $value = sprintf($span, '<span style="font-weight:bold">resource</span>(<span style="font-style: italic">'
                                        . intval($value) . ', ' . get_resource_type($value) . '</span>)');
                break;
            default:
                if(is_scalar($value)) {
                    $value = '<span style="font-size: 85%">' . $type . '</span> ' . sprintf($span, $value);
                } else {
                    $value = sprintf($span, $type);
                }
                break;
        }
        return $value;
    };
    if(is_array($data)) {
        $size = count($data);
        $spaces = str_repeat('  ', $nest);
        echo '<span style="font-weight: bold">array</span> (<span style="font-style: italic">size='
             . $size . '</span>)' . $br;
        foreach($data as $key => $val) {
            if(is_string($key)) {
                $key = "'$key'";
            }
            if(is_array($val)) {
                if($nest >= $maxNests) {
                    $aSize = count($val);
                    echo $spaces . $key . ' => ' . $br
                         . $spaces . '<span style="font-weight: bold">array</span>(<span style="font-style: italic">size='
                         . $aSize . '</span>) ...' . $br;
                    continue;
                }
                echo ($key > -1 ? $spaces . $key . ' => ' . $br
                     . $spaces : $spaces);
                dump($val, $maxNests, $nest + 1, $key);
            } else {
                $val = $parse($val);
                echo $spaces . $key . ' => ' . $val . $br;
            }
        }
    } else {
        echo $parse($data) . $br;
    }
    if($nest == 1) {
        echo '</pre>';
    }
}
