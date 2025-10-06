<?php
class Table
{
    private $title;
    private $subtitle;
    private $columns;
    private $rows = [];
    private $id;
    private $grey;
    private $itemsPerPage;

    public function __construct($title, $columns, $subtitle ='', $id = "tableExample", $grey = false, $itemsPerPage = 10)
    {
        $this->title = $title;
        $this->subtitle = $subtitle;
        $this->columns = $columns;
        $this->id = $id;
        $this->grey = $grey;
        $this->itemsPerPage = $itemsPerPage; // Ajout de la nouvelle ligne pour définir le nombre d'éléments par page
    }

    public function addRow($row, $actions = [], $tdAttributes = [])
{
    $this->rows[] = ['data' => $row, 'actions' => $actions, 'tdAttributes' => $tdAttributes];
}
    public function addRowWithButton($row, $button)
{
    $row['Action'] = '<button class="btn btn-' . ($button['class'] ?? 'primary') . '" onclick="' . $button['link'] . '">' . $button['name'] . '</button>';
    $this->rows[] = ['data' => $row, 'actions' => []]; // Pas d'actions dropdown ici
}

    

public function render(bool $showButtonsDirectly = false)
{
    $valueNames = htmlspecialchars(json_encode($this->columns), ENT_NOQUOTES);
    $html = '';

    if (!$this->grey) {
        $html .= '<div class="card shadow-none border border-300 mb-3" data-component-card="data-component-card">';
        $html .= '<div class="card-header p-4 border-bottom border-300 bg-soft"><div class="row g-3 justify-content-between align-items-end"><div class="col-12 col-md">';
        $html .= '<h4 class="text-900 mb-0" data-anchor="data-anchor">' . $this->title . '</h4>';
        $html .= '<p class="mb-0 mt-2 text-800">' . $this->subtitle . '</p></div><div class="col col-md-auto"></div></div></div>';
        $html .= '<div class="p-4">';
    }

    $html .= '<div class="iamtable" id="' . $this->id . '" data-list=\'{"valueNames":' . $valueNames . ',"page":' . $this->itemsPerPage . ',"pagination":true}\'>';

    if ($this->grey) {
        $html .= ' <h3 class="mb-4">' . $this->title . ' <span class="text-700 fw-normal">' . $this->subtitle . '</span></h3>';
    }

    $html .= '<div class="d-flex flex-column flex-md-row align-items-md-center mb-3">';
    $html .= '<div class="search-box w-100 w-md-auto mx-auto mx-md-0">';
    $html .= '<form class="position-relative" data-bs-toggle="search">';
    $html .= '<input class="form-control search-input search form-control-sm" type="search" placeholder="Search" aria-label="Search" />';
    $html .= '<span class="fas fa-search search-box-icon"></span>';
    $html .= '</form></div></div>';
    $html .= '<div class="table-responsive"><table class="table table-striped table-sm fs--1 mb-0"><thead><tr>';

    foreach ($this->columns as $column) {
        $html .= '<th class="sort border-top ps-3" data-sort="' . $column . '">' . ucfirst($column) . '</th>';
    }

    $hasActions = !empty(array_filter($this->rows, fn($row) => !empty($row['actions'])));
    if ($hasActions) {
        $html .= '<th class="sort text-end align-middle pe-0 border-top" scope="col">ACTION</th>';
    }

    $html .= '</tr></thead><tbody class="list">';

    foreach ($this->rows as $row) {
        $html .= '<tr>';
        foreach ($this->columns as $column) {
            $html .= '<td class="align-middle ps-3 ' . $column . '"';
            if (isset($row['tdAttributes'][$column])) {
                foreach ($row['tdAttributes'][$column] as $attrName => $attrValue) {
                    $html .= ' ' . $attrName . '="' . $attrValue . '"';
                }
            }
            $html .= '>' . $row['data'][$column] . '</td>';
        }

        if ($hasActions) {
            $html .= '<td class="align-middle white-space-nowrap text-end pe-0">';

            if ($showButtonsDirectly) {
                // 🔹 Affichage direct des boutons
                foreach ($row['actions'] as $action) {
                    $class = 'btn btn-sm ' . ($action['class'] ?? 'btn-primary');
                    $html .= '<button class="' . $class . '" onclick="' . $action['link'] . '">' . $action['name'] . '</button> ';
                }
            } else {
                // 🔹 Ancien comportement (menu déroulant `...`)
                $html .= '<div class="font-sans-serif btn-reveal-trigger position-static">';
                $html .= '<button class="btn btn-sm dropdown-toggle dropdown-caret-none transition-none btn-reveal fs--2" type="button" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false" data-bs-reference="parent">';
                $html .= '<span class="fas fa-ellipsis-h fs--2"></span></button>';
                $html .= '<div class="dropdown-menu dropdown-menu-end py-2">';

                foreach ($row['actions'] as $action) {
                    $class = 'dropdown-item';
                    if (isset($action['class'])) {
                        $class .= ' text-' . $action['class'];
                    }
                    $html .= '<a class="' . $class . '" onclick="' . $action['link'] . '">' . $action['name'] . '</a>';
                }

                $html .= '</div></div>';
            }

            $html .= '</td>';
        }

        $html .= '</tr>';
    }

    $html .= '</tbody></table></div>';
    $html .= '<div class="d-flex justify-content-between mt-3"><span class="d-none d-sm-inline-block" data-list-info="data-list-info"></span>';
    $html .= '<div class="d-flex">';
    $html .= '<button class="page-link" data-list-pagination="prev"><span class="fas fa-chevron-left"></span></button>';
    $html .= '<ul class="mb-0 pagination"></ul>';
    $html .= '<button class="page-link pe-0" data-list-pagination="next"><span class="fas fa-chevron-right"></span></button>';
    $html .= '</div>';

    return $html;
}




}



?>