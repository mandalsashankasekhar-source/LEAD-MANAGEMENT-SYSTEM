<?php
session_start();

include("lib/api_helper.php");
include("config/db.php");

/* ---------------- DELETE ---------------- */

if (isset($_GET['delete_id']) && !empty($_GET['delete_id'])) {

    $result = awsapi(
        [
            "user_id" => $_SESSION['id'],
            "id"      => $_GET['delete_id']
        ],
        "managelead_delete"
    );

    header("Location: manage_lead.php?msg=delete_success");
    exit();
}
/* ---------------- SAVE / UPDATE ---------------- */

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //echo "<pre>"; print_r($_POST); exit;
    $contact_name = $_POST['contact_name'];
    $phone        = $_POST['phone'];
    $email        = $_POST['email'];
    $company      = $_POST['company'];
    $priority     = $_POST['priority'];
    $status       = $_POST['status'];

    // UPDATE
    if (!empty($_POST['edit_id'])) {

        $result = awsapi(
            [
                "user_id"       => $_SESSION['id'],
                "edit_id"       => $_POST['edit_id'],
                "contact_name"  => $contact_name,
                "phone"         => $phone,
                "email"         => $email,
                "company"       => $company,
                "priority"      => $priority,
                "status"        => $status
            ],
            "managelead_save"
        );

        header("Location: manage_lead.php?msg=edit_success");
        exit();
    }

    // INSERT
    else {

        $result = awsapi(
            [
                "user_id"       => $_SESSION['id'],
                "contact_name"  => $contact_name,
                "phone"         => $phone,
                "email"         => $email,
                "company"       => $company,
                "priority"      => $priority,
                "status"        => $status
            ],
            "managelead_save"
        );

        header("Location: manage_lead.php?msg=success");
        exit();
    }

} else {

    echo "Invalid Request";

}

mysqli_close($conn);

?>