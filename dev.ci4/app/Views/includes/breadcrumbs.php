<?php 
if(empty($breadcrumbs)) return;

$nav = new \App\Views\Htm\Breadcrumbs;
echo $nav->htm($breadcrumbs);