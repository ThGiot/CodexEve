<?php

class NavigationBar
{
    private $navData;

    public function __construct($navData)
    {
        $this->navData = $navData;
    }

    public function render()
    {
        $html = '<body>';
        $html .= '<main class="main" id="top">';
        $html .= '<nav id="navBar" class="navbar navbar-vertical navbar-expand-lg">';
        $html .= '<div class="collapse navbar-collapse" id="navbarVerticalCollapse">';
        $html .= '<div class="navbar-vertical-content">';
        $html .= '<ul class="navbar-nav flex-column" id="navbarVerticalNav">';

        foreach ($this->navData as $navItem) {
            $html .= '<li class="nav-item">';

            if (isset($navItem['label'])) {
                $html .= '<p class="navbar-vertical-label">' . $navItem['label'] . '</p>';
                $html .= '<hr class="navbar-vertical-line" />';
            }

            foreach ($navItem['pages'] as $page) {
                $html .= '<div class="nav-item-wrapper">';

                if (isset($page['subpages'])) {
                    // Render parent page as a dropdown
                    $html .= '<a class="nav-link dropdown-indicator label-1" href="#nv-' . strtolower(str_replace(' ', '-', $page['name'])) . '" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="nv-' . strtolower(str_replace(' ', '-', $page['name'])) . '">';
                    $html .= '<div class="d-flex align-items-center"><div class="dropdown-indicator-icon"><span class="fas fa-caret-right"></span></div><span class="nav-link-icon"><span data-feather="' . $page['icon'] . '"></span></span><span class="nav-link-text">' . $page['name'] . '</span></div></a>';

                    $html .= '<div class="parent-wrapper label-1"><ul class="nav collapse parent" data-bs-parent="#navbarVerticalCollapse" id="nv-' . strtolower(str_replace(' ', '-', $page['name'])) . '">';
                    $html .= '<li class="collapsed-nav-item-title d-none">' . $page['name'] . '</li>';
                    foreach ($page['subpages'] as $subpage) {
                        $html .= '<li class="nav-item"><a class="nav-link" href="javascript:void(0);" onclick="getContent(\'' . $subpage['link'] . '\')" data-bs-toggle="" aria-expanded="false"><div class="d-flex align-items-center"><span class="nav-link-text">' . $subpage['name'] . '</span></div></a></li>';
                    }
                    $html .= '</ul></div>';

                } else {
                    // Render page as a single link
                    $html .= '<a class="nav-link label-1" href="javascript:void(0);" onclick="getContent(\'' . $page['link'] . '\')" role="button" data-bs-toggle="" aria-expanded="false">';
                    $html .= '<div class="d-flex align-items-center"><span class="nav-link-icon"><span data-feather="' . $page['icon'] . '"></span></span><span class="nav-link-text-wrapper"><span class="nav-link-text">' . $page['name'] . '</span></span></div></a>';
                }

                $html .= '</div>';  // nav-item-wrapper
            }

            $html .= '</li>';  // nav-item
        }
     
        $html .= '</ul>';  // navbar-nav flex-column
        $html .= '</div>';  // navbar-vertical-content
        $html .= '</div>';  // navbar-collapse
        $html .= '<div class="navbar-vertical-footer">';
        $html .= '<button class="btn navbar-vertical-toggle border-0 fw-semi-bold w-100 white-space-nowrap d-flex align-items-center"><span class="uil uil-left-arrow-to-left fs-0"></span><span class="uil uil-arrow-from-right fs-0"></span><span class="navbar-vertical-footer-text ms-2">Collapsed View</span></button>';
        $html .= '</div>';  // navbar-vertical-footer
        $html .= '</nav>';  // navbar navbar-vertical navbar-expand-lg

        return $html;
    }
}
?>
