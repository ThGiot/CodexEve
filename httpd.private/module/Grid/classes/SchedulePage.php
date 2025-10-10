<?php

namespace Grid;
class SchedulePage
{
    private FilterService $filterService;
    private array $filterOptions;
   
    private int $clientId;

    public function __construct(FilterService $filterService, int $clientId)
    {
        $this->filterService = $filterService;
        $this->clientId      = $clientId;
        // Récupération de toutes les options (associations, types, zones)
        $this->filterOptions = $this->filterService->getSelectOptions($clientId);

        

    }

    /**
     * Affiche le rendu complet de la page.
     */
    public function render(): void
    {
        // Affichage de la modal
   
        ?>
        <header class="bg-primary text-white text-center py-3">
            <h1 class="mb-0">Planification des Postes</h1>
        </header>
        <main class="container my-4">
            <!-- Filtres -->
            <section class="filters d-flex flex-wrap gap-3 mb-4">
                <!-- Filtre Zone -->
                <div class="form-group">
                    <label for="filterZone" class="form-label fw-bold">Zone :</label>
                    <select id="filterZone" class="form-select" onchange="filterTable()">
                        <option value="">Toutes les zones</option>
                        <?php foreach ($this->filterOptions['zones'] as $zone): ?>
                            <option value="<?= htmlspecialchars($zone) ?>"><?= htmlspecialchars($zone) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Filtre Type -->
                <div class="form-group">
                    <label for="filterType" class="form-label fw-bold">Type :</label>
                    <select id="filterType" class="form-select" onchange="filterTable()">
                        <option value="">Tous les types</option>
                        <?php foreach ($this->filterOptions['types'] as $type): ?>
                            <option value="<?= htmlspecialchars($type) ?>"><?= htmlspecialchars($type) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Filtre Association -->
                <div class="form-group">
                    <label for="filterAssociation" class="form-label fw-bold">Association :</label>
                    <select id="filterAssociation" class="form-select" onchange="filterTable()">
                        <option value="">Toutes les associations</option>
                        <?php foreach ($this->filterOptions['associations'] as $association): ?>
                            <option value="<?= htmlspecialchars($association) ?>"><?= htmlspecialchars($association) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Recherche par poste -->
                <div class="form-group flex-grow-1">
                    <label for="searchPost" class="form-label fw-bold">Chercher un poste :</label>
                    <input type="text" id="searchPost" class="form-control" placeholder="Rechercher un poste..." oninput="filterTable()" />
                </div>

                <!-- Filtre par jour -->
                <div class="form-group">
                    <label for="filterDay" class="form-label fw-bold">Jours :</label>
                    <select id="filterDay" class="form-select" multiple size="5" onchange="filterTable()">
                        <option value="Jeu">Jeudi</option>
                        <option value="Ven">Vendredi</option>
                        <option value="Sam">Samedi</option>
                        <option value="Dim">Dimanche</option>
                        <option value="Lun">Lundi</option>
                    </select>
                </div>
            </section>

            <div class="form-group">
                <input type="checkbox" id="ignoreEarlyHours" class="form-check-input" checked onchange="filterTable()" />
                <label for="ignoreEarlyHours" class="form-check-label">Ignorer les heures entre 03h00 et 08h00</label>
            </div>
            <div class="text-end my-3">
  <button class="btn btn-success" onclick="node('exportGridToExcel', {})">Exporter en Excel</button>
</div>


            <!-- Tableau de planning -->
            <section id="scheduleTable" class="table-responsive"></section>

            <!-- Infos complémentaires -->
            <section id="info" class="mt-4">
                <p id="totalPosts"><b><i>Cliquez sur une cellule pour voir le total des postes à cette heure.</i></b></p>
            </section>
        </main>

        <footer class="bg-dark text-white text-center py-3">
            <p class="mb-0">&copy; 2024 Planification</p>
        </footer>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
        <?php
    }
}

?>