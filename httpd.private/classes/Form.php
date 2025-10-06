<?php

/**
 * Classe Form : Permet de créer et de gérer des formulaires HTML.
 * 
 * Utilisation :
 * 1. Créez une instance de la classe Form :
 *    $form = new Form($id, $name, $method, $action, $title);
 *    
 * 2. Ajoutez des champs au formulaire avec les méthodes appropriées :
 *    $form->addField($type, $id, $name, $label, $value = '', $placeholder = '', $options = []);
 *    - $type : Le type d'input ('text', 'email', 'password', 'textarea', etc.)
 *    - $id : L'ID du champ
 *    - $name : Le nom du champ
 *    - $label : Le texte du label associé au champ
 *    - $value : La valeur par défaut du champ (facultatif)
 *    - $placeholder : Le placeholder du champ (facultatif)
 *    - $options : Un tableau associatif d'options supplémentaires (facultatif)
 *    
 *    Vous pouvez également ajouter un champ date, un champ time ou un champ datetime avec :
 *    $form->datePicker($id, $name, $label, $value = '', $placeholder = '', $options = []);
 *    $form->addTimePicker($id, $name, $label, $value = '', $placeholder = '');
 *    $form->addDateTimePicker($id, $name, $label, $value = '', $placeholder = '');
 *    
 * 3. Définissez un bouton de soumission avec la méthode setSubmitButton :
 *    $form->setSubmitButton($id, $name, $value, $text);
 *    
 * 4. Générez le HTML du formulaire avec la méthode render :
 *    echo $form->render();
 */



class Form {
    private $id;
    private $name;
    private $method;
    private $action;
    private $title;
    private $fields = [];
    private $submitButton = ['id' => '', 'name' => '', 'value' => '', 'text' => 'Submit'];
    private $enctype = '';

    public function __construct($id="", $name="", $method="", $action="", $title="") {
        $this->id = $id;
        $this->name = $name;
        $this->method = $method;
        $this->action = $action;
        $this->title = $title;
    }

    public function setEnctype($enctype) {
        $this->enctype = $enctype;
    }

    public function addField($type, $id, $name, $label, $value = '', $placeholder = '', $options = [], $group = null, $selectedValue = null) {
        $fieldData = [
            'type' => $type,
            'id' => $id,
            'name' => $name,
            'label' => $label,
            'value' => $value,
            'selectedValue' => $selectedValue,
            'placeholder' => $placeholder,
            'options' => $options
        ];
        
        if ($group !== null) {
            if (!isset($this->fields[$group]) || !is_array($this->fields[$group])) {
                $this->fields[$group] = [];
            }
            $this->fields[$group][] = $fieldData;
        } else {
            $this->fields[] = $fieldData;
        }
    }

    public function setSubmitButton($id, $name, $value, $text) {
        $this->submitButton = ['id' => $id, 'name' => $name, 'value' => $value, 'text' => $text];
    }

    public function render() {
        $form = '<div class="card shadow-none border border-300 mb-3" data-component-card="data-component-card">';
        $form .= '<div class="card-header p-4 border-bottom border-300 bg-soft"><div class="row g-3 justify-content-between align-items-end"><div class="col-12 col-md">';
        $form .= '<h4 class="text-900 mb-0" data-anchor="data-anchor">' . $this->title . '</h4>';
        $form .= '<p class="mb-0 mt-2 text-800"></p></div><div class="col col-md-auto"></div></div></div>';
        $form .= '<div class="p-4">';
        $form .= '<form id="' . $this->id . '" name="' . $this->name . '" method="' . $this->method . '" onsubmit="' . $this->action . '"';
        if ($this->enctype) {
            $form .= ' enctype="' . $this->enctype . '"';
        }
        $form .= '>';
        
        foreach ($this->fields as $key => $field) {
            if (is_int($key)) {
                // Rendu normal
                $form .= $this->renderField($field);
            } else {
                // C'est un groupe
                $form .= '<div class="row g-3 mb-5">';
                foreach ($field as $groupedField) {
                    $form .= '<div class="col-12 col-lg-6">' . $this->renderField($groupedField) . '</div>';
                }
                $form .= '</div>';
            }
        }

        if (!empty($this->submitButton['id'])) {
            $form .= '<button class="btn btn-primary" type="submit" id="' . $this->submitButton['id'] . '" name="' . $this->submitButton['name'] . '" value="' . $this->submitButton['value'] . '">' . $this->submitButton['text'] . '</button>';
        }        
        $form .= '</form></div></div>';

        return $form;
    }
    private function renderField($field) {
        $output = '<div class="mb-3">';
        $output .= '<label class="form-label" for="' . $field['id'] . '">' . $field['label'] . '</label>';
    
        if ($field['type'] === 'select') {
            $output .= $this->generateSelectField($field);
        } elseif ($field['type'] === 'searchable-select') {
            $output .= $this->generateSearchableSelectField($field);
        } elseif ($field['type'] === 'textarea') {
            $output .= $this->generateTextareaField($field);
        } else {
            $output .= $this->generateInputField($field);
        }
    
        $output .= '</div>';
        return $output;
    }
    
    private function generateSearchableSelectField($field) {
        $select = '<select class="form-select searchable-select" id="' . $field['id'] . '" name="' . $field['name'] . '" data-choices>';
    
        // Placeholder en première option si nécessaire
        if (!empty($field['placeholder'])) {
            $select .= '<option value="" disabled selected>' . htmlspecialchars($field['placeholder']) . '</option>';
        }
    
        // Ajout des options
        foreach ($field['options'] as $option) {
            $selected = ($field['selectedValue'] == $option['value']) ? ' selected' : '';
            $select .= '<option value="' . htmlspecialchars($option['value']) . '"' . $selected . '>' . htmlspecialchars($option['text']) . '</option>';
        }
    
        $select .= '</select>';
        return $select;
    }

    
    private function generateInputField($field) {
    $input = '<input class="form-control';
    if (isset($field['options']['class'])) {
        $input .= ' ' . $field['options']['class'];
        unset($field['options']['class']);
    }
    // Utilisez le type défini dans la configuration du champ.
    $input .= '" id="' . $field['id'] . '" name="' . $field['name'] . '" type="' . $field['type'] . '" value="' . $field['value'] . '" placeholder="' . $field['placeholder'] . '"';
    
    // Ajouter les attributs supplémentaires
    foreach ($field['options'] as $key => $value) {
        $input .= ' ' . $key . '="' . $value . '"';
    }
    $input .= '>';
    return $input;
}

    
    public function addHtml($html) {
        $this->fields[] = [
            'type' => 'html',
            'content' => $html
        ];
    }

    private function generateSelectField($field) {
        $select = '<select class="form-select" id="' . $field['id'] . '" name="' . $field['name'] . '" aria-label="Default select example">';
        foreach ($field['options'] as $option) {
            $selected = ($field['selectedValue'] == $option['value']) ? ' selected' : '';
            $select .= '<option value="' . $option['value'] . '"' . $selected . '>' . $option['text'] . '</option>';
        }
        $select .= '</select>';
        return $select;
    }
    public function addSearchableSelectField($id, $name, $label, $options = [], $selectedValue = null, $placeholder = "Sélectionner une option") {
        $fieldData = [
            'type' => 'searchable-select', // Nouveau type pour différencier
            'id' => $id,
            'name' => $name,
            'label' => $label,
            'options' => $options,
            'selectedValue' => $selectedValue,
            'placeholder' => $placeholder
        ];
    
        $this->fields[] = $fieldData;
    }
    
    private function generateTextareaField($field) {
        $textarea = '<textarea class="form-control" id="' . $field['id'] . '" name="' . $field['name'] . '" rows="3"';
        
        // Ajouter les attributs supplémentaires
        foreach ($field['options'] as $key => $value) {
            $textarea .= ' ' . $key . '="' . $value . '"';
        }
    
        $textarea .= '>' . $field['value'] . '</textarea>';
        return $textarea;
    }
    

    public function addInput($type, $id, $name, $label, $value = '', $placeholder = '', $options = []) {
        $this->fields[] = [
            'type' => $type,
            'id' => $id,
            'name' => $name,
            'label' => $label,
            'value' => $value,
            'placeholder' => $placeholder,
            'options' => $options
        ];
    }

    public function renderSingleField($type, $id, $name, $value = '', $placeholder = '', $options = [],$selectedValue='') {
        $fieldData = [
            'type' => $type,
            'id' => $id,
            'name' => $name,
            'value' => $value,
            'placeholder' => $placeholder,
            'options' => $options,
            'selectedValue'=>$selectedValue
        ];
    
        switch ($type) {
            case 'select':
                return $this->generateSelectField($fieldData);
            case 'textarea':
                return $this->generateTextareaField($fieldData);
            default:
                return $this->generateInputField($fieldData);
        }
    }
    public function datePicker($id, $name, $label, $value = '', $placeholder = '', $options = []) {
        if (!is_array($options)) {
            $options = [];
        }
        $options['class'] = 'datetimepicker flatpickr-input';
        $options['data-options'] = htmlspecialchars(json_encode(array_merge(['dateFormat' => 'd/m/y'], $options['data-options'] ?? [])));
        $options['readonly'] = 'readonly';
        $this->addInput('text', $id, $name, $label, $value, $placeholder, $options);
    }

    public function addTimePicker($id, $name, $label, $value = '', $placeholder = '') {
        $options = [
            'class' => 'datetimepicker flatpickr-input',
            'data-options' => htmlspecialchars(json_encode(['enableTime' => true, 'noCalendar' => true, 'dateFormat' => 'H:i', 'disableMobile' => true])),
            'readonly' => 'readonly'
        ];
        $this->addInput('text', $id, $name, $label, $value, $placeholder, $options);
    }

    public function addDateTimePicker($id, $name, $label, $value = '', $placeholder = '', $options = []) {
        $defaultOptions = [
            'class' => 'datetimepicker flatpickr-input',
            'data-options' => htmlspecialchars(json_encode(['enableTime' => true, 'dateFormat' => 'd/m/y H:i', 'disableMobile' => true])),
        ];
        // Fusionner les options par défaut avec celles fournies
        $finalOptions = array_merge($defaultOptions, $options);
        $this->addInput('text', $id, $name, $label, $value, $placeholder, $finalOptions);
    }
    
    public function renderMinimal() {
        $form = '<form id="' . $this->id . '" name="' . $this->name . '" method="' . $this->method . '"';
        if ($this->enctype) {
            $form .= ' enctype="' . $this->enctype . '"';
        }
        $form .= '>';
    
        foreach ($this->fields as $field) {
          
            $form .= $this->renderField($field);
        }
    
        if (!empty($this->submitButton['id'])) {
            $form .= '<button type="submit" id="' . $this->submitButton['id'] . '" name="' . $this->submitButton['name'] . '" value="' . $this->submitButton['value'] . '">' . $this->submitButton['text'] . '</button>';
        }
    
        $form .= '</form>';
        return $form;
    }
    
}
?>
