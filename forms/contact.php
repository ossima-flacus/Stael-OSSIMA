<?php
// Activation des erreurs pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Définition de l'adresse email qui recevra les messages
$receiving_email_address = 'staelaxel.ossima@gmail.com';

// Vérification de la méthode de requête
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Méthode non autorisée. Seules les requêtes POST sont acceptées.');
}

// Vérification des champs obligatoires
$required_fields = ['name', 'email', 'subject', 'message'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        die('Le champ ' . $field . ' est requis.');
    }
}

// Validation de l'email
if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    die('L\'adresse email n\'est pas valide.');
}

// Vérification de l'existence de la bibliothèque PHP Email Form
$php_email_form_path = '../assets/vendor/php-email-form/php-email-form.php';
if (!file_exists($php_email_form_path)) {
    die('Impossible de charger la bibliothèque "PHP Email Form"!');
}

include($php_email_form_path);

try {
    // Création d'une nouvelle instance du formulaire email
    $contact = new PHP_Email_Form;
    
    // Activation de la soumission AJAX pour le formulaire
    $contact->ajax = true;
    
    
    
    // Configuration des paramètres de base de l'email
    $contact->to = $receiving_email_address; 
    $contact->from_name = htmlspecialchars($_POST['name']);         
    $contact->from_email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);       
    $contact->subject = htmlspecialchars($_POST['subject']);       

    // Ajout des différents éléments du message
    $contact->add_message(htmlspecialchars($_POST['name']), 'From');
    $contact->add_message(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL), 'Email');
    $contact->add_message(htmlspecialchars($_POST['message']), 'Message', 10);

    // Envoi de l'email et affichage du résultat
    $result = $contact->send();
    
    if ($result) {
        echo 'Votre message a été envoyé avec succès.';
    } else {
        echo 'Une erreur est survenue lors de l\'envoi du message.';
    }
    
} catch (Exception $e) {
    die('Erreur: ' . $e->getMessage());
}
?>