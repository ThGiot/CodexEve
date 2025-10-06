<?php
class Toolbar {
    private $buttons = [];

    public function addButton($class, $onClick, $icon, $label) {
        $this->buttons[] = [
            'class' => $class,
            'onClick' => $onClick,
            'icon' => $icon,
            'label' => $label
        ];
    }

    public function render() {
        $output = '<div class="row align-items-center justify-content-between g-3 mb-4">';
        $output .= '<div class="col-auto"></div>'; // Espace vide dans l'exemple, peut être personnalisé si nécessaire
        $output .= '<div class="col-auto"><div class="row g-3">';

        foreach ($this->buttons as $button) {
            $output .= '<div class="col-auto">';
            $output .= '<button class="btn ' . $button['class'] . '"';
            if (!empty($button['onClick'])) {
                $output .= ' onclick="' . $button['onClick'] . '"';
            }
            $output .= '>';
            if (!empty($button['icon'])) {
                $output .= '<span class="' . $button['icon'] . ' me-2"></span>';
            }
            $output .= $button['label'];
            $output .= '</button>';
            $output .= '</div>';
        }

        $output .= '</div></div></div>'; // Fermeture des divs

        return $output;
    }
}


?>