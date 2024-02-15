<?php require_once('header.php'); ?>
<?php
$error_message = '';
$success_message = '';

if(isset($_POST['form1'])) {
    $valid = true;
    if(empty($_POST['tcat_id'])) {
        $valid = false;
        $error_message .= "You must select a top level category.<br>";
    } else {
        echo "Top level category selected successfully.<br>";
    }
    if(empty($_POST['mcat_id'])) {
        $valid = false;
        $error_message .= "You must select a mid level category.<br>";
    } else {
        echo "Mid level category selected successfully.<br>";
    }
    if(empty($_POST['ecat_id'])) {
        $valid = false;
        $error_message .= "You must select an end level category.<br>";
    } else {
        echo "End level category selected successfully.<br>";
    }
    if(empty($_POST['p_name'])) {
        $valid = false;
        $error_message .= "Product name cannot be empty.<br>";
    } else {
        echo "Product name entered successfully.<br>";
    }
    if(empty($_POST['p_current_price'])) {
        $valid = false;
        $error_message .= "Current Price cannot be empty.<br>";
    } else {
        echo "Current Price entered successfully.<br>";
    }
    if(empty($_POST['p_qty'])) {
        $valid = false;
        $error_message .= "Quantity cannot be empty.<br>";
    } else {
        echo "Quantity entered successfully.<br>";
    }
    if($valid) {
        try {
            // Check if PDO is connected
            var_dump($pdo);

            $pdo->beginTransaction();
            $statement = $pdo->prepare("INSERT INTO tbl_product (
                p_name,
                p_old_price,
                p_current_price,
                p_qty,
                p_featured_photo,
                p_description,
                p_short_description,
                p_feature,
                p_condition,
                p_return_policy,
                p_total_view,
                p_is_featured,
                p_is_active,
                ecat_id
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $statement->execute([
                $_POST['p_name'],
                $_POST['p_old_price'],
                $_POST['p_current_price'],
                $_POST['p_qty'],
                $final_name,
                $_POST['p_description'],
                $_POST['p_short_description'],
                $_POST['p_feature'],
                $_POST['p_condition'],
                $_POST['p_return_policy'],
                0,
                $_POST['p_is_featured'],
                $_POST['p_is_active'],
                $_POST['ecat_id']
            ]);

            // Check if the product was inserted successfully
            var_dump($pdo->lastInsertId());

            if(isset($_FILES['photo'])) {
                foreach($_FILES['photo']['tmp_name'] as $key => $tmp_name) {
                    if($_FILES['photo']['error'][$key] == UPLOAD_ERR_OK) {
                        $photo_name = $_FILES['photo']['name'][$key];
                        $photo_tmp = $_FILES['photo']['tmp_name'][$key];
                        $photo_ext = pathinfo($photo_name, PATHINFO_EXTENSION);
                        $photo_final_name = 'product-photo-' . uniqid() . '.' . $photo_ext;
                        move_uploaded_file($photo_tmp, "../assets/uploads/product_photos/" . $photo_final_name);
                        $statement = $pdo->prepare("INSERT INTO tbl_product_photo (photo, p_id) VALUES (?, ?)");
                        $statement->execute([$photo_final_name, $pdo->lastInsertId()]);
                    } else {
                        $error_message .= "Error uploading file: " . $_FILES['photo']['error'][$key] . "<br>";
                    }
                }
            }

            $pdo->commit();
            $success_message = 'Product is added successfully.';
        } catch (Exception $e) {
            $pdo->rollBack();
            $error_message = 'Error adding product: ' . $e->getMessage();
        }
    }
}
?>
<section class="content-header">
    <div class="content-header-left">
        <h1>Add Product</h1>
    </div>
    <div class="content-header-right">
        <a href="product.php" class="btn btn-primary btn-sm">View All</a>
    </div>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <?php if($error_message): ?>
                <div class="callout callout-danger">
                    <p><?php echo $error_message; ?></p>
                </div>
            <?php endif; ?>

            <?php if($success_message): ?>
                <div class="callout callout-success">
                    <p><?php echo $success_message; ?></p>
                </div>
            <?php endif; ?>
            <form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
                <div class="box box-info">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Top Level Category Name <span>*</span></label>
                            <div class="col-sm-4">
                                <select name="tcat_id" id="tcat_id" class="form-control select2 top-cat" oninput="logFormData('tcat_id')">
                                    <option value="">Select Top Level Category</option>
                                    <?php
                                    $statement = $pdo->prepare("SELECT * FROM tbl_top_category ORDER BY tcat_name ASC");
                                    $statement->execute();
                                    $result = $statement->fetchAll(PDO::FETCH_ASSOC);    
                                    foreach ($result as $row) {
                                        ?>
                                        <option value="<?php echo $row['tcat_id']; ?>"><?php echo $row['tcat_name']; ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Mid Level Category Name <span>*</span></label>
                            <div class="col-sm-4">
                                <select name="mcat_id" id="mcat_id" class="form-control select2 mid-cat" oninput="logFormData('mcat_id')">
                                    <option value="">Select Mid Level Category</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">End Level Category Name <span>*</span></label>
                            <div class="col-sm-4">
                                <select name="ecat_id" id="ecat_id" class="form-control select2 end-cat" oninput="logFormData('ecat_id')">
                                    <option value="">Select End Level Category</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Product Name <span>*</span></label>
                            <div class="col-sm-4">
                                <input type="text" name="p_name" id="p_name" class="form-control" oninput="logFormData('p_name')">
                            </div>
                        </div>    
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Old Price <br><span style="font-size:10px;font-weight:normal;">(In ₹)</span></label>
                            <div class="col-sm-4">
                                <input type="text" name="p_old_price" id="p_old_price" class="form-control" oninput="logFormData('p_old_price')">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Current Price <span>*</span><br><span style="font-size:10px;font-weight:normal;">(In ₹)</span></label>
                            <div class="col-sm-4">
                                <input type="text" name="p_current_price" id="p_current_price" class="form-control" oninput="logFormData('p_current_price')">
                            </div>
                        </div>    
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Quantity <span>*</span></label>
                            <div class="col-sm-4">
                                <input type="text" name="p_qty" id="p_qty" class="form-control" oninput="logFormData('p_qty')">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Select Size</label>
                            <div class="col-sm-4">
                                <select name="size[]" id="size" class="form-control select2" multiple="multiple" oninput="logFormData('size')">
                                    <?php
                                    $statement = $pdo->prepare("SELECT * FROM tbl_size ORDER BY size_id ASC");
                                    $statement->execute();
                                    $result = $statement->fetchAll(PDO::FETCH_ASSOC);            
                                    foreach ($result as $row) {
                                        ?>
                                        <option value="<?php echo $row['size_id']; ?>"><?php echo $row['size_name']; ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Select Color</label>
                            <div class="col-sm-4">
                                <select name="color[]" id="color" class="form-control select2" multiple="multiple" oninput="logFormData('color')">
                                    <?php
                                    $statement = $pdo->prepare("SELECT * FROM tbl_color ORDER BY color_id ASC");
                                    $statement->execute();
                                    $result = $statement->fetchAll(PDO::FETCH_ASSOC);            
                                    foreach ($result as $row) {
                                        ?>
                                        <option value="<?php echo $row['color_id']; ?>"><?php echo $row['color_name']; ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Featured Photo <span>*</span></label>
                            <div class="col-sm-4" style="padding-top:4px;">
                                <input type="file" name="p_featured_photo" id="p_featured_photo" oninput="logFormData('p_featured_photo')">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Other Photos</label>
                            <div class="col-sm-4" style="padding-top:4px;">
                                <table id="ProductTable" style="width:100%;">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="upload-btn">
                                                    <input type="file" name="photo[]" id="photo" style="margin-bottom:5px;" oninput="logFormData('photo')">
                                                </div>
                                            </td>
                                            <td style="width:28px;"><a href="javascript:void()" class="Delete btn btn-danger btn-xs">X</a></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-sm-2">
                                <input type="button" id="btnAddNew" value="Add Item" style="margin-top: 5px;margin-bottom:10px;border:0;color: #fff;font-size: 14px;border-radius:3px;" class="btn btn-warning btn-xs">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Description</label>
                            <div class="col-sm-8">
                                <textarea name="p_description" id="p_description" class="form-control" cols="30" rows="10" id="editor1" oninput="logFormData('p_description')"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Short Description</label>
                            <div class="col-sm-8">
                                <textarea name="p_short_description" id="p_short_description" class="form-control" cols="30" rows="10" id="editor2" oninput="logFormData('p_short_description')"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Features</label>
                            <div class="col-sm-8">
                                <textarea name="p_feature" id="p_feature" class="form-control" cols="30" rows="10" id="editor3" oninput="logFormData('p_feature')"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Conditions</label>
                            <div class="col-sm-8">
                                <textarea name="p_condition" id="p_condition" class="form-control" cols="30" rows="10" id="editor4" oninput="logFormData('p_condition')"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Return Policy</label>
                            <div class="col-sm-8">
                                <textarea name="p_return_policy" id="p_return_policy" class="form-control" cols="30" rows="10" id="editor5" oninput="logFormData('p_return_policy')"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Is Featured?</label>
                            <div class="col-sm-8">
                                <select name="p_is_featured" id="p_is_featured" class="form-control" style="width:auto;" oninput="logFormData('p_is_featured')">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select> 
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Is Active?</label>
                            <div class="col-sm-8">
                                <select name="p_is_active" id="p_is_active" class="form-control" style="width:auto;" oninput="logFormData('p_is_active')">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select> 
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label"></label>
                            <div class="col-sm-6">
                                <button type="submit" class="btn btn-success pull-left" name="form1">Add Product</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
<?php require_once('footer.php'); ?>

<script>
    function logFormData(fieldName) {
    const field = document.getElementById(fieldName);
    if (field) {
        console.log(`${fieldName}: ${field.value}`);
    } else {
        console.log(`Field with id ${fieldName} not found.`);
    }
}
</script>
