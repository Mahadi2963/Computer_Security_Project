<?php
ob_start();

$action = $_GET['action'];
include 'admin_class.php';

$crud = new Action();

switch ($action) {
    case 'login':
        echo $crud->login();
        break;

    case 'login2':
        echo $crud->login2();
        break;

    case 'logout':
        echo $crud->logout();
        break;

    case 'logout2':
        echo $crud->logout2();
        break;

    case 'save_user':
        echo $crud->save_user();
        break;

    case 'delete_user':
        echo $crud->delete_user();
        break;

    case 'signup':
        echo $crud->signup();
        break;

    case 'update_account':
        echo $crud->update_account();
        break;

    case 'save_settings':
        echo $crud->save_settings();
        break;

    case 'save_course':
        echo $crud->save_course();
        break;

    case 'delete_course':
        echo $crud->delete_course();
        break;

    case 'update_alumni_acc':
        echo $crud->update_alumni_acc();
        break;

    case 'save_gallery':
        echo $crud->save_gallery();
        break;

    case 'delete_gallery':
        echo $crud->delete_gallery();
        break;

    case 'save_career':
        echo $crud->save_career();
        break;

    case 'delete_career':
        echo $crud->delete_career();
        break;

    case 'save_forum':
        echo $crud->save_forum();
        break;

    case 'delete_forum':
        echo $crud->delete_forum();
        break;

    case 'save_comment':
        echo $crud->save_comment();
        break;

    case 'delete_comment':
        echo $crud->delete_comment();
        break;

    case 'save_event':
        echo $crud->save_event();
        break;

    case 'delete_event':
        echo $crud->delete_event();
        break;

    case 'participate':
        echo $crud->participate();
        break;

    case 'get_venue_report':
        echo $crud->get_venue_report();
        break;

    case 'save_art_fs':
        echo $crud->save_art_fs();
        break;

    case 'delete_art_fs':
        echo $crud->delete_art_fs();
        break;

    case 'get_pdetails':
        echo $crud->get_pdetails();
        break;

    default:
        echo "Invalid action";
}

ob_end_flush();
