<?php
function dump($data, $nest = 1, $lastKey = -1) {
	$newLine = '<br />';
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
		'resource' => ''
	);
	$parse = function($value) use($colors) {
		$type = gettype($value);
		$span = '<span style="color: ' . $colors[$type] . '">%s</span>';
		switch($type) {
			case 'string':
				$len = strlen($value);
				if($len > 512) {
					$value = substr($value, 0, 512);
				}
				$value = htmlentities($value);
				$value = '<span style="font-size: 85%">' . $type . '</span> ' . sprintf($span, "'$value'" . ($len > 512 ? '...' : '')) . ' (<span style="font-style: italic">length=' . $len . '</span>)';
				break;
			case 'boolean':
				$value = '<span style="font-size: 85%">' . $type . '</span> ' . sprintf($span, ($value === true ? 'true' : 'false'));
				break;
			case 'object':
				$value = sprintf($span, '<span style="font-weight:bold">object</span><span style="font-style:italic">(' . get_class($value) . ')[' . spl_object_hash($value) . ']</span>');
				break;
			case 'resource':
				$value = sprintf($span, '<span style="font-weight:bold">resource</span>(<span style="font-style: italic">' . intval($value) . ', ' . get_resource_type($value) . '</span>)');
				break;
			default:
				if((!is_array($value)) && ((!is_object($value) && settype( $value, 'string' ) !== false ) ||
					(is_object($value) && method_exists( $value, '__toString')))) {
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
		$nestSpaces = str_repeat(' ', $nest * 2);
		$nestSpaces2 = str_repeat(' ', $nest);
		echo ($nest < 3 ? $nestSpaces2 : $nestSpaces) . ($lastKey > -1 ? $lastKey . ' => ' . $newLine . $nestSpaces : '') . '<span style="font-weight: bold">array</span> (<span style="font-style: italic">size=' . $size . '</span>)' . $newLine;
		foreach($data as $key => $val) {
			if(is_array($val)) {
				if($nest >= 3) {
					$aSize = count($val);
					echo $nestSpaces . '  ' . $key . ' => ' . $newLine . $nestSpaces . '    <span style="font-weight: bold">array</span>(<span style="font-style: italic">size=' . $aSize . '</span>) ...' . $newLine;
					continue;
				}
				dump($val, $nest + 1, $key);
			} else {
				$val = $parse($val);
				if(is_string($key)) {
					$key = "'$key'";
				}
				echo $nestSpaces . ($nest < 2 ? '' : '  ') . $key . ' => ' . $val . $newLine;
			}
		}
	} else {
		$type = gettype($data);
		echo $parse($data) . $newLine;
	}
	if($nest == 1) {
		echo '</pre>';
	}
}
