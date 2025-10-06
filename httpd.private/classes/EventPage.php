<?php

class EventPage {
    private $title;
    private $subtitle;
    private $eventDetails = [];
    private $groups = [];

    public function setTitle($title) {
        $this->title = $title;
    }

    public function setSubtitle($subtitle) {
        $this->subtitle = $subtitle;
    }

    public function setEventDetails($details) {
        $this->eventDetails = $details;
    }

    public function addGroup($groupTitle, $blocks) {
        $this->groups[] = [
            'title' => $groupTitle,
            'blocks' => $blocks
        ];
    }

    private function generateHeader() {
        return "<header class='thor-header'>
                    <h1>{$this->title}</h1>
                    <p>{$this->subtitle}</p>
                </header>";
    }

    private function generateEventInfo() {
        $infoHtml = '';
        foreach ($this->eventDetails as $detail) {
            $infoHtml .= "<p>{$detail}</p>";
        }
        return "<div class='thor-event-info'>{$infoHtml}</div>";
    }

    private function generateGroup($group) {
        $blocksHtml = '';
        foreach ($group['blocks'] as $block) {
            $blocksHtml .= $this->generateScheduleBlock($block);
        }
        return "<section class='thor-schedule'>
                    <div class='thor-schedule-info'>{$group['title']}</div>
                    {$blocksHtml}
                </section>";
    }

    private function generateScheduleBlock($block) {
        $personsHtml = '';
        foreach ($block['personnel'] as $person) {
            $personsHtml .= "<span class='thor-person'>{$person}</span>";
        }
        return "<div class='thor-schedule-block'>
                    <h3 class='thor-schedule-title'>{$block['title']}</h3>
                    <p class='thor-schedule-time'>{$block['time']}</p>
                    <div class='thor-personnel'>{$personsHtml}</div>
                    <div class='thor-footer'>
                        <span class='thor-count'>{$block['count']}</span>
                    </div>
                </div>";
    }

    public function render() {
        $header = $this->generateHeader();
        $eventInfo = $this->generateEventInfo();
        $groupsHtml = '';

        foreach ($this->groups as $group) {
            $groupsHtml .= $this->generateGroup($group);
        }

        return "<div class='thor-container'>
                    {$header}
                    {$eventInfo}
                    {$groupsHtml}
                </div>";
    }
}


?>
