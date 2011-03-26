<?php

$config  = array(
	//default recursive
	'd_recurs'		=> true,

	'upload'	=> array(
		'path'		=> 'files',
		'types'		=> 'jpe?g|png',
		'max_size'	=> 9000000,
		'max_height'	=> 5000,
		'max_width'	=> 5000,
		'min_height'	=> 10,
		'min_width'	=> 10,
		'overwrite'	=> false,
		'rand_name'	=> false,
	),
);