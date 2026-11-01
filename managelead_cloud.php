<?php
include ("lib/api_helper.php");
include("config/db.php");
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

// ---------------------- ADD or UPDATE ----------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $payload = [
        "action" => empty($_POST['id']) ? "add" : "update",
        "sales_person_id" => $_SESSION['id'],
        "contact_name" => $_POST['contact_name'] ?? '',
        "phone" => $_POST['phone'] ?? '',
        "id" => $_POST['id'] ?? '',
        "email" => $_POST['email'] ?? '',
        "company" => $_POST['company'] ?? '',
        "priority" => $_POST['priority'] ?? '',
        "status" => $_POST['status'] ?? ''
    ];
    //echo "<pre>";print_r($payload);exit;



    if (!empty($_POST['id'])) {
        $payload['id'] = $_POST['id'];
    }

    awsAPI($payload, 'managelead_save');

    header("Location: managelead_cloud.php");
    exit;
}

// ---------------------- DELETE ----------------------
if (isset($_GET['delete'])) {

    awsAPI([
        "action" => "delete",
        "id" => $_GET['delete']
    ], 'managelead_delete');

    header("Location: managelead_cloud.php");
    exit;
}

// ---------------------- EDIT FETCH ----------------------
$rowData = [];

if (isset($_GET['edit'])) {

    $rowData = awsAPI([
        "action" => "getOne",
        "id" => $_GET['edit']
    ], 'managelead_get');
    //echo "<pre>";print_r($rowData);exit;

    if (!is_array($rowData)) {
        $rowData = [];
    }
}

// ---------------------- FETCH LEADS ----------------------
$leadList = awsAPI([
    "action" => "list",
    "sales_person_id" => $_SESSION['id']
], 'managelead_list');
////


if (!is_array($leadList)) {
    $leadList = [];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Lead Management CRUD</title>

    <link href="assets/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body{
            background:#f5f6fa;
        }

        .card{
            border:none;
            box-shadow:0px 3px 10px rgba(0,0,0,.1);
        }
    </style>
</head>
<body>

<style>
.sidebar{
    background:#212529;
    min-height:100vh;
}

.sidebar .logo{
    background:#0d6efd;
    color:#fff;
    text-align:center;
    padding:20px;
    font-size:24px;
    font-weight:bold;
}

.sidebar .nav-link{
    color:#fff;
    padding:12px 20px;
    border-bottom:1px solid rgba(255,255,255,.1);
}

.sidebar .nav-link:hover{
    background:#0d6efd;
    color:#fff;
}

.main-content{
    padding:20px;
}
</style>

<div class="container-fluid">
<div class="row">

    <!-- Left Menu -->
    <div class="col-md-2 sidebar p-0">

        <div class="logo">
             <?php echo $_SESSION['username']; ?>(<?php echo $_SESSION['role']; ?>)
        </div>

        <ul class="nav flex-column">

            <li class="nav-item">
                <a href="dashboard.php" class="nav-link">
                    🏠 Dashboard
                </a>
            </li>

            <li class="nav-item">
                <a href="manage_lead.php" class="nav-link">
                    📋 Manage Leads
                </a>
            </li>

            

            <li class="nav-item">
                <a href="logout.php" class="nav-link text-danger">
                    🚪 Logout
                </a>
            </li>

        </ul>

    </div>

    <!-- Right Content -->
    <div class="col-md-10 main-content">

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h4>
                <?php echo $rowData ? "Edit Lead" : "Add New Lead"; ?>
            </h4>
        </div>

        <div class="card-body">

            <form method="post" action="">

                <input type="hidden"
                       name="id"
                       value="<?php echo $rowData[0]['id'] ?? ''; ?>">

                <div class="row">

                   

                    <div class="col-md-4 mb-3">
                        <label>Contact Name</label>

                        <input type="text"
                               name="contact_name"
                               class="form-control"
                               required
                               value="<?php echo $rowData[0]['contact_name'] ?? ''; ?>">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Phone</label>

                        <input type="text"
                               name="phone"
                               class="form-control"
                               required
                               value="<?php echo $rowData[0]['phone'] ?? ''; ?>">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Email</label>

                        <input type="email"
                               name="email"
                               class="form-control"
                               value="<?php echo $rowData[0]['email'] ?? ''; ?>">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Company</label>

                        <input type="text"
                               name="company"
                               class="form-control"
                               value="<?php echo $rowData[0]['company'] ?? ''; ?>">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Priority</label>

                        <select name="priority"
                                class="form-control">

                            <option value="High"
                            <?php if(($rowData[0]['priority'] ?? '')=='High') echo 'selected'; ?>>
                                High
                            </option>

                            <option value="Medium"
                            <?php if(($rowData[0]['priority'] ?? '')=='Medium') echo 'selected'; ?>>
                                Medium
                            </option>

                            <option value="Low"
                            <?php if(($rowData[0]['priority'] ?? '')=='Low') echo 'selected'; ?>>
                                Low
                            </option>

                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Status</label>

                        <select name="status"  class="form-control">

                            <option value="0"
                            <?php if(($rowData[0]['status'] ?? '')=='0') echo 'selected'; ?>>
                                Active
                            </option>

                            <option value="1"
                            <?php if(($rowData[0]['status'] ?? '')=='1') echo 'selected'; ?>>
                                In-Active   
                            </option>

                        </select>
                    </div>

                </div>

                <button type="submit"
                        name="save"
                        class="btn btn-success">
                    Save Lead
                </button>

                <a href="manage_lead.php"
                   class="btn btn-secondary">
                    Clear
                </a>

            </form>

        </div>
    </div>

    <div class="card">

        <div class="card-header bg-dark text-white">
            <h4>Lead List</h4>
        </div>

        <div class="card-body">

            <table class="table table-bordered table-striped">

                <thead>
                <tr>
                    <th>ID</th>
                    <th>Contact Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Company</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>

                <tbody>

<?php  
 
$i=1;
foreach($leadList as $row){
?>


<tr>
    <td><?php echo $i++; ?></td>
    <td><?php echo htmlspecialchars($row['contact_name'] ?? ''); ?></td>
    <td><?php echo htmlspecialchars($row['phone'] ?? ''); ?></td>
    <td><?php echo htmlspecialchars($row['email'] ?? ''); ?></td>
    <td><?php echo htmlspecialchars($row['company'] ?? ''); ?></td>
    <td><?php echo htmlspecialchars($row['priority'] ?? ''); ?></td>
    <td>
        <?php echo ($row['status'] == 0) ? 'Active' : 'In-Active'; ?>
    </td>
    <td>
        <a href="?edit=<?php echo $row['id']; ?>"
           class="btn btn-warning btn-sm">
           Edit
        </a>

        <a href="?delete=<?php echo $row['id']; ?>"
           onclick="return confirm('Delete this lead?')"
           class="btn btn-danger btn-sm">
           Delete
        </a>
    </td>
</tr>

<?php
}
 
?>

</tbody>

            </table>

        </div>

    </div>

</div>

</body>
</html>