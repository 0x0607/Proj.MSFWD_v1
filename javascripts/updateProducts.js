function setProductStatus(product_id, status) {
  $.ajax({
    url: "/api/api.updateProduct.php",
    method: "POST",
    data: {
      action: "change_status",
      product_id: product_id,
      product_status: status,
    },
    success: function (res) {
      console.log(res);
    },
    error: function (err) {
      console.log(err);
    },
  });
}
function addProduct(id) {
  productName = document.getElementById(id + "_name").value;
  productPrice = parseFloat(document.getElementById(id + "_price").value);
  productQuantity = parseInt(document.getElementById(id + "_quantity").value);

  $.ajax({
    url: "/api/api.updateProduct.php",
    method: "POST",
    data: {
      action: "add_product",
      product_name: productName,
      product_price: productPrice,
      product_quantity: productQuantity,
    },
    success: function (res) {
      console.log(res);
      if ($.isEmptyObject(res)) return false;
      location.reload();
    },
    error: function (err) {
      console.log(err);
    },
  });
}
function deleteProduct(product_id) {
  $.ajax({
    url: "/api/api.updateProduct.php",
    method: "POST",
    data: {
      action: "change_status",
      product_id: product_id,
      product_status: -1,
    },
    success: function (res) {
      console.log(res);
      location.reload();
    },
    error: function (err) {
      console.log(err);
    },
  });
}

function updateProduct(id) {
  productName = document.getElementById(id + "_name").value;
  productPrice = parseFloat(document.getElementById(id + "_price").value);
  productQuantity = parseInt(document.getElementById(id + "_quantity").value);
  productDescription = document.getElementById(id + "_description").value;

  $.ajax({
    url: "/api/api.updateProduct.php",
    method: "POST",
    data: {
      action: "update_product",
      product_name: productName,
      product_price: productPrice,
      product_quantity: productQuantity,
      product_description: productDescription
    },
    success: function (res) {
      console.log(res);
      if ($.isEmptyObject(res)) return false;
      // location.reload();
    },
    error: function (err) {
      console.log(err);
    },
  });
}