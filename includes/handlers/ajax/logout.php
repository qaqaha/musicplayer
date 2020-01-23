<?php 
// without session start the destroy doesnt know anything about sessions.
session_start();
session_destroy();
?>