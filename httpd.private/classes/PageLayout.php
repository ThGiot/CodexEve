
<?php
class PageLayout {
    private $elements = [];
    private $rowGroups = [];

    public function addElement($content, $colSize = 12, $groupId = null) {
        $element = [
            'content' => $content,
            'colSize' => $colSize,
        ];

        if ($groupId !== null) {
            $this->rowGroups[$groupId][] = $element;
        } else {
            $this->elements[] = $element;
        }
    }

    public function startRowGroup($groupId) {
        if (!isset($this->rowGroups[$groupId])) {
            $this->rowGroups[$groupId] = [];
        }
    }

    public function render($narrow = false) {
        $output = '<div class="content"><div class="mb-9">';

        // Vérifie s'il y a des groupes. Si oui, utilisez la nouvelle logique de rendu.
        if (!empty($this->rowGroups)) {
            foreach ($this->rowGroups as $group) {
                $output .= '<div class="row g-5">';
                foreach ($group as $element) {
                    $output .= '<div class="col-12 col-xxl-' . $element['colSize'] . '">';
                    $output .= $element['content'];
                    $output .= '</div>';
                }
                $output .= '</div>';
            }
        } else {
            // Sinon, utilisez la logique de rendu existante.
            $output .= '<div class="row g-5">';
            
            foreach ($this->elements as $element) {
                $output .= '<div class="col-12 col-xxl-' . $element['colSize'] . '">';
                $output .= '<div class="row g-3 g-xxl-0 h-100">';
                $output .= $element['content'];
                $output .= '</div></div>';
                if($narrow == false) $output .= '</div></div>';
            }

            $output .= '</div>'; // Fermeture de row
        }
        $output .= '</div></div>'; // Fermeture de content et mb-9

        return $output;
    }
}


?>