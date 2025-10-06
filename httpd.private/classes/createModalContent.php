<?php
require_once 'Modal.php'; 
function createModalContent($formId, $inputs, $modalId, $modalTitle, $onClickAction = '', $size='') {

    $body = '<form id="'.$formId.'">';
    $form = new Form("monFormulaire", "monFormulaire", "post", "traitement.php", "Mon Formulaire");

    foreach ($inputs as $input) {
        $input_name = $input['name'];
        $body .= ucfirst($input_name) . ':';
        $body .= $form->renderSingleField(
            type: $input['type'],
            id: $input['prefix'] . $input_name,
            name: $input_name,
            placeholder: $input_name
        );
        $body .= '</br>';
    }

    $body .= '</br></form>';

    $modal = new Modal(
        id: $modalId, 
        title: $modalTitle, 
        body: $body,
        headerClass: "",
        okayButtonClass: "primary",
        okayButtonText : "Enregistrer",
        cancelButtonClass: "outline-secondary",
        showOkayButton: true,
        showButton : false,
        size : $size
    );

    if ($onClickAction != '') {
        $modal->setOkayButtonOnClick($onClickAction);
    }

    echo $modal->render();
}
?>