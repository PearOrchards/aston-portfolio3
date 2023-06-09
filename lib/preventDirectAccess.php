<?php
    // This file MUST be required from any file that is not a page.
    // It'll prevent access to any following code.
    // (When accessing project.php, for example, only this file and the project.php will be required)
    // (Whereas when accessing projects.php: projects.php, lib/project.php, and this file will be required, and so code will not be stopped)
    if ( count(get_included_files()) <= 2 ) {
        header("HTTP 1.1/ 403 Forbidden");
        exit ("Direct access to this file is forbidden.");
    }