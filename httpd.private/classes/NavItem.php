<?php
class NavItem {
    private $icon;
    private $title;
    private $link;

    public function __construct($icon, $title, $link = "#!") {
        $this->icon = $icon;
        $this->title = $title;
        $this->link = $link;
    }

    public function render() {
        return "<div class='col-4'>
                    <a class='d-block hover-bg-200 p-2 rounded-3 text-center text-decoration-none mb-3' href='{$this->link}'>
                        <img src='{$this->icon}' alt='' width='30' />
                        <p class='mb-0 text-black text-truncate fs--2 mt-1 pt-1'>{$this->title}</p>
                    </a>
                </div>";
    }
}

class NavItemList {
    private $items = [];

    public function addItem($icon, $title, $link = "#!") {
        $this->items[] = new NavItem($icon, $title, $link);
    }

    public function render() {
        $items_html = '';
        foreach($this->items as $item) {
            $items_html .= $item->render();
        }
        return "<li class='nav-item dropdown' ='navitem'>
                    <a class='nav-link' id='navbarDropdownNindeDots' href='#' role='button' data-bs-toggle='dropdown' aria-haspopup='true' data-bs-auto-close='outside' aria-expanded='false'>
                    <svg width='16' height='16' viewbox='0 0 16 16' fill='none' xmlns='http://www.w3.org/2000/svg'>
                    <circle cx= '2 ' cy= '2 ' r= '2 ' fill= 'currentColor '></circle>
                    <circle cx= '2 ' cy= '8 ' r= '2 ' fill= 'currentColor '></circle>
                    <circle cx= '2 ' cy= '14 ' r= '2 ' fill= 'currentColor '></circle>
                    <circle cx= '8 ' cy= '8 ' r= '2 ' fill= 'currentColor '></circle>
                    <circle cx= '8 ' cy= '14 ' r= '2 ' fill= 'currentColor '></circle>
                    <circle cx= '14 ' cy= '8 ' r= '2 ' fill= 'currentColor '></circle>
                    <circle cx= '14 ' cy= '14 ' r= '2 ' fill= 'currentColor '></circle>
                    <circle cx= '8 ' cy= '2 ' r= '2 ' fill= 'currentColor '></circle>
                    <circle cx= '14 ' cy= '2 ' r= '2 ' fill= 'currentColor '></circle>
                  </svg>
                    </a>
                    <div class='dropdown-menu dropdown-menu-end navbar-dropdown-caret py-0 dropdown-nide-dots shadow border border-300' aria-labelledby='navbarDropdownNindeDots'>
                        <div class='card bg-white position-relative border-0'>
                            <div class='card-body pt-3 px-3 pb-0 overflow-auto scrollbar' style='height: 20rem;'>
                                <div class='row text-center align-items-center gx-0 gy-0'>
                                    {$items_html}
                                </div>
                            </div>
                        </div>
                    </div>
                </li>";
    }
}
?>
