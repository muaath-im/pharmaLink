<!-- Add New Item Modal -->
<div class="modal fade" id="addnew" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <center><h4 class="modal-title" id="myModalLabel">Add New Item</h4></center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form method="POST" action="add.php">
                        <?php echo csrfField(); ?>
                        <div class="row form-group">
                            <div class="col-sm-3">
                                <label class="control-label modal-label">Item Name:</label>
                            </div>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="itemname" required>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-3">
                                <label class="control-label modal-label">Category:</label>
                            </div>
                            <div class="col-sm-9">
                                <select name="type" class="form-control" required>
                                    <option value="">Select Category</option>
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
                                <input type="number" class="form-control" name="quantity" required>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-3">
                                <label class="control-label modal-label">Details:</label>
                            </div>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="itemdetail" required>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-3">
                                <label class="control-label modal-label">Image Filename:</label>
                            </div>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="image" placeholder="e.g. panadol.jpg" required>
                            </div>
                        </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancel</button>
                <button type="submit" name="add" class="btn btn-primary"><span class="glyphicon glyphicon-floppy-disk"></span> Save</button>
                </form>
            </div>
        </div>
    </div>
</div>
