<?php $this->extend('default');

$this->section('content');

$attrs = [
	'num' => 45,
	'name' => "hphn doe"
];

$clubret = new \App\Entities\Clubret($attrs);
d($clubret);
d($clubret->num);


$entry = new \App\Entities\Entry($attrs);
d($entry);
# d($entry->num);


$this->endSection();

$this->section('top'); ?>

<?php $this->endSection();