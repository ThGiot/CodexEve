<?php
//----------------------------------------------------------------
// Dépendances
//----------------------------------------------------------------
require_once PRIVATE_PATH . '/classes/PageLayout.php';
require_once PRIVATE_PATH . '/classes/Table.php';
require_once PRIVATE_PATH . '/classes/Form.php';
require_once PRIVATE_PATH . '/classes/BootstrapCard.php';

//----------------------------------------------------------------
// Mise en forme de la page
//----------------------------------------------------------------
$layout = new PageLayout();

$introCard = new BootstrapCard('clinical-manager-hero', 'shadow-none border border-300 mb-4');
$introCard->setHeader('<div class="d-flex align-items-center gap-3"><span class="fs-2">🩺</span><div><h4 class="mb-1">Gestion des fiches cliniques</h4></div></div>');
$introCard->setFooter('<div class="d-flex flex-wrap gap-2"><button class="btn btn-sm btn-primary" onclick="node(\'Clinical_new\', {})">Nouvelle fiche</button><button class="btn btn-sm btn-outline-secondary" onclick="node(\'Clinical_sync\', {})">Synchroniser le contenu</button><button class="btn btn-sm btn-outline-warning" onclick="node(\'Clinical_audit\', {})">Voir l\'audit</button></div>');
$layout->addElement($introCard->render(), 12, 'intro');

//----------------------------------------------------------------
// Tableau des fiches
//----------------------------------------------------------------
$fiches = [
    [
        'procedure' => 'Intubation orotrachéale',
        'systeme'   => '<span class="badge bg-primary-subtle text-primary">Voies aériennes</span>',
        'statut'    => '<span class="badge bg-success-subtle text-success">Publié</span>',
        'maj'       => '02/02/2024',
        'auteur'    => 'Dr L. Martin'
    ],
    [
        'procedure' => 'Sédation procédurale adulte',
        'systeme'   => '<span class="badge bg-info-subtle text-info">Urgence</span>',
        'statut'    => '<span class="badge bg-warning-subtle text-warning">Brouillon</span>',
        'maj'       => '28/01/2024',
        'auteur'    => 'Infirmier.e C. Dubois'
    ],
    [
        'procedure' => 'Gestion du choc anaphylactique',
        'systeme'   => '<span class="badge bg-danger-subtle text-danger">Allergologie</span>',
        'statut'    => '<span class="badge bg-secondary-subtle text-secondary">Revue</span>',
        'maj'       => '14/01/2024',
        'auteur'    => 'Dr P. Rousseau'
    ],
    [
        'procedure' => 'Accès intra-osseux pédiatrique',
        'systeme'   => '<span class="badge bg-warning-subtle text-warning">Pédiatrie</span>',
        'statut'    => '<span class="badge bg-success-subtle text-success">Publié</span>',
        'maj'       => '05/12/2023',
        'auteur'    => 'Dr E. Bernard'
    ],
];

$table = new Table(
    'Fiches publiées et brouillons',
    ['procedure', 'systeme', 'statut', 'maj', 'auteur'],
    'Filtrez les fiches selon leur statut, leur système ou leur auteur.',
    'clinicalSheetsTable',
    false,
    8
);

foreach ($fiches as $index => $fiche) {
    $table->addRow(
        $fiche,
        [
            [
                'name'  => 'Modifier',
                'link'  => "node('Clinical_edit', {id: $index})",
                'class' => 'btn-primary'
            ],
            [
                'name'  => 'Prévisualiser',
                'link'  => "node('Clinical_preview', {id: $index})",
                'class' => 'btn-outline-secondary'
            ],
            [
                'name'  => 'Archiver',
                'link'  => "node('Clinical_archive', {id: $index})",
                'class' => 'btn-outline-danger'
            ]
        ]
    );
}

