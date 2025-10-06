<?php
class TaskTable
{
    private $title;
    private $columns;
    private $tasks = [];
    private $id;
    private $itemsPerPage;

    public function __construct($title, $columns, $id = "taskTable", $itemsPerPage = 10)
    {
        $this->title = $title;
        $this->columns = $columns;
        $this->id = $id;
        $this->itemsPerPage = $itemsPerPage;
    }

    public function addTask($taskName, $startDay, $startHour, $endDay, $endHour)
    {
        $this->tasks[] = [
            'taskName' => $taskName,
            'startDay' => $startDay,
            'startHour' => $startHour,
            'endDay' => $endDay,
            'endHour' => $endHour
        ];
    }

    public function render()
    {
        $html = '<div class="task-table-container">';
        $html .= '<h2>' . $this->title . '</h2>';
        $html .= '<div class="task-table" id="' . $this->id . '">';
        $html .= '<table class="table table-bordered"><thead><tr>';

        foreach ($this->columns as $column) {
            $html .= '<th>' . $column . '</th>';
        }

        $html .= '</tr></thead><tbody>';

        foreach ($this->tasks as $task) {
            $html .= '<tr>';
            $html .= '<td>' . $task['taskName'] . '</td>';
            $html .= '<td>' . $task['startDay'] . '</td>';
            $html .= '<td>' . $task['startHour'] . '</td>';
            $html .= '<td>' . $task['endDay'] . '</td>';
            $html .= '<td>' . $task['endHour'] . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table></div></div>';
        return $html;
    }

    public function renderGanttChart()
    {
        $days = ['Thursday', 'Friday', 'Saturday', 'Sunday', 'Monday'];
        $hours = range(8, 3);

        $html = '<div class="gantt-chart">';
        $html .= '<h2>' . $this->title . '</h2>';
        $html .= '<table class="gantt-table"><thead><tr><th>Task Name</th>';

        foreach ($days as $day) {
            foreach ($hours as $hour) {
                $html .= '<th>' . $day . ' ' . sprintf('%02d:00', $hour) . '</th>';
            }
        }

        $html .= '</tr></thead><tbody>';

        foreach ($this->tasks as $task) {
            $html .= '<tr><td>' . $task['taskName'] . '</td>';
            foreach ($days as $day) {
                foreach ($hours as $hour) {
                    if ($task['startDay'] == $day && $task['startHour'] == $hour) {
                        $html .= '<td colspan="' . ($task['endHour'] - $task['startHour'] + 1) . '" class="task-cell">' . $task['taskName'] . '</td>';
                    } else {
                        $html .= '<td></td>';
                    }
                }
            }
            $html .= '</tr>';
        }

        $html .= '</tbody></table></div>';
        return $html;
    }
}
?>