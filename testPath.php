<?php 
$path = 'C:\AppServ\www\WebCalendar1\src';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

echo ini_get('include_path');

?>