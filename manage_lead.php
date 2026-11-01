<?php
session_start();

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

include 'lib/api_helper.php';
include("config/db.php");
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['id'];
$search = "";

if (isset($_GET['search'])){
    $search = $_GET['search']?? '';
}
//     $query = "SELECT id, sales_person_id, contact_name, phone, email, company, priority, status
//               FROM leads
//               WHERE sales_person_id='$user_id'
//               AND contact_name LIKE '%$search%'";
// } else {
//     $query = "SELECT id, sales_person_id, contact_name, phone, email, company, priority, status
//               FROM leads
//               WHERE sales_person_id='$user_id'";
// }

$result = awsapi(
        [            
            "user_id" => $user_id,
            "search" => $search
        ],
        "managelead_list"
    );
    // echo "<pre>";
    // print_r($result);
    // exit;


if (isset($_GET['id'])) {

    $lead_details = awsapi(
        [
            "user_id" => $user_id,
            "id" => $_GET['id']
        ],
        "managelead_get"
    );

    //echo "<pre>";print_r($lead_details) ;exit;

    if (isset($lead_details['body'])) {
        $lead_details = json_decode($lead_details['body'], true);
    }
}
// print_r($_SESSION);exit;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lead Management</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <div class="row ">
        
        <div class="col-4">
           <h1>Manage Lead</h1>
        </div>
        <div class="col-4"><?php echo $_SESSION['username']; ?>(<?php echo $_SESSION['role']; ?>)</div>
        <div class="col-4 text-end">
            <a href="dashboard.php" class="btn btn-danger">Dashboard</a>
          <a href="logout.php" class="btn btn-danger">Logout</a>
        </div> 
    </div>


    <div class="card border-0 rounded-0">
        <div class="card-header bg-primary text-white py-3 fs-5 fw-medium">
         Add New Lead
        </div>
        
        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'success'): ?>
        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
            <strong>Success!</strong> New lead record has been added successfully.
           
        </div>
        <?php elseif (isset($_GET['msg']) && $_GET['msg'] == 'edit_success'): ?>
        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
            <strong>Success!</strong> Your lead record has been edited successfully.
        </div>
        <?php elseif (isset($_GET['msg']) && $_GET['msg'] == 'delete_success'): ?>
        <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
            <strong>Success!</strong> Your lead record has been deleted successfully.
        </div>
        
    <?php endif; ?>
        <div class="card-body bg-light-subtle p-4 border-bottom border-2">
            <form action="manage_action.php" method="POST">
                    <input type="hidden" name="edit_id" value="<?php echo $lead_details['id'] ?? ''?>">
                    <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label  text-secondary fw-semibold">Contact Name</label>
                        <input type="text" class="form-control" name="contact_name" required value="<?php echo $lead_details['contact_name'] ?? ''?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label  text-secondary fw-semibold">Phone</label>
                        <input type="text" class="form-control" name="phone" required value="<?php echo $lead_details['phone'] ?? ''?>">
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label text-secondary fw-semibold">Email</label>
                        <input type="email" class="form-control" name="email" required value="<?php echo $lead_details['email'] ?? ''?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-secondary fw-semibold">Company</label>
                        <input type="text" class="form-control" name="company" required value="<?php echo $lead_details['company'] ?? ''?>" >
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label text-secondary">Priority</label>
                        <select class="form-select text-black " id="priority" name="priority">
                            <option value="High" <?php  if(isset($lead_details['priority'])&& $lead_details['priority']=="High"){?>selected <?php }?>>High</option>
                            <option value="Medium" <?php  if(isset($lead_details['priority'])&& $lead_details['priority']=="Medium"){?>selected <?php }?>>Medium</option>
                            <option value="Low" <?php  if(isset($lead_details['priority'])&& $lead_details['priority']=="Low"){?>selected <?php }?>>Low</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-secondary">Status</label>
                        <select class="form-select" name="status">
                            <option value="0">Active</option>
                            <option value="1">In-Active</option>
                        </select>
                    </div>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success px-4 fw-medium">Save Lead</button>
                    <button type="reset" class="btn btn-secondary px-4 fw-medium">Clear</button>
                </div>
            </form>
        </div>
    </div>
    <div class="row mt-3 mb-3">
    <div class="col-md-6">
        <form method="GET" class="d-flex">
            <input type="text"
                   name="search"
                   class="form-control me-2"
                   placeholder="Search Contact Name"
                   value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">

            <button type="submit" class="btn btn-primary me-2">
                Search
            </button>

            <a href="manage_lead.php" class="btn btn-secondary">
                Clear
            </a>
        </form>
    </div>
</div>

    <div class="card border-0 rounded-0">
        <div class="card-header bg-dark text-white py-3 fs-5 fw-medium">
            Lead List

    
        </div>
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle  table-bordered border-light-subtle">
                    <thead class="table-light">
                        <tr class="text-secondary">
                            <th>ID</th>
                             
                            <th>Contact_Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Company</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th style="width: 140px;">Action</th>
                        </tr>
                    </thead>
                   <tbody>
<?php
if (!empty($result)) {
    $i = 1;

    foreach ($result as $row) {
?>
<tr>
    <td><?php echo $i++; ?></td>

    <td><?php echo htmlspecialchars($row['contact_name']); ?></td>

    <td><?php echo htmlspecialchars($row['phone']); ?></td>

    <td><?php echo htmlspecialchars($row['email']); ?></td>

    <td><?php echo htmlspecialchars($row['company']); ?></td>

    <td>
        <?php
        if ($row['priority'] == "High") {
            echo '<span class="badge bg-danger">High</span>';
        } elseif ($row['priority'] == "Medium") {
            echo '<span class="badge bg-warning text-dark">Medium</span>';
        } else {
            echo '<span class="badge bg-success">Low</span>';
        }
        ?>
    </td>

    <td>
        <?php
        echo ($row['status'] == 1) ? "In-Active" : "Active";
        ?>
    </td>

    <td>
        <a href="manage_lead.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">
            Edit
        </a>

        <a href="manage_action.php?delete_id=<?php echo $row['id']; ?>"
           class="btn btn-danger btn-sm"
           onclick="return confirm('Are you sure you want to delete this lead?');">
            Delete
        </a>
    </td>
</tr>

<?php
    }
} else {
?>
<tr>
    <td colspan="8" class="text-center">No Records Found</td>
</tr>
<?php
}
?>
</tbody>
                </table>
            </div>
        </div>
    </div>

</div>
</body>
</html>