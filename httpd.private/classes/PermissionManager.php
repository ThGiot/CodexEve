<?php
class PermissionManager {
    private $role;
    private $page;

    public function __construct($role, $page = null) {
        $this->role = $role;
        $this->page = $page;
    }

    public function getPermissionArray() {
        return [$this->role, $this->page];
    }

    public function canAccess($allowedRoles, $pageId) {
        foreach ($allowedRoles as $role) {
            if ($this->role == $role && $this->page == $pageId) {
                return true;
            }
        }
        return false;
    }
}
