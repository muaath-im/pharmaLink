<?php
/**
 * admin/index.php
 * PharmaLink Admin Panel — Inventory management (CRUD).
 * Lists all items from the 'members' table with DataTables.
 */
session_start();

require_once dirname(__DIR__) . '/src/config/auth_guard.php';
require_once dirname(__DIR__) . '/src/config/csrf.php';

requireAdmin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Admin Panel | PharmaLink</title>
    <link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/datatable/dataTable.bootstrap.min.css">
    <link rel="icon" type="image/png" href="/assets/images/logo.png">
    <style>
        .height10 { height: 10px; }
        .mtop10   { margin-top: 10px; }
        .modal-label { position: relative; top: 7px; }
    </style>
</head>
<body>
<div class="container">
    <div class="text-center" style="margin-top: 25px; margin-bottom: 10px;">
        <a href="/pages/home.html"><img src="/assets/images/logo.png" alt="PharmaLink Logo" style="max-height: 65px; width: auto;"></a>
    </div>
    <h1 class="page-header text-center" style="margin-top: 10px;">Inventory Management</h1>
    <div class="row">
        <div class="col-sm-8 col-sm-offset-2">
            <div class="row">
                <?php
                if (isset($_SESSION['error'])) {
                    echo "<div class='alert alert-danger text-center'>
                            <button class='close'>&times;</button>
                            " . htmlspecialchars($_SESSION['error']) . "
                          </div>";
                    unset($_SESSION['error']);
                }
                if (isset($_SESSION['success'])) {
                    echo "<div class='alert alert-success text-center'>
                            <button class='close'>&times;</button>
                            " . htmlspecialchars($_SESSION['success']) . "
                          </div>";
                    unset($_SESSION['success']);
                }
                ?>
            </div>
            <div class="row">
                <a href="#addnew" data-toggle="modal" class="btn btn-primary">
                    <span class="glyphicon glyphicon-plus"></span> New
                </a>
            </div>
            <div class="height10"></div>
            <div class="row">
                <table id="myTable" class="table table-bordered table-striped">
                    <thead>
                        <th>Item Code</th>
                        <th>Item Name</th>
                        <th>Category Type</th>
                        <th>Quantity</th>
                        <th>Item Details</th>
                        <th>Item Image</th>
                        <th>Action</th>
                    </thead>
                    <tbody>
                        <?php
                        include_once('db.php');
                        $sql   = 'SELECT * FROM members';
                        $query = $conn->query($sql);
                        while ($row = $query->fetch_assoc()) {
                            echo "<tr>
                                <td>" . htmlspecialchars((string)$row['id'])         . "</td>
                                <td>" . htmlspecialchars($row['itemname'])            . "</td>
                                <td>" . htmlspecialchars($row['type'])               . "</td>
                                <td>" . htmlspecialchars((string)$row['quantity'])   . "</td>
                                <td>" . htmlspecialchars($row['itemdetail'])         . "</td>
                                <td><img src='data:image/jpeg;base64," . base64_encode($row['image']) . "' height='100' width='100'/></td>
                                <td>
                                    <a href='#edit_{$row['id']}' class='btn btn-success btn-sm' data-toggle='modal'><span class='glyphicon glyphicon-edit'></span> Edit</a>
                                    <a href='#delete_{$row['id']}' class='btn btn-danger btn-sm' data-toggle='modal'><span class='glyphicon glyphicon-trash'></span> Delete</a>
                                </td>
                            </tr>";
                            include('edit-delete-modal.php');
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include('add-modal.php'); ?>

<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
<script src="vendor/datatable/jquery.dataTables.min.js"></script>
<script src="vendor/datatable/dataTable.bootstrap.min.js"></script>
<script>
$(document).ready(function () {
    $('#myTable').DataTable();
    $(document).on('click', '.close', function () {
        $('.alert').hide();
    });
});
</script>
</body>
</html>