$filtersCard = '<div class="card shadow-none border border-300 mb-3">'
    . '<div class="card-header border-bottom border-300 bg-soft">
            <div class="d-flex flex-wrap align-items-center gap-3">
                <div>
                    <h5 class="mb-1">Filtres rapides</h5>
                    <p class="mb-0 text-700">Affinez l\'affichage par statut, système ou tag.</p>
                </div>
                <div class="ms-auto d-flex flex-wrap gap-2">
                    <div class="btn-group" role="group" aria-label="Filtre statut">
                        <button type="button" class="btn btn-sm btn-outline-secondary active" onclick="node(\'Clinical_filter\', {statut: \"all\"})">Tous</button>
                        <button type="button" class="btn btn-sm btn-outline-success" onclick="node(\'Clinical_filter\', {statut: \"published\"})">Publié</button>
                        <button type="button" class="btn btn-sm btn-outline-warning" onclick="node(\'Clinical_filter\', {statut: \"draft\"})">Brouillon</button>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="node(\'Clinical_filter\', {statut: \"review\"})">À relire</button>
                    </div>
                    <button class="btn btn-sm btn-outline-primary" onclick="node(\'Clinical_filterr\', {tag: \"SMUR\"})">Tag SMUR</button>
                    <button class="btn btn-sm btn-outline-primary" onclick="node(\'Clinical_filter\', {tag: \"Pédiatrie\"})">Tag Pédiatrie</button>
                </div>
            </div>
        </div>'
    . '<div class="card-body">
            <div class="row g-3 align-items-center">
                <div class="col-sm-6 col-lg-4">
                    <label class="form-label mb-1" for="filterSystem">Système</label>
                    <select class="form-select form-select-sm" id="filterSystem" onchange="node(\'Clinical_filter\', {system: this.value})">
                        <option value="">Tous les systèmes</option>
                        <option value="airway">Voies aériennes</option>
                        <option value="urgence">Urgence</option>
                        <option value="allergologie">Allergologie</option>
                        <option value="pediatrie">Pédiatrie</option>
                    </select>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <label class="form-label mb-1" for="filterAuthor">Auteur</label>
                    <select class="form-select form-select-sm" id="filterAuthor" onchange="node(\'Clinical_filter\', {author: this.value})">
                        <option value="">Tous les auteurs</option>
                        <option value="martin">Dr L. Martin</option>
                        <option value="dubois">Infirmier.e C. Dubois</option>
                        <option value="rousseau">Dr P. Rousseau</option>
                        <option value="bernard">Dr E. Bernard</option>
                    </select>
                </div>
                <div class="col-sm-12 col-lg-4">
                    <label class="form-label mb-1" for="filterSearch">Recherche</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-transparent"><span class="fas fa-search"></span></span>
                        <input type="search" class="form-control" id="filterSearch" placeholder="Rechercher une fiche" oninput="node(\'Clinical_filter\', {search: this.value})">
                        <button class="btn btn-outline-secondary" onclick="node(\'Clinical_filter_reset\', {})">Réinitialiser</button>
                    </div>
                </div>
            </div>
        </div>'
    . '</div>';

$leftColumn = $filtersCard . $table->render(true);
$layout->addElement($leftColumn, 7, 'overviewRow');

//----------------------------------------------------------------
// Carte de prévisualisation et métadonnées
//----------------------------------------------------------------
$previewCard = '<div class="card shadow-none border border-300 mb-3">
        <div class="card-header border-bottom border-300 bg-soft">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="mb-1">Prévisualisation</h5>
                    <p class="mb-0 text-700">Aperçu rapide de la fiche sélectionnée.</p>
                </div>
                <button class="btn btn-sm btn-outline-secondary" onclick="node(\'Clinical_open_public\', {id: 0})">Ouvrir dans le portail</button>
            </div>
        </div>
        <div class="card-body">
            <h4 class="fw-semibold mb-2">Intubation orotrachéale</h4>
            <div class="d-flex flex-wrap gap-2 mb-3">
                <span class="badge bg-primary-subtle text-primary">Voies aériennes</span>
                <span class="badge bg-info-subtle text-info">SMUR</span>
                <span class="badge bg-secondary-subtle text-secondary">Dernière relecture : 02/2024</span>
            </div>
            <p class="text-700 mb-3">Objectif : garantir une intubation sécurisée en situation d\'urgence. Les points clés incluent la préparation du matériel, l\'induction rapide et la vérification du positionnement.</p>
            <div class="alert alert-warning d-flex align-items-center gap-2" role="alert">
                <span class="fas fa-triangle-exclamation"></span>
                <span>Checklist pré-intubation obligatoire avant validation finale.</span>
            </div>
        </div>
        <div class="card-footer bg-light">
            <div class="small text-700 d-flex flex-column">
                <span><strong>Version :</strong> 3.2 — publiée le 02/02/2024</span>
                <span><strong>Validée par :</strong> Comité anesthésie</span>
                <span><strong>Révision programmée :</strong> 02/2025</span>
            </div>
        </div>
    </div>';

