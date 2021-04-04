<?php
$methods = [
	'submitAmbassador' => [
		'params' => [
			[
				'name' => 'firstname',
				'source' => 'p',
				'pattern' => 'pat_name',
				'required' => true
			],
			[
				'name' => 'secondname',
				'source' => 'p',
				'pattern' => 'pat_name',
				'required' => true
			],
			[
				'name' => 'position',
				'source' => 'p',
				'required' => false,
				'default' => ''
			],
			[
				'name' => 'phone',
				'source' => 'p',
				'pattern' => 'pat_ukr_number',
				'required' => true
			],
		]
	]
];