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

$systemOptions = [
    'airway' => ['label' => 'Voies aériennes', 'badge' => 'primary'],
    'urgence' => ['label' => 'Urgence', 'badge' => 'info'],
    'allergologie' => ['label' => 'Allergologie', 'badge' => 'danger'],
    'pediatrie' => ['label' => 'Pédiatrie', 'badge' => 'warning'],
    'cardio' => ['label' => 'Cardiologie', 'badge' => 'danger'],
    'neuro' => ['label' => 'Neurologie', 'badge' => 'info'],
    'smur' => ['label' => 'SMUR / Pré-hospitalier', 'badge' => 'info'],
];

$statusOptions = [
    'draft' => ['label' => 'Brouillon', 'badge' => 'warning'],
    'review' => ['label' => 'En relecture', 'badge' => 'secondary'],
    'ready' => ['label' => 'Prêt à publier', 'badge' => 'info'],
    'published' => ['label' => 'Publié', 'badge' => 'success'],
    'archived' => ['label' => 'Archivé', 'badge' => 'secondary'],
];

$fiches = [
    [
        'id' => 1,
        'title' => 'Intubation orotrachéale',
        'slug' => 'intubation-orotracheale',
        'system' => ['slug' => 'airway'] + $systemOptions['airway'],
        'status' => ['slug' => 'published'] + $statusOptions['published'],
        'lastUpdated' => '2024-02-02',
        'owner' => 'Dr L. Martin',
        'ownerSlug' => 'martin',
        'summary' => 'Objectif : garantir une intubation sécurisée en situation d\'urgence avec une checklist pas-à-pas.',
        'content' => "1. Préparation du patient\n2. Préparation du matériel\n3. Induction\n4. Intubation\n5. Confirmation & sécurisation",
        'tags' => ['SMUR', 'Voies aériennes'],
        'version' => '3.2',
        'validatedBy' => 'Comité anesthésie',
        'nextReview' => '2025-02-01',
        'alert' => ['variant' => 'warning', 'message' => 'Checklist pré-intubation obligatoire avant validation finale.'],
        'history' => [
            ['date' => '2024-02-02', 'variant' => 'success', 'title' => 'Publication', 'description' => 'Validée par Dr L. Martin, commentaire : « Version ok pour diffusion SMUR ».'],
            ['date' => '2024-01-30', 'variant' => 'warning', 'title' => 'Retour relecteur', 'description' => 'Préciser la posologie de la kétamine pour les >120 kg.'],
            ['date' => '2024-01-28', 'variant' => 'info', 'title' => 'Mise à jour', 'description' => 'Ajout d\'un schéma pas-à-pas pour la préparation du matériel.'],
        ],
        'publicUrl' => '/procedures/intubation-orotracheale'
    ],
    [
        'id' => 2,
        'title' => 'Sédation procédurale adulte',
        'slug' => 'sedation-procedurale-adulte',
        'system' => ['slug' => 'urgence'] + $systemOptions['urgence'],
        'status' => ['slug' => 'draft'] + $statusOptions['draft'],
        'lastUpdated' => '2024-01-28',
        'owner' => 'Infirmier.e C. Dubois',
        'ownerSlug' => 'dubois',
        'summary' => 'Protocoles standardisés pour la sédation consciente en salle de déchocage.',
        'content' => "1. Évaluation initiale\n2. Préparation\n3. Surveillance\n4. Récupération",
        'tags' => ['Urgence', 'Analgésie'],
        'version' => '0.9',
        'validatedBy' => 'En attente de validation',
        'nextReview' => '2024-03-15',
        'alert' => ['variant' => 'info', 'message' => 'Relecture médicale planifiée avec le Dr Martin.'],
        'history' => [
            ['date' => '2024-01-28', 'variant' => 'info', 'title' => 'Brouillon créé', 'description' => 'Contenu initial saisi par C. Dubois.'],
        ],
        'publicUrl' => '/procedures/sedation-procedurale-adulte'
    ],
    [
        'id' => 3,
        'title' => 'Gestion du choc anaphylactique',
        'slug' => 'gestion-choc-anaphylactique',
        'system' => ['slug' => 'allergologie'] + $systemOptions['allergologie'],
        'status' => ['slug' => 'review'] + $statusOptions['review'],
        'lastUpdated' => '2024-01-14',
        'owner' => 'Dr P. Rousseau',
        'ownerSlug' => 'rousseau',
        'summary' => 'Arbre décisionnel pour la prise en charge rapide des chocs anaphylactiques.',
        'content' => "1. Diagnostic\n2. Adrénaline IM\n3. Remplissage\n4. Surveillance continue",
        'tags' => ['Allergologie', 'Critique'],
        'version' => '1.6',
        'validatedBy' => 'Dr P. Rousseau',
        'nextReview' => '2024-10-01',
        'alert' => ['variant' => 'danger', 'message' => 'Version en cours de relecture suite aux nouvelles recommandations 2024.'],
        'history' => [
            ['date' => '2024-01-14', 'variant' => 'warning', 'title' => 'Relu par pharmacologie', 'description' => 'Ajout des interactions médicamenteuses majeures.'],
            ['date' => '2024-01-04', 'variant' => 'info', 'title' => 'Version 1.6', 'description' => 'Mise à jour de la posologie des corticoïdes IV.'],
        ],
        'publicUrl' => '/procedures/gestion-choc-anaphylactique'
    ],
    [
        'id' => 4,
        'title' => 'Accès intra-osseux pédiatrique',
        'slug' => 'acces-intra-osseux-pediatrique',
        'system' => ['slug' => 'pediatrie'] + $systemOptions['pediatrie'],
        'status' => ['slug' => 'published'] + $statusOptions['published'],
        'lastUpdated' => '2023-12-05',
        'owner' => 'Dr E. Bernard',
        'ownerSlug' => 'bernard',
        'summary' => 'Référentiel pour l\'accès intra-osseux d\'urgence chez le nourrisson et l\'enfant.',
        'content' => "1. Indications\n2. Sites de pose\n3. Matériel\n4. Surveillance",
        'tags' => ['Pédiatrie', 'SMUR'],
        'version' => '2.1',
        'validatedBy' => 'Cellule pédiatrique',
        'nextReview' => '2024-12-01',
        'alert' => ['variant' => 'success', 'message' => 'Version validée pour la diffusion au SMUR pédiatrique.'],
        'history' => [
            ['date' => '2023-12-05', 'variant' => 'success', 'title' => 'Publication', 'description' => 'Validée par Cellule pédiatrique.'],
            ['date' => '2023-11-20', 'variant' => 'info', 'title' => 'Ajout d\'infographies', 'description' => 'Schémas d\'implantation ajoutés pour la formation interne.'],
        ],
        'publicUrl' => '/procedures/acces-intra-osseux-pediatrique'
    ],
];

