<?php
class BootstrapCard {
    private $title;
    private $body;
    private $footer;
    private $header;
    private $classes;
    private $id;

    public function __construct($id = '', $classes = '') {
        $this->id = $id;
        $this->classes = $classes;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function setBody($body) {
        $this->body = $body;
    }

    public function setFooter($footer) {
        $this->footer = $footer;
    }

    public function setHeader($header) {
        $this->header = $header;
    }

    public function render() {
        $card = '<div class="card ' . $this->classes . '" id="' . $this->id . '">';
        
        if (!empty($this->header)) {
            $card .= '<div class="card-header">' . $this->header . '</div>';
        }

        if (!empty($this->title) || !empty($this->body)) {
            $card .= '<div class="card-body">';
            if (!empty($this->title)) {
                $card .= '<h5 class="card-title">' . $this->title . '</h5>';
            }
            if (!empty($this->body)) {
                $card .= '<p class="card-text">' . $this->body . '</p>';
            }
            $card .= '</div>';
        }

        if (!empty($this->footer)) {
            $card .= '<div class="card-footer">' . $this->footer . '</div>';
        }

        $card .= '</div>';
        return $card;
    }
}

?>
