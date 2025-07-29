<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern WMS | Inbound Receiving</title>
    <!-- Font Awesome 5 CDN: -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <style>
        :root {
            --primary: #4361ee;
            --primary-dark: #233085;
            --success: #10b981;
            --danger: #ef4444;
            --dark: #212529;
            --gray: #6b7280;
            --gray-light: #e5e7eb;
        }
        body {
            margin:0; background-color: #f9fafb; color: var(--dark); line-height: 1.6; font-family: system-ui, sans-serif;
        }
        .container {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 240px;
            background: #fff;
            border-right: 1px solid var(--gray-light);
            min-height: 100vh;
            padding: 2rem 1rem 2rem 1.5rem;
        }
        .sidebar-header { display: flex; align-items: center; gap: 10px; margin-bottom: 2rem;}
        .sidebar-header i { color: var(--primary); font-size: 1.5rem;}
        .sidebar-header h2 { margin:0; font-size: 1.25rem;}
        .sidebar-menu .menu-title {
            font-size: 0.95rem; margin: 1.5rem 0 0.5rem 0.75rem; font-weight: 700; color: var(--gray);
        }
        .sidebar-menu .menu-item {
            display: flex; align-items: center; gap:12px; color: var(--dark);
            padding: 0.55rem 0.75rem; border-radius: 4px; margin-bottom: 0.2rem;
            text-decoration: none; transition: background 0.18s;
        }
        .sidebar-menu .menu-item.active, .sidebar-menu .menu-item:hover {
            background: var(--primary); color: #fff;
        }
        .sidebar-menu .menu-item.active i,
        .sidebar-menu .menu-item:hover i{
            color: #fff;
        }
        .main-content {
            flex: 1;
            padding: 2rem 2.5vw;
            overflow-x: auto;
        }
        .header {
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;
        }
        .header h1 { margin: 0;}
        .user-menu {
            display: flex; align-items: center; gap: 1rem;
        }
        .notification-icon {
            position: relative; font-size: 1.2rem;
        }
        .notification-badge {
            position: absolute; top: -5px; right: -7px; background: var(--danger); color: #fff;
            font-size: 0.7rem; min-width:1.1em; padding: 1.5px 6px; border-radius: 10px; text-align: center;
        }
        .user-info h4 { margin: 0; font-size: 1rem;}
        .user-info p { margin: 0; font-size: 0.9rem; color: var(--gray);}
        .user-menu img {
            width:40px; height:40px; object-fit:cover; border-radius:50%; border: 1px solid var(--gray-light);
        }
        .card {
            background: #fff; border-radius: 8px; padding: 1.5rem; box-shadow: 0 2px 6px rgba(0,0,0,0.045);
            margin-bottom: 1.5rem;
        }
        .card-header { padding-bottom: 1rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--gray-light);}
        .card-header h3 {font-size:1.15rem; margin:0;}
        .form-row { display: flex; gap: 1.5rem; margin-bottom: 1rem;}
        .form-group { flex:1; margin-bottom:1rem;}
        .form-group label { display:block; margin-bottom: 0.5rem; font-size:0.875rem; font-weight:500;}
        .form-control {
            width:100%; padding:0.75rem; border:1px solid var(--gray-light);
            border-radius:4px; font-size:0.92rem; transition: border-color 0.3s;
        }
        .form-control:focus {outline: none; border-color: var(--primary);}
        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%236b7280' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 0.75rem center;
            background-size: 16px 12px; padding-right: 2.2rem;
        }
        textarea.form-control {min-height: 70px; resize: vertical;}
        .btn {
            display: inline-flex; align-items: center; justify-content: center;
            padding: 0.65rem 1.3rem; border-radius:4px; font-size:0.93rem; font-weight:500; cursor:pointer; transition:all 0.25s; border:none;
        }
        .btn i { margin-right:8px;}
        .btn-primary {background: var(--primary); color:#fff;}
        .btn-primary:hover {background: var(--primary-dark);}
        .btn-secondary {background: var(--gray-light); color: var(--dark);}
        .btn-secondary:hover {background: #d1d5db;}
        .form-actions { display:flex; gap:1rem; margin-top:1.3rem;}
        .alert {
            padding:1rem; border-radius: 4px; margin-bottom:1.5rem; font-size: 0.95rem;
        }
        .alert-success {
            background-color: rgba(16,185,129,0.1); color: var(--success); border-left:3px solid var(--success);
        }
        .alert-error {
            background-color: rgba(239,68,68,0.1); color: var(--danger); border-left:3px solid var(--danger);
        }
        .data-table {width: 100%; border-collapse: collapse; font-size: 0.94rem;}
        .data-table th, .data-table td {
            padding: 0.7rem 1rem; text-align: left; border-bottom: 1px solid var(--gray-light);
        }
        .data-table th {
            font-weight: 600; color: var(--gray); text-transform: uppercase; font-size: 0.77rem; background-color: #f9fafb;
        }
        .data-table tr:hover td {background: rgba(67, 97, 238, 0.046);}
        @media (max-width: 1024px) {
            .container{ flex-direction: column;}
            .sidebar { width:100%; border-right: none; border-bottom: 1px solid var(--gray-light);}
        }
        @media (max-width: 768px) {
            .form-row,.form-actions {flex-direction: column; gap:0;}
            .btn {width:100%; margin-bottom: 0.5rem;}
            .data-table {display: block; overflow-x: auto;}
            .main-content {padding: 1rem;}
            .sidebar {padding-left: 1rem;}
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Sidebar -->
    <aside class="sidebar" aria-label="Sidebar Navigation">
        <div class="sidebar-header">
            <i class="fas fa-warehouse"></i>
            <h2>Modern WMS</h2>
        </div>
        <div class="sidebar-menu">
            <div class="menu-title">Main</div>
            <a href="dashboard.php" class="menu-item"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
            <a href="inventory.php" class="menu-item"><i class="fas fa-boxes"></i><span>Inventory</span></a>
            <a href="receiving.php" class="menu-item active"><i class="fas fa-truck"></i><span>Receiving</span></a>
            <a href="shipping.php" class="menu-item"><i class="fas fa-shipping-fast"></i><span>Shipping</span></a>
            <a href="transfers.php" class="menu-item"><i class="fas fa-exchange-alt"></i><span>Transfers</span></a>

            <div class="menu-title">Management</div>
            <a href="users.php" class="menu-item"><i class="fas fa-users"></i><span>Users</span></a>
            <a href="locations.php" class="menu-item"><i class="fas fa-map-marker-alt"></i><span>Locations</span></a>
            <a href="products.php" class="menu-item"><i class="fas fa-barcode"></i><span>Products</span></a>
            <a href="reports.php" class="menu-item"><i class="fas fa-chart-line"></i><span>Reports</span></a>

            <div class="menu-title">System</div>
            <a href="settings.php" class="menu-item"><i class="fas fa-cog"></i><span>Settings</span></a>
            <a href="help.php" class="menu-item"><i class="fas fa-question-circle"></i><span>Help</span></a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="header">
            <h1>Inbound Receiving</h1>
            <div class="user-menu">
                <div class="notification-icon">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">3</span>
                </div>
                <div class="user-info">
                    <h4><!--?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?-->User</h4>
                    <p><!--?php echo htmlspecialchars($_SESSION['user_role'] ?? 'Role'); ?-->Manager</p>
                </div>
                <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="User" />
            </div>
        </div>

        <!-- Sample Success and Error Message placeholders -->
        <!--
        -- Insert server logic here --
        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        -->

        <!-- Receiving Form -->
        <div class="card">
            <div class="card-header"><h3>Receive Products</h3></div>
            <div class="card-body">
                <form method="POST" action="receiving.php">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="supplier_id">Supplier</label>
                            <select id="supplier_id" name="supplier_id" class="form-control">
                                <option value="">Select Supplier</option>
                                <!--?php foreach ($suppliers as $supplier): ?-->
                                <option value="1">ACME Supplies</option>
                                <option value="2">WMS Direct</option>
                                <!--?php endforeach; ?-->
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="product_id">Product</label>
                            <select id="product_id" name="product_id" class="form-control" required>
                                <option value="">Select Product</option>
                                <!--?php foreach ($products as $product): ?-->
                                <option value="10">PRD-001 - Widget A</option>
                                <option value="20">PRD-002 - Widget B</option>
                                <!--?php endforeach; ?-->
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="quantity">Quantity</label>
                            <input type="number" id="quantity" name="quantity" class="form-control" min="1" required>
                        </div>
                        <div class="form-group">
                            <label for="location_id">Storage Location</label>
                            <select id="location_id" name="location_id" class="form-control" required>
                                <option value="">Select Location</option>
                                <!--?php foreach ($locations as $location): ?-->
                                <option value="5">Zone A - Rack 1 - Shelf 2</option>
                                <option value="6">Zone B - Rack 2 - Shelf 4</option>
                                <!--?php endforeach; ?-->
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="batch_number">Batch/Lot Number</label>
                            <input type="text" id="batch_number" name="batch_number" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="expiry_date">Expiry Date</label>
                            <input type="date" id="expiry_date" name="expiry_date" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea id="notes" name="notes" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check"></i> Receive Products
                        </button>
                        <button type="reset" class="btn btn-secondary">
                            <i class="fas fa-undo"></i> Reset
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Recent Receiving Activity (example static contents) -->
        <div class="card">
            <div class="card-header"><h3>Recent Receiving Activity</h3></div>
            <div class="card-body">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Location</th>
                            <th>Supplier</th>
                            <th>Received By</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!--?php foreach ($recent_receiving as $receiving): ?-->
                        <tr>
                            <td>2024-06-10 11:21</td>
                            <td>PRD-001 - Widget A</td>
                            <td>42</td>
                            <td>A-1-2</td>
                            <td>ACME Supplies</td>
                            <td>John Doe</td>
                        </tr>
                        <tr>
                            <td>2024-06-09 15:02</td>
                            <td>PRD-002 - Widget B</td>
                            <td>16</td>
                            <td>B-2-4</td>
                            <td>WMS Direct</td>
                            <td>Lisa Smith</td>
                        </tr>
                        <!--?php endforeach; ?-->
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
<script>
    // Sidebar menu active highlighting (if needed via JS), optional and must not affect server-side selection
    document.querySelectorAll('.sidebar .menu-item').forEach(item => {
        item.addEventListener('click', function() {
            document.querySelectorAll('.sidebar .menu-item').forEach(i => i.classList.remove('active'));
            this.classList.add('active');
        });
    });
    // Demo: Show which product selected (for further enhancements)
    document.getElementById('product_id')?.addEventListener('change', function () {
        // You may fetch after selecting product
        // console.log("Selected product:", this.value);
    });
</script>
</body>
</html>
