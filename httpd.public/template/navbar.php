  
<?php
require_once PRIVATE_PATH . '/classes/NavigationBar.php';
$navData = [];
$navbar = new NavigationBar($navData);
echo $navbar->render();
?>