<?php
session_start();
session_destroy();
header("Location: ../system/index.php");
exit();