$introCard = new BootstrapCard('clinical-manager-hero', 'shadow-none border border-300 mb-4');
$introCard->setHeader('<div class="d-flex align-items-center gap-3"><span class="fs-2">🩺</span><div><h4 class="mb-1">Gestion des fiches cliniques</h4><p class="mb-0 text-700">Centralisez la création, la publication et l\'archivage des procédures en un seul endroit.</p></div></div>');
$introCard->setFooter('<div class="d-flex flex-wrap gap-2"><button class="btn btn-sm btn-primary" onclick="node(\'Clinical_new\', {})">Nouvelle fiche</button><button class="btn btn-sm btn-outline-secondary" onclick="node(\'Clinical_sync\', {})">Synchroniser le contenu</button><button class="btn btn-sm btn-outline-warning" onclick="node(\'Clinical_audit\', {})">Voir l\'audit</button></div>');
$layout->addElement($introCard->render(), 12, 'intro');

//----------------------------------------------------------------
// Tableau des fiches
//----------------------------------------------------------------
$table = new Table(
    'Fiches publiées et brouillons',
    ['procedure', 'systeme', 'statut', 'maj', 'auteur'],
    'Filtrez les fiches selon leur statut, leur système ou leur auteur.',
    'clinicalSheetsTable',
    false,
    8
);

