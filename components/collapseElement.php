<?php
// There is no sensitive data in this file, so we don't need to prevent direct access.
/**
 * Creates a collapsible element for use in projects.php
 * @param $pid - Project ID
 * @param $title - Project Title
 * @param $desc - Project Description
 * @param $status - Project Status
 * @param $startTime - Project Start Time
 * @param $endTime - Project End Time
 * @param $assigned - Project Assigned To
 * @return void - But echos the generated HTML
 */
function collapseElement($pid, $title, $desc, $status, $startTime, $endTime, $assigned, $openByDefault = false): void {
    // Converts the start and end times into a more readable format.
    $startTime = $startTime->format("d/m/Y");
    $endTime = $endTime->format("d/m/Y");

    // Escaping fields that require it
    $title = htmlspecialchars($title);
    $desc = htmlspecialchars($desc);
    $status = htmlspecialchars($status);
    $assigned = htmlspecialchars($assigned);
    // pid is always defined by the db, so we don't need to escape it.
    // startTime and endTime will either be a valid date or null, so we don't need to escape them either.

    // Checks the user is logged in. If so, display two extra buttons. Unless we're on the delete page
    $modifyButtons = (isset($_SESSION["user"]) && basename($_SERVER['PHP_SELF']) != "delete.php") ? <<<"HTML"
            <h2><a href="upsert.php?mode=modify&pid=$pid"><i class="fa-solid fa-wrench"></i></a></h2>
            <h2><a href="delete.php?pid=$pid"><i class="fa-solid fa-trash"></i></a></h2>  
        HTML : "";

    $openByDefault = $openByDefault ? "open" : "";
    // Generates the HTML for the collapsible element.
    $html = <<<"HTML"
            <div class="collapseElement $openByDefault">
                <div class="collapseHeader">
                    <div class="left row">
                        <h2><i class="fa-solid fa-chevron-right"></i></h2>
                        <h2>$title</h2>
                    </div>
                    <div class="right row">
                        <h2 class="upperEndTime"><i class="fa-solid fa-clock-rotate-left"></i>$startTime</h2>
                        $modifyButtons
                    </div>
                </div>
                <div class="collapseContent">
                    <div class="desc">
                        <pre>Current Status: $status</pre>
                        <h3>$desc</h3>
                    </div>
                    <div class="times">
                        <h3><i class="fa-solid fa-clock-rotate-left"></i>$startTime</h3>
                        <h3><i class="fa-solid fa-clock-rotate-left fa-flip-horizontal"></i>$endTime</h3>
                    </div>
                    <div class="assigned">
                        <h4>Assigned to: $assigned</h4>
                    </div>
                </div>
            </div>
        HTML;

    echo $html;
}