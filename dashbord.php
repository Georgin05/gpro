<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Set the admin name using the username stored in session
$admin = [
    'name' => $_SESSION['username']
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Warehouse Dashboard</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
    }

    .wrapper {
      display: flex;
      height: 100vh;
    }

    .topbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 30px;
      background-color: #343a40;
      color: white;
    }

    .logout-button {
      text-decoration: none;
      background-color: #dc3545;
      color: white;
      padding: 8px 16px;
      border-radius: 4px;
    }

    .logout-button:hover {
      background-color: #c82333;
    }

    .sidebar {
      width: 250px;
      background-color: #2c3e50;
      color: white;
      padding: 20px;
    }

    .sidebar h2 {
      margin-top: 0;
      font-size: 24px;
      margin-bottom: 20px;
    }

    .sidebar-note {
      background-color: #1a252f;
      padding: 10px;
      text-align: center;
      border-radius: 6px;
      font-size: 14px;
      color: #f1f1f1;
      margin-top: 20px;
    }

    .menu {
      list-style: none;
      padding: 0;
    }

    .menu > li {
      margin-bottom: 10px;
    }

    .menu-item {
      cursor: pointer;
      display: block;
      padding: 10px;
      background-color: #34495e;
      border-radius: 4px;
    }

    .menu-item:hover {
      background-color: #3c5d7a;
    }

    .submenu {
      list-style: none;
      margin: 0;
      padding-left: 20px;
      display: none;
    }

    .submenu li {
      background-color: #3c5d7a;
      padding: 8px;
      margin: 4px 0;
      border-radius: 4px;
      cursor: pointer;
    }

    .submenu li:hover {
      background-color: #4a6a89;
    }

    .submenu li a {
  display: block;
  text-decoration: none;
  color: white;
}

.submenu li a:hover {
  color: #ffe082;
}


    .content {
      flex-grow: 1;
      padding: 30px;
    }
  </style>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<div class="topbar">
  <div><strong>Admin Dashboard</strong></div>
  <a class="logout-button" href="logout.php">Logout</a>
</div>

<div class="wrapper">
  <aside class="sidebar">
    <div><strong>Welcome, <?= htmlspecialchars($admin['name']) ?></strong></div>

   <ul class="menu">
  <li>
    <span class="menu-item">Products ▼</span>
    <ul class="submenu">
      <li><a href="add_product.php" target="content-frame">Add Product</a></li>
      <li><a href="view_products.php" target="content-frame">View Products</a></li>
    </ul>
  </li>
  <li>
    <span class="menu-item">Orders ▼</span>
    <ul class="submenu">
      <li><a href="new_orders.php" target="content-frame">New Orders</a></li>
      <li><a href="order_history.php" target="content-frame">Order History</a></li>
    </ul>
  </li>
  <li>
    <span class="menu-item">Customers ▼</span>
    <ul class="submenu">
      <li><a href="add_customer.php" target="content-frame">Add Customer</a></li>
      <li><a href="customer_list.php" target="content-frame">Customer List</a></li>
    </ul>
  </li>
  <li>
    <span class="menu-item">Suppliers ▼</span>
    <ul class="submenu">
      <li><a href="add_supplier.php" target="content-frame">Add Supplier</a></li>
      <li><a href="supplier_list.php" target="content-frame">Supplier List</a></li>
    </ul>
  </li>
</ul>

    <div class="sidebar-note">
      👋 Yo, Admin!
    </div>
  </aside>

 <main class="content">
  <iframe name="content-frame" style="width: 100%; height: 80vh; border: none;"></iframe>
</main>

</div>

<script>
// Toggle submenu on click
$(document).ready(function () {
  $(".menu-item").click(function () {
    $(this).next(".submenu").slideToggle();
  });
});
</script>

</body>
</html>




