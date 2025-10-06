<?php

class Notification {
    private $user;
    private $avatar;
    private $status;
    private $message;
    private $time;

    public function __construct($user, $avatar, $status, $message, $time) {
        $this->user = $user;
        $this->avatar = $avatar;
        $this->status = $status;
        $this->message = $message;
        $this->time = $time;
    }

    public function render() {
        return "
        <div class='px-2 px-sm-3 py-3 border-300 notification-card position-relative {$this->status} border-bottom'>
            <div class='d-flex align-items-center justify-content-between position-relative'>
                <div class='d-flex'>
                    <div class='avatar avatar-m status-online me-3'>
                        <img class='rounded-circle' src='{$this->avatar}' alt='' />
                    </div>
                    <div class='flex-1 me-sm-3'>
                        <h4 class='fs--1 text-black'>{$this->user}</h4>
                        <p class='fs--1 text-1000 mb-2 mb-sm-3 fw-normal'>{$this->message}<span class='ms-2 text-400 fw-bold fs--2'>10m</span></p>
                        <p class='text-800 fs--1 mb-0'><span class='me-1 fas fa-clock'></span><span class='fw-bold'>{$this->time} </span>August 7,2021</p>
                    </div>
                </div>
                <div class='font-sans-serif d-none d-sm-block'>
                    <button class='btn fs--2 btn-sm dropdown-toggle dropdown-caret-none transition-none notification-dropdown-toggle' type='button' data-bs-toggle='dropdown' data-boundary='window' aria-haspopup='true' aria-expanded='false' data-bs-reference='parent'><span class='fas fa-ellipsis-h fs--2 text-900'></span></button>
                    <div class='dropdown-menu dropdown-menu-end py-2'><a class='dropdown-item' href='#!'>Mark as unread</a></div>
                </div>
            </div>
        </div>";
    }
}

class NotificationList {
    private $notifications = [];

    public function addNotification($user, $avatar, $status, $message, $time) {
        $this->notifications[] = new Notification($user, $avatar, $status, $message, $time);
    }

    public function render() {
        $output = "
        <li class='nav-item dropdown'>
            <a class='nav-link' href='#' style='min-width: 2.5rem' role='button' data-bs-toggle='dropdown' aria-haspopup='true' aria-expanded='false' data-bs-auto-close='outside'><span data-feather='bell' style='height:20px;width:20px;'></span></a>
            <div class='dropdown-menu dropdown-menu-end notification-dropdown-menu py-0 shadow border border-300 navbar-dropdown-caret' id='navbarDropdownNotfication' aria-labelledby='navbarDropdownNotfication'>
                <div class='card position-relative border-0'>
                    <div class='card-header p-2'>
                        <div class='d-flex justify-content-between'>
                            <h5 class='text-black mb-0'>Notifications</h5>
                            <button class='btn btn-link p-0 fs--1 fw-normal' type='button'>Mark all as read</button>
                        </div>
                    </div>
                    <div class='card-body p-0'>
                        <div class='scrollbar-overlay' style='height: 27rem;'>
                            <div class='border-300'>";

        foreach ($this->notifications as $notification) {
            $output .= $notification->render();
        }

        $output .= "
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </li>";

        return $output;
    }
}


?>
