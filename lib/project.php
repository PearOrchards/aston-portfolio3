<?php
    require __DIR__ . '/preventDirectAccess.php'; // Stop direct access to this file
/**
 * Again, we treat this more as an object, rather than a class. I love OOP.
 */
class Project {
    public int $pid;
    public string $title;
    public DateTime $startDate;
    public DateTime $endDate;
    public string $phase;
    public string $description;
    public int $uid;

    public function __construct(?int $pid, string $title, DateTime $startDate, DateTime $endDate, string $phase, string $description, int $uid) {
        $this->pid = $pid;
        $this->title = $title;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->phase = $phase;
        $this->description = $description;
        $this->uid = $uid;
    }
}