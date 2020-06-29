<?php
/*
Plugin Name: Enable Mailhog
Description: Verbindet in einer Docker-Umgebung WordPress mit Mailhog
Author: Tom Rose
Version: 1.0 
*/

add_action( 'phpmailer_init', 'mailhogsetup' );
function mailhogsetup( PHPMailer $phpmailer ) {
    $phpmailer->Host = 'mailhog';
    $phpmailer->Port = 1025;
    $phpmailer->IsSMTP();
}

?>