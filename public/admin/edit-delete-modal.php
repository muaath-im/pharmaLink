<!-- Edit Modal -->
<div class="modal fade" id="edit_<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <center><h4 class="modal-title" id="myModalLabel">Edit Item</h4></center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form method="POST" action="edit.php">
                        <?php echo csrfField(); ?>
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <div class="row form-group">
                            <div class="col-sm-3">
                                <label class="control-label modal-label">Item Name:</label>
                            </div>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="itemname" value="<?php echo htmlspecialchars($row['itemname']); ?>" required>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-3">
                                <label class="control-label modal-label">Category:</label>
                            </div>
                            <div class="col-sm-9">
                                <select name="type" class="form-control" required>
                                    <option value="<?php echo htmlspecialchars($row['type']); ?>"><?php echo htmlspecialchars($row['type']); ?></option>
                                    <option value="Kids Items">Kids Items</option>
                                    <option value="Women Items">Women Items</option>
                                    <option value="Men Items">Men Items</option>
                                    <option value="Animal Items">Animal Items</option>
                                    <option value="Common Things">Common Things</option>
                                </select>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-3">
                                <label class="control-label modal-label">Quantity:</label>
                            </div>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" name="quantity" value="<?php echo htmlspecialchars((string)$row['quantity']); ?>" required>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-3">
                                <label class="control-label modal-label">Details:</label>
                            </div>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="itemdetail" value="<?php echo htmlspecialchars($row['itemdetail']); ?>" required>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-3">
                                <label class="control-label modal-label">Image:</label>
                            </div>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="image" value="<?php echo htmlspecialchars($row['image']); ?>" required>
                            </div>
                        </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancel</button>
                <button type="submit" name="edit" class="btn btn-success"><span class="glyphicon glyphicon-check"></span> Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="delete_<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <center><h4 class="modal-title" id="myModalLabel">Delete Item</h4></center>
            </div>
            <div class="modal-body">
                <p class="text-center">Are you sure you want to delete this item?</p>
                <h3 class="text-center"><?php echo htmlspecialchars($row['itemname'] . ' (' . $row['type'] . ')'); ?></h3>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancel</button>
                <form method="POST" action="delete.php" style="display:inline;">
                    <?php echo csrfField(); ?>
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <button type="submit" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span> Yes, Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
