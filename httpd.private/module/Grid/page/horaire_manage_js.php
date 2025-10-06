
<?php
require_once PRIVATE_PATH . '/classes/Table.php';
require_once PRIVATE_PATH . '/classes/Form.php';
require_once PRIVATE_PATH . '/classes/PageLayout.php';
require_once PRIVATE_PATH . '/classes/Modal.php';
require_once PRIVATE_PATH . '/classes/RequestHandler.php';
require_once dirname(__DIR__, 1) . '/classes/HoraireService.php';


$requestHandler = new RequestHandler();

// Définir les règles de validation
$rules = ['horaire_id' => ['type' => 'int']];
$data = $requestHandler->handleRequest($data, $rules);

$horaireService = new HoraireService($dbh);

$horaireId = isset($data['horaire_id']) ? (int) $data['horaire_id'] : 0;
$clientId = $_SESSION['client_actif'];

// Vérifie si l'ID de l'horaire est valide
if (!$horaireId) {
    exit('Aucun horaire spécifié.');
}
?>
    
    
    <h2 class="text-center mb-4">Gestion des Horaires</h2>

    <!-- Configuration des périodes -->
    <div id="periodeManage" lass="card p-3 mb-3">
        <div class="row g-2 align-items-center">
            <div class="col-md-4">
                <input type="text" id="period-name" class="form-control" placeholder="Nom de la période">
            </div>
            <div class="col-md-2">
                <input type="color" id="period-color" class="form-control form-control-color" value="#4CAF50">
            </div>
            <div class="col-md-4">
                <button class="btn btn-info w-100" onclick="node('grid_hpm_add_periode', {})">Ajouter Période</button>
                <button class="btn btn-secondary w-100" onclick="node('grid_periode_manage', {horaireId:'<?php echo $horaireId;?>'})">Charger</button>
            </div>
        </div>
    </div>

    <!-- Liste des périodes avec édition -->
    <div id="period-list" class="d-flex flex-wrap gap-2 mb-3"></div>

    <!-- Tableau des horaires -->
    <div class="table-responsive">
        <table id="schedule" class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr id="table-header">
                    <th>Jour</th>
                </tr>
            </thead>
            <tbody id="table-body">
                <!-- Les lignes des jours seront générées dynamiquement -->
            </tbody>
        </table>
    </div>

    <button class="btn btn-success mt-3" onclick="node('grid_periode_save', {horaireId:'<?php echo $horaireId;?>'})">Sauvegarder</button>
    
    <pre id="output" class="mt-3 p-3 bg-light border rounded"></pre>

    <!-- Toast Bootstrap (Haut à droite) -->
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="errorToast" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="toastMessage">Erreur</div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
  </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>node('grid_periode_manage', {})</script>

