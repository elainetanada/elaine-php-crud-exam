<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link href="/assets/css/main.css" rel="stylesheet">
</head>

<body>
    <div class="container">
    
        <div style="text-align: right;">
            <button class="btn btn-primary"><a href="/index.php">Display List</a></button>
        </div>
        <h3>Add New Product</h3>
        <hr>
        <form class="needs-validation" id="productForm">
            <div class="row ">
                <div class="col-md-4 mt-2">
                    <label for="validationName" class="form-label">Product Name</label>
                    <input name="name" type="text" class="form-control" id="validationName" aria-describedby="inputGroupPrepend" required>
                    <div class="invalid-feedback">
                        Name field is required
                    </div>
                </div>
                <div class="col-md-4 mt-2">
                    <label for="validationUnit" class="form-label">Unit</label>
                    <input name="unit" type="text" class="form-control" id="validationUnit" aria-describedby="inputGroupPrepend" required>
                    <div class="invalid-feedback">
                        Unit field is required
                    </div>
                </div>
                <div class="col-md-4 mt-2">
                    <label for="validationPrice" class="form-label">Price</label>
                    <input name="price" type="text" class="form-control" id="validationPrice" aria-describedby="inputGroupPrepend" required>
                    <div class="invalid-feedback">
                        Price field is required
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mt-2">
                    <label for="validationExpiryDate" class="form-label">Expiry Date</label>
                    <input name="expiry_date" type="date" class="form-control" id="validationExpiryDate" aria-describedby="inputGroupPrepend" required>
                    <div class="invalid-feedback">
                        Expiry Date field is required
                    </div>
                </div>
                <div class="col-md-4 mt-2">
                    <label for="validationAvailablePrice" class="form-label">Available Inventory</label>
                    <input name="available_inventory" type="text" class="form-control" id="validationAvailablePrice" aria-describedby="inputGroupPrepend" required>
                    <div class="invalid-feedback">
                        Available Inventory field is required
                    </div>
                </div>
                <div class="col-md-4 mt-2">
                    <label for="validationImage" class="form-label">Image</label>
                    <input name="image" type="file" class="form-control product-image" id="validationImage" aria-describedby="inputGroupPrepend" accept="image/*" required>
                    <div class="invalid-feedback">
                        Image field is required
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12">
                    <button class="btn btn-primary" type="button" onclick="getFormValues()">Save</button>
                </div>
            </div>
                
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>
<script>

  // Fetch all the forms we want to apply custom Bootstrap validation styles to
  const forms = document.querySelectorAll('.needs-validation')
  function getFormValues() {
    let isFormValid = true;
    // Get the form element
    var form = document.getElementById("productForm");

    // Loop and validate each fields
    Array.from(forms).forEach(form => {
        if (!form.checkValidity()) {
            
            event.stopPropagation();
            isFormValid = false;
        }
        
        form.classList.add('was-validated')
    })
    // Create a FormData object from the form
    var formData = new FormData(form);
    formData.append("action", "create")
    if(isFormValid) {
        var productImage = $('.product-image').prop('files')[0];
        if(productImage) {
            formData.append("image", productImage);
        }

        // Submit form via AJAX
        $.ajax({
            url: config.base_url + 'crud/CRUD.php',
            method: 'POST',
            dataType: 'json',
            data: formData,
            contentType: false,
            processData: false,
            success: function (data) {
                console.log(data)
                window.location.href = config.base_url + "index.php?message="+data.message;

            },
            error: function (xhr, status, error) {
                console.log(error);
            }
        });
    }
}

</script>