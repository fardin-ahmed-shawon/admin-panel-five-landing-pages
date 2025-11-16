<nav class="sidebar sidebar-offcanvas" id="sidebar">
          <ul class="nav">
            <li class="nav-item nav-profile">
              <a href="" class="nav-link">
                <div class="nav-profile-image">
                  <img src="img/admin.jpg" alt="profile" />
                  <span class="login-status online"></span>
                  <!--change to offline or busy as needed-->
                </div>
                <div class="nav-profile-text d-flex flex-column">
                  <span class="font-weight-bold mb-2"><?= $_SESSION['admin']; ?></span>
                  <span class="text-secondary text-small"><?= $_SESSION['role']; ?></span>
                </div>
                <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
              </a>
            </li>

            <?php if (isset($access['dashboard']) && $access['dashboard'] == 1): ?>
            <li class="nav-item">
              <a class="nav-link" href="index.php">
                <span class="menu-title">Dashboard</span>
                <i class="mdi mdi-home menu-icon"></i>
              </a>
            </li>
            <?php endif; ?>
            
            <?php if (isset($access['product']) && $access['product'] == 1): ?>
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="collapse" href="#product" aria-expanded="false" aria-controls="product">
                <span class="menu-title">Product</span>
                <i class="mdi mdi-format-list-bulleted menu-icon"></i>
              </a>
              <div class="collapse" id="product">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item">
                    <a class="nav-link" href="addProduct.php">Add Product</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="viewProduct.php">View Product</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="create-landing-page.php">Create Landing Page</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="landing-page-list.php">Landing Page List</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="sizes.php">Sizes</a>
                  </li>
                </ul>
              </div>
            </li>
            <?php endif; ?>
            
            <?php if (isset($access['categories']) && $access['categories'] == 1): ?>
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="collapse" href="#categories" aria-expanded="false" aria-controls="categories">
                <span class="menu-title">Product Categories</span>
                <i class="mdi mdi-table-large menu-icon"></i>
              </a>
              <div class="collapse" id="categories">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item">
                    <a class="nav-link" href="insertCategory.php">Insert Category</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="viewCategory.php">View Category</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="deleteCategory.php">Delete Category</a>
                  </li>
                </ul>
              </div>
            </li>
            <?php endif; ?>

            <?php if (isset($access['slider']) && $access['slider'] == 1): ?>
            <li class="nav-item">
              <a class="nav-link" href="slider.php">
                <span class="menu-title">Slider</span>
                <i class="mdi mdi-panorama-variant menu-icon"></i>
              </a>
            </li>
            <?php endif; ?>

            <?php if (isset($access['banner']) && $access['banner'] == 1): ?>
            <li class="nav-item">
              <a class="nav-link" href="banner.php">
                <span class="menu-title">Banner</span>
                <i class="mdi mdi-image-edit menu-icon"></i>
              </a>
            </li>
            <?php endif; ?>

            <?php if (isset($access['discounts']) && $access['discounts'] == 1): ?>
            <li class="nav-item">
              <a class="nav-link" href="add-discounts.php">
                <span class="menu-title">Discounts</span>
                <i class="mdi mdi-sale-outline menu-icon"></i>
              </a>
            </li>
            <?php endif; ?>

            <?php if (isset($access['coupons']) && $access['coupons'] == 1): ?>
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="collapse" href="#coupons" aria-expanded="false" aria-controls="order">
                <span class="menu-title">Coupons</span>
                <i class="mdi mdi-ticket-percent menu-icon"></i>
              </a>
              <div class="collapse" id="coupons">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item">
                    <a class="nav-link" href="add-coupons.php">Add Coupons</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="view-coupons.php">View Coupons</a>
                  </li>
                </ul>
              </div>
            </li>
            <?php endif; ?>

            <?php if (isset($access['customers']) && $access['customers'] == 1): ?>
            <li class="nav-item">
              <a class="nav-link" href="viewCustomers.php">
                <span class="menu-title">View Customers</span>
                <i class="mdi mdi-account menu-icon"></i>
              </a>
            </li>
            <?php endif; ?>

            <li class="nav-item">
              <a class="nav-link" href="fraud-checking.php">
                <span class="menu-title">Fraud Checker</span>
                <i class="mdi mdi-account-question menu-icon"></i>
              </a>
            </li>

            <?php if (isset($access['orders']) && $access['orders'] == 1): ?>
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="collapse" href="#order" aria-expanded="false" aria-controls="order">
                <span class="menu-title">View Orders</span>
                <i class="mdi mdi-format-list-bulleted menu-icon"></i>
              </a>
              <div class="collapse" id="order">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item">
                    <a class="nav-link" href="pendingOrders.php">Pending Orders</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="viewOrders.php">Active Orders</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="create-order.php">Create Order</a>
                  </li>
                </ul>
              </div>
            </li>
            <?php endif; ?>

            <?php if (isset($access['payments']) && $access['payments'] == 1): ?>
            <li class="nav-item">
              <a class="nav-link" href="viewPayments.php">
                <span class="menu-title">View Payments</span>
                <i class="mdi mdi-currency-usd menu-icon"></i>
              </a>
            </li>
            <?php endif; ?>

            <?php if (isset($access['accounts']) && $access['accounts'] == 1): ?>
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="collapse" href="#accounts" aria-expanded="false" aria-controls="order">
                <span class="menu-title">Accounts</span>
                <i class="mdi mdi-finance menu-icon"></i>
              </a>
              <div class="collapse" id="accounts">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item">
                    <a class="nav-link" href="accounts-dashboard.php">Dashboard</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="total-collections.php">Total Collections</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="expense-category.php">Expense Category</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="add-expense.php">Add Expense</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="all-expenses.php">All Expenses</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="statements.php">Statements</a>
                  </li>
                </ul>
              </div>
            </li>
            <?php endif; ?>

            <?php if (isset($access['inventory']) && $access['inventory'] == 1): ?>
            <li class="nav-item">
              <a class="nav-link" href="inventory.php">
                <span class="menu-title">Inventory</span>
                <i class="mdi mdi-storefront menu-icon"></i>
              </a>
            </li>
            <?php endif; ?>
            
            <?php if (isset($access['invoice']) && $access['invoice'] == 1): ?>
            <li class="nav-item">
              <a class="nav-link" href="makeInvoice.php">
                <span class="menu-title">Make Invoice</span>
                <i class="mdi mdi-invoice-list-outline menu-icon"></i>
              </a>
            </li>
            <?php endif; ?>

            <?php if (isset($access['courier']) && $access['courier'] == 1): ?>
            <li class="nav-item">
              <a class="nav-link" href="courier.php">
                <span class="menu-title">Courier</span>
                <i class="mdi mdi-truck menu-icon"></i>
              </a>
            </li>
            <?php endif; ?>
            
            <?php if (isset($access['history']) && $access['history'] == 1): ?>
            <li class="nav-item">
              <a class="nav-link" href="purchaseHistory.php">
                <span class="menu-title">Purchase History</span>
                <i class="mdi mdi-history menu-icon"></i>
              </a>
            </li>
            <?php endif; ?>

            <?php
              if ($_SESSION['role'] == 'Admin') {
                ?>

                <li class="nav-item">
                  <a class="nav-link" data-bs-toggle="collapse" href="#roles" aria-expanded="false" aria-controls="order">
                    <span class="menu-title">Roles & Permission</span>
                    <i class="mdi mdi-account-key menu-icon"></i>
                  </a>
                  <div class="collapse" id="roles">
                    <ul class="nav flex-column sub-menu">
                      <li class="nav-item">
                        <a class="nav-link" href="add-role.php">Add Role</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" href="view-roles.php">View Roles</a>
                      </li>
                    </ul>
                  </div>
                </li>

                <li class="nav-item">
                  <a class="nav-link" data-bs-toggle="collapse" href="#users" aria-expanded="false" aria-controls="order">
                    <span class="menu-title">Users</span>
                    <i class="mdi mdi-account-group menu-icon"></i>
                  </a>
                  <div class="collapse" id="users">
                    <ul class="nav flex-column sub-menu">
                      <li class="nav-item">
                        <a class="nav-link" href="add-user.php">Add user</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" href="view-users.php">View User</a>
                      </li>
                    </ul>
                  </div>
                </li>

                <?php
              }
            ?>

            <?php if (isset($access['settings']) && $access['settings'] == 1): ?>
            <li class="nav-item">
              <a class="nav-link" href="settings.php">
                <span class="menu-title">Settings</span>
                <i class="mdi mdi-cog menu-icon"></i>
              </a>
            </li>
            <?php endif; ?>

            <li class="nav-item">
              <a class="nav-link" href="logout.php">
                <span class="menu-title">Logout</span>
                <i class="mdi mdi-logout menu-icon"></i>
              </a>
            </li>

          </ul>
        </nav>