$timelineCard = '<div class="card shadow-none border border-300 mb-3">
        <div class="card-header border-bottom border-300 bg-soft">
            <h5 class="mb-1">Historique des modifications</h5>
            <p class="mb-0 text-700">Gardez la trace des validations, publications et retours.</p>
        </div>
        <div class="card-body">
            <div class="timeline timeline-sm">
                <div class="timeline-item">
                    <div class="timeline-icon bg-success-subtle text-success"><span class="fas fa-check"></span></div>
                    <div class="timeline-content">
                        <p class="fs--1 mb-1 text-700">02/02/2024 — Publication</p>
                        <p class="fs--1 mb-0">Validée par Dr L. Martin, commentaire : « Version ok pour diffusion SMUR ».</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-icon bg-warning-subtle text-warning"><span class="fas fa-pen"></span></div>
                    <div class="timeline-content">
                        <p class="fs--1 mb-1 text-700">30/01/2024 — Retour relecteur</p>
                        <p class="fs--1 mb-0">Préciser la posologie de la kétamine pour les >120 kg.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-icon bg-info-subtle text-info"><span class="fas fa-upload"></span></div>
                    <div class="timeline-content">
                        <p class="fs--1 mb-1 text-700">28/01/2024 — Mise à jour</p>
                        <p class="fs--1 mb-0">Ajout d\'un schéma pas-à-pas pour la préparation du matériel.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>';

$rightColumn = $previewCard . $timelineCard;
$layout->addElement($rightColumn, 5, 'overviewRow');

//----------------------------------------------------------------
// Formulaire d'édition détaillée
//----------------------------------------------------------------
$onSubmit = "event.preventDefault(); node('Clinical_save', {formId: 'clinical_editor_form'});";
$form = new Form('clinical_editor_form', 'clinical_editor_form', 'POST', $onSubmit, 'Éditer la fiche sélectionnée');
$form->addField('text', 'sheetTitle', 'sheetTitle', 'Titre de la fiche', 'Intubation orotrachéale', 'Titre affiché dans la liste');
$form->addField('text', 'sheetSlug', 'sheetSlug', 'Identifiant / slug', 'intubation-orotracheale', 'Utilisé pour l\'URL et les exports');
$form->addField(
    'searchable-select',
    'sheetSystem',
    'sheetSystem',
    'Système concerné',
    '',
    'Sélectionner un système',
    [
        ['value' => 'airway', 'text' => 'Voies aériennes'],
        ['value' => 'cardio', 'text' => 'Cardiologie'],
        ['value' => 'neuro', 'text' => 'Neurologie'],
        ['value' => 'pediatrie', 'text' => 'Pédiatrie'],
        ['value' => 'smur', 'text' => 'SMUR / Pré-hospitalier'],
    ],
    null,
    'airway'
);
$form->addField(
    'searchable-select',
    'sheetStatus',
    'sheetStatus',
    'Statut de publication',
    '',
    'Choisir un statut',
    [
        ['value' => 'draft', 'text' => 'Brouillon'],
        ['value' => 'review', 'text' => 'En relecture'],
        ['value' => 'ready', 'text' => 'Prêt à publier'],
        ['value' => 'published', 'text' => 'Publié'],
        ['value' => 'archived', 'text' => 'Archivé'],
    ],
    null,
    'published'
);
$form->addField('text', 'sheetTags', 'sheetTags', 'Tags & mots-clés', 'intubation, smur, airway', 'Séparez les tags par une virgule');
$form->addField('textarea', 'sheetSummary', 'sheetSummary', 'Résumé court', 'Résumé destiné aux listes et aux exports PDF.', '', ['rows' => 3]);
$form->addField('textarea', 'sheetContent', 'sheetContent', 'Contenu principal', "1. Préparation du patient\n2. Préparation du matériel\n3. Induction\n4. Intubation\n5. Confirmation & sécurisation", '', ['rows' => 10, 'class' => 'font-monospace']);
$form->addField('text', 'sheetOwner', 'sheetOwner', 'Responsable éditorial', 'Dr L. Martin', 'Personne référente pour cette fiche');
$form->addField('date', 'sheetNextReview', 'sheetNextReview', 'Révision programmée', '2025-02-01', '', ['class' => 'form-control']);
$form->addField('file', 'sheetAttachments', 'sheetAttachments', 'Pièces jointes', '', '', ['class' => 'form-control', 'multiple' => 'multiple']);
$form->setSubmitButton('sheetSubmit', 'sheetSubmit', 'save', 'Enregistrer les modifications');

$layout->addElement($form->render(), 12, 'editorRow');

//----------------------------------------------------------------
// Rendu final
//----------------------------------------------------------------
echo $layout->render();
?>
