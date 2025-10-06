<?php
require PRIVATE_PATH . '/classes/Notification.php';
$notificationList = new NotificationList();
//$notificationList->addNotification('John Doe', 'assets/img/team/40x40/1.webp', 'unread', 'A new message', '10:41 AM');
echo $notificationList->render();
?>