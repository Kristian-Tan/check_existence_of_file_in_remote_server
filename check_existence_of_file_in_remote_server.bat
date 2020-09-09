@ECHO OFF
setlocal DISABLEDELAYEDEXPANSION
SET BIN_TARGET=%~dp0/check_existence_of_file_in_remote_server.php
php "%BIN_TARGET%" %*
