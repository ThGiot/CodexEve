<?php
require_once PRIVATE_PATH . '/classes/Form.php';
require_once 'PosteService.php';

class PosteForm {
    private Form $form;

    public function __construct(
        ?array $poste = null,  // NULL = création, Tableau = modification
        array $optionsAssociations = [],
        array $optionsTypes = [],
        array $optionsZones = [],
        array $optionsHoraires = [],
        string $action = '',
        string $id = '',
    ) {
        $isUpdate = !is_null($poste); // Vérifie si c'est une modification

        $this->form = new Form(
            id: $id,
            name: 'posteForm',
            method: 'POST',
            action: $action,
            title: $isUpdate ? 'Modifier un poste' : 'Ajouter un poste'
        );

        $this->buildForm(
            poste: $poste ?? [], // Si $poste est null, on passe un tableau vide
            optionsAssociations: $optionsAssociations,
            optionsTypes: $optionsTypes,
            optionsZones: $optionsZones,
            optionsHoraires: $optionsHoraires,
            isUpdate: $isUpdate
        );
    }

    private function buildForm(
        array $poste,
        array $optionsAssociations,
        array $optionsTypes,
        array $optionsZones,
        array $optionsHoraires,
        bool $isUpdate
    ): void {
        // Gestion du champ "Poste N°"
            $numeroPoste = $poste['numero'];

        $this->form->addField(
            type: 'text', // Mettre "text" pour éviter les erreurs sur un champ non numérique
            id: 'posteNum',
            name: 'poste_num',
            label: 'Poste N°',
            value: $numeroPoste,
            group: 'fact',
        );

        // Champ "Nom du Poste"
        $this->form->addField(
            type: 'text',
            id: 'posteNom',
            name: 'poste_nom',
            label: 'Poste',
            value: $poste['nom'] ?? '',
            placeholder: 'Entrez le nom du poste',
            group: 'fact'
        );

        // Sélection "Zone"
        $this->form->addField(
            type: 'select',
            id: 'posteZone',
            name: 'poste_zone',
            label: 'Zone',
            selectedValue: $poste['zone_id'] ?? '',
            options: $optionsZones,
            group: 'fact'
        );

        // Sélection "Association"
        $this->form->addField(
            type: 'select',
            id: 'posteAssociation',
            name: 'poste_association',
            label: 'Association',
            selectedValue: $poste['association_id'] ?? '',
            options: $optionsAssociations,
            group: 'fact'
        );

        // Sélection "Type de poste"
        $this->form->addField(
            type: 'select',
            id: 'posteType',
            name: 'poste_type',
            label: 'Type',
            selectedValue: $poste['poste_type_id'] ?? '',
            options: $optionsTypes,
            group: 'fact'
        );

        // Sélection "Horaire"
        $this->form->addField(
            type: 'select',
            id: 'posteHoraire',
            name: 'horaire_id',
            label: 'Horaire',
            selectedValue: $poste['horaire_id'] ?? '',
            options: $optionsHoraires,
            group: 'fact'
        );

        // Bouton de soumission
        $this->form->setSubmitButton(
            id: 'buttonSubmit',
            name: 'submit',
            value: 'send',
            text: $isUpdate ? 'Modifier' : 'Enregistrer'
        );
    }

    public function renderMinimal(): string {
        // Vérifie si des champs existent avant d'appeler renderMinimal()
        if (empty($this->form->fields)) {
            return "<p>Aucun champ à afficher.</p>";
        }
    
        return $this->form->renderMinimal();
    }
    
    

    public function render(): string {
        return $this->form->render();
    }
}
?>