foreach ($fiches as $fiche) {
    $systemBadge = sprintf('<span class="badge bg-%1$s-subtle text-%1$s">%2$s</span>', $fiche['system']['badge'], htmlspecialchars($fiche['system']['label']));
    $statusBadge = sprintf('<span class="badge bg-%1$s-subtle text-%1$s">%2$s</span>', $fiche['status']['badge'], htmlspecialchars($fiche['status']['label']));
    $row = [
        'procedure' => '<span class="clinical-row-label" data-fiche-id="' . $fiche['id'] . '" data-clinical-status="' . $fiche['status']['slug'] . '" data-clinical-system="' . $fiche['system']['slug'] . '" data-clinical-author="' . $fiche['ownerSlug'] . '" data-clinical-tags="' . htmlspecialchars(implode('|', $fiche['tags'])) . '">' . htmlspecialchars($fiche['title']) . '</span>',
        'systeme'   => $systemBadge,
        'statut'    => $statusBadge,
        'maj'       => (new DateTime($fiche['lastUpdated']))->format('d/m/Y'),
        'auteur'    => htmlspecialchars($fiche['owner'])
    ];

    $table->addRow(
        $row,
        [
            [
                'name'  => 'Modifier',
                'link'  => "node('Clinical_edit', {id: " . $fiche['id'] . "})",
                'class' => 'btn-primary'
            ],
            [
                'name'  => 'Prévisualiser',
                'link'  => "node('Clinical_preview', {id: " . $fiche['id'] . "})",
                'class' => 'btn-outline-secondary'
            ],
            [
                'name'  => 'Archiver',
                'link'  => "node('Clinical_archive', {id: " . $fiche['id'] . "})",
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
                <div class="ms-auto d-flex flex-wrap gap-2 align-items-center">
                    <div class="btn-group" id="clinicalStatusFilters" role="group" aria-label="Filtre statut">
                        <button type="button" class="btn btn-sm btn-outline-secondary active" data-clinical-status="all" onclick="node(\'Clinical_filter\', {statut: \"all\"})">Tous</button>
                        <button type="button" class="btn btn-sm btn-outline-success" data-clinical-status="published" onclick="node(\'Clinical_filter\', {statut: \"published\"})">Publié</button>
                        <button type="button" class="btn btn-sm btn-outline-warning" data-clinical-status="draft" onclick="node(\'Clinical_filter\', {statut: \"draft\"})">Brouillon</button>
                        <button type="button" class="btn btn-sm btn-outline-danger" data-clinical-status="review" onclick="node(\'Clinical_filter\', {statut: \"review\"})">À relire</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-clinical-status="archived" onclick="node(\'Clinical_filter\', {statut: \"archived\"})">Archivé</button>
                    </div>
                    <button class="btn btn-sm btn-outline-primary" data-clinical-tag="SMUR" onclick="node(\'Clinical_filter\', {tag: \"SMUR\"})">Tag SMUR</button>
                    <button class="btn btn-sm btn-outline-primary" data-clinical-tag="Pédiatrie" onclick="node(\'Clinical_filter\', {tag: \"Pédiatrie\"})">Tag Pédiatrie</button>
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
                        <option value="smur">SMUR / Pré-hospitalier</option>
                    </select>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <label class="form-label mb-1" for="filterAuthor">Responsable</label>
                    <select class="form-select form-select-sm" id="filterAuthor" onchange="node(\'Clinical_filter\', {author: this.value})">
                        <option value="">Tous les responsables</option>
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
$initialFiche = $fiches[0];

$tagBadges = '';
foreach ($initialFiche['tags'] as $tag) {
    $tagBadges .= '<span class="badge bg-primary-subtle text-primary">' . htmlspecialchars($tag) . '</span>';
}

$alertVariant = $initialFiche['alert']['variant'] ?? '';
$alertMessage = $initialFiche['alert']['message'] ?? '';
$alertClass = $alertMessage ? 'alert alert-' . $alertVariant . ' d-flex align-items-center gap-2' : 'alert d-none align-items-center gap-2';

$timelineVariants = [
    'success' => ['class' => 'bg-success-subtle text-success', 'icon' => 'fa-check'],
    'warning' => ['class' => 'bg-warning-subtle text-warning', 'icon' => 'fa-pen'],
    'info' => ['class' => 'bg-info-subtle text-info', 'icon' => 'fa-upload'],
    'danger' => ['class' => 'bg-danger-subtle text-danger', 'icon' => 'fa-triangle-exclamation'],
    'secondary' => ['class' => 'bg-secondary-subtle text-secondary', 'icon' => 'fa-clock'],
];

$timelineItems = '';
foreach ($initialFiche['history'] as $event) {
    $variant = $timelineVariants[$event['variant']] ?? $timelineVariants['secondary'];
    $timelineItems .= '<div class="timeline-item">'
        . '<div class="timeline-icon ' . $variant['class'] . '"><span class="fas ' . $variant['icon'] . '"></span></div>'
        . '<div class="timeline-content">'
        . '<p class="fs--1 mb-1 text-700">' . (new DateTime($event['date']))->format('d/m/Y') . ' — ' . htmlspecialchars($event['title']) . '</p>'
        . '<p class="fs--1 mb-0">' . htmlspecialchars($event['description']) . '</p>'
        . '</div>'
        . '</div>';
}

$previewCard = '<div class="card shadow-none border border-300 mb-3">'
    . '<div class="card-header border-bottom border-300 bg-soft">'
    . '<div class="d-flex align-items-center justify-content-between">'
    . '<div>'
    . '<h5 class="mb-1">Prévisualisation</h5>'
    . '<p class="mb-0 text-700">Aperçu rapide de la fiche sélectionnée.</p>'
    . '</div>'
    . '<button class="btn btn-sm btn-outline-secondary" onclick="node(\'Clinical_open_public\', {id: ' . $initialFiche['id'] . '})">Ouvrir dans le portail</button>'
    . '</div>'
    . '</div>'
    . '<div class="card-body" id="clinicalPreviewCard">'
    . '<h4 class="fw-semibold mb-2" id="clinicalPreviewTitle">' . htmlspecialchars($initialFiche['title']) . '</h4>'
    . '<div class="d-flex flex-wrap gap-2 mb-3" id="clinicalPreviewTags">'
    . '<span class="badge bg-' . $initialFiche['system']['badge'] . '-subtle text-' . $initialFiche['system']['badge'] . '">' . htmlspecialchars($initialFiche['system']['label']) . '</span>'
    . $tagBadges
    . '</div>'
    . '<p class="text-700 mb-3" id="clinicalPreviewSummary">' . htmlspecialchars($initialFiche['summary']) . '</p>'
    . '<div class="' . $alertClass . '" role="alert" id="clinicalPreviewAlert" data-alert-variant="' . htmlspecialchars($alertVariant) . '">'
    . '<span class="fas" id="clinicalPreviewAlertIcon"></span>'
    . '<span id="clinicalPreviewAlertText">' . htmlspecialchars($alertMessage) . '</span>'
    . '</div>'
    . '</div>'
    . '<div class="card-footer bg-light">'
    . '<div class="small text-700 d-flex flex-column gap-1" id="clinicalPreviewMeta">'
    . '<span><strong>Version :</strong> <span id="clinicalPreviewVersion">' . htmlspecialchars($initialFiche['version']) . '</span></span>'
    . '<span><strong>Dernière mise à jour :</strong> <span id="clinicalPreviewPublished">' . (new DateTime($initialFiche['lastUpdated']))->format('d/m/Y') . '</span></span>'
    . '<span><strong>Validée par :</strong> <span id="clinicalPreviewValidatedBy">' . htmlspecialchars($initialFiche['validatedBy']) . '</span></span>'
    . '<span><strong>Révision programmée :</strong> <span id="clinicalPreviewReviewDue">' . (new DateTime($initialFiche['nextReview']))->format('d/m/Y') . '</span></span>'
    . '</div>'
    . '</div>'
    . '</div>';

$timelineCard = '<div class="card shadow-none border border-300 mb-3">'
    . '<div class="card-header border-bottom border-300 bg-soft">'
    . '<h5 class="mb-1">Historique des modifications</h5>'
    . '<p class="mb-0 text-700">Gardez la trace des validations, publications et retours.</p>'
    . '</div>'
    . '<div class="card-body">'
    . '<div class="timeline timeline-sm" id="clinicalTimeline">'
    . $timelineItems
    . '</div>'
    . '</div>'
    . '</div>';

$rightColumn = $previewCard . $timelineCard;
$layout->addElement($rightColumn, 5, 'overviewRow');

//----------------------------------------------------------------
// Formulaire d'édition détaillée
//----------------------------------------------------------------
$onSubmit = "event.preventDefault(); node('Clinical_save', {formId: 'clinical_editor_form'});";
$form = new Form('clinical_editor_form', 'clinical_editor_form', 'POST', $onSubmit, 'Éditer la fiche sélectionnée');
$form->addField('text', 'sheetTitle', 'sheetTitle', 'Titre de la fiche', htmlspecialchars($initialFiche['title']), 'Titre affiché dans la liste');
$form->addField('text', 'sheetSlug', 'sheetSlug', 'Identifiant / slug', htmlspecialchars($initialFiche['slug']), 'Utilisé pour l\'URL et les exports');
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
        ['value' => 'urgence', 'text' => 'Urgence'],
        ['value' => 'allergologie', 'text' => 'Allergologie'],
    ],
    null,
    $initialFiche['system']['slug']
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
    $initialFiche['status']['slug']
);
$form->addField('text', 'sheetTags', 'sheetTags', 'Tags & mots-clés', htmlspecialchars(implode(', ', $initialFiche['tags'])), 'Séparez les tags par une virgule');
$form->addField('textarea', 'sheetSummary', 'sheetSummary', 'Résumé court', htmlspecialchars($initialFiche['summary']), '', ['rows' => 3]);
$form->addField('textarea', 'sheetContent', 'sheetContent', 'Contenu principal', htmlspecialchars($initialFiche['content']), '', ['rows' => 10, 'class' => 'font-monospace']);
$form->addField('text', 'sheetOwner', 'sheetOwner', 'Responsable éditorial', htmlspecialchars($initialFiche['owner']), 'Personne référente pour cette fiche');
$form->addField('date', 'sheetNextReview', 'sheetNextReview', 'Révision programmée', $initialFiche['nextReview'], '', ['class' => 'form-control']);
$form->addField('file', 'sheetAttachments', 'sheetAttachments', 'Pièces jointes', '', '', ['class' => 'form-control', 'multiple' => 'multiple']);
$form->setSubmitButton('sheetSubmit', 'sheetSubmit', 'save', 'Enregistrer les modifications');

$layout->addElement($form->render(), 12, 'editorRow');

//----------------------------------------------------------------
// Rendu final
//----------------------------------------------------------------
$datasetJson = htmlspecialchars(json_encode(array_values($fiches), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8');

echo $layout->render();
echo '<script type="application/json" id="clinicalFichesData">' . $datasetJson . '</script>';
?>
