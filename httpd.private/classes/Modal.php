<?php
/*
$modal = new Modal(
    id: "WarningLastDetail", 
    title: "Attention", 
    body: "Si vous supprimer tous les détails d'une facture, la facture sera supprimée.",
    headerClass: "warning",
    okayButtonClass: "warning",
    okayButtonText : "Je confirme",
    cancelButtonClass: "outline-secondary",
    showOkayButton: true,
    showButton : true
);
$modal -> setOkayButtonOnClick("alert('ok');");
echo $modal->render();
*/
class Modal {
    private $id;
    private $title;
    private $body;
    private $showButton;
    private $okayButtonOnClick;
    private $okayButtonText;
    private $cancelButtonText;
    private $headerClass;
    private $okayButtonClass;
    private $cancelButtonClass;
    private $showOkayButton;
    private $size; 

    public function __construct($id, $title, $body, $showButton = true, $okayButtonText = "Okay", $cancelButtonText = "Cancel", $headerClass = "primary", $okayButtonClass = "primary", $cancelButtonClass = "outline-primary", $showOkayButton = true,$size = "") {
        $this->id = $id;
        $this->title = $title;
        $this->body = $body;
        $this->showButton = $showButton;
        $this->okayButtonText = $okayButtonText;
        $this->cancelButtonText = $cancelButtonText;
        $this->headerClass = $headerClass;
        $this->okayButtonClass = $okayButtonClass;
        $this->cancelButtonClass = $cancelButtonClass;
        $this->showOkayButton = $showOkayButton;
        $this->size = $size;
    }

    public function setOkayButtonOnClick($onClick) {
        $this->okayButtonOnClick = $onClick;
    }

    public function render() {
        $button = $this->showButton ? "<button class=\"btn btn-$this->headerClass\" type=\"button\" data-bs-toggle=\"modal\" data-bs-target=\"#$this->id\">$this->title</button>" : "";

        $okayButton = $this->showOkayButton ? ($this->okayButtonOnClick ? "<button class=\"btn btn-$this->okayButtonClass\" type=\"button\" id=\"{$this->id}OkayButton\" onclick=\"$this->okayButtonOnClick\">$this->okayButtonText</button>" : "<button class=\"btn btn-$this->okayButtonClass\" id=\"{$this->id}OkayButton\" type=\"button\">$this->okayButtonText</button>") : "";

        $cancelButton = "<button class=\"btn id=\"{$this->id}CancelButton\" btn-$this->cancelButtonClass\" type=\"button\" data-bs-dismiss=\"modal\">$this->cancelButtonText</button>";
        $modalSizeClass = $this->size ? "modal-$this->size" : "";
        $modalDialogClass = "modal-dialog modal-dialog-centered $modalSizeClass";
        return 
            $button .
            "<div class=\"modal fade\" id=\"$this->id\" tabindex=\"-1\" aria-labelledby=\"{$this->id}ModalLabel\" aria-hidden=\"true\">
                <div class=\"$modalDialogClass\">
                    <div class=\"modal-content\">
                        <div class=\"modal-header bg-$this->headerClass\">
                            <h5 class=\"modal-title\" id=\"{$this->id}Title\">$this->title</h5>
                            <button class=\"btn p-1\" type=\"button\" data-bs-dismiss=\"modal\" aria-label=\"Close\"><span class=\"fas fa-times fs--1\"></span></button>
                        </div>
                        <div class=\"modal-body\">
                            <p class=\"text-700 lh-lg mb-0\" id=\"{$this->id}Body\">$this->body</p>
                        </div>
                        <div class=\"modal-footer\">
                            $okayButton
                            $cancelButton
                        </div>
                    </div>
                </div>
            </div>";
    }
}


?>