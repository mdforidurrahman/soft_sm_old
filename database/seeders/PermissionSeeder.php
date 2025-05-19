<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            ['id' => 1, 'name' => 'dashboard_view', 'display_name' => 'View Dashboard', 'description' => 'Can view dashboard', 'category' => 'Dashboard'],

            // Store Permissions
            ['id' => 2, 'name' => 'store_view', 'display_name' => 'View Stores', 'description' => 'Can view store list', 'category' => 'Stores'],
            ['id' => 3, 'name' => 'store_create', 'display_name' => 'Create Store', 'description' => 'Can create new store', 'category' => 'Stores'],
            ['id' => 4, 'name' => 'store_edit', 'display_name' => 'Edit Store', 'description' => 'Can edit store details', 'category' => 'Stores'],
            ['id' => 5, 'name' => 'store_delete', 'display_name' => 'Delete Store', 'description' => 'Can delete store', 'category' => 'Stores'],

            // Stock Transfer Permissions
            ['id' => 6, 'name' => 'stock_transfer_view', 'display_name' => 'View Stock Transfers', 'description' => 'Can view stock transfer list', 'category' => 'Stock Transfer'],
            ['id' => 7, 'name' => 'stock_transfer_create', 'display_name' => 'Create Stock Transfer', 'description' => 'Can create stock transfer', 'category' => 'Stock Transfer'],
            ['id' => 8, 'name' => 'stock_transfer_edit', 'display_name' => 'Edit Stock Transfer', 'description' => 'Can edit stock transfer', 'category' => 'Stock Transfer'],
            ['id' => 9, 'name' => 'stock_transfer_delete', 'display_name' => 'Delete Stock Transfer', 'description' => 'Can delete stock transfer', 'category' => 'Stock Transfer'],

            // POS Permissions
            ['id' => 10, 'name' => 'pos_view', 'display_name' => 'View POS', 'description' => 'Can access Point of Sale', 'category' => 'Point of Sale'],
            ['id' => 11, 'name' => 'pos_create', 'display_name' => 'Create POS Sale', 'description' => 'Can create POS sale', 'category' => 'Point of Sale'],
            ['id' => 12, 'name' => 'pos_report_view', 'display_name' => 'View POS Reports', 'description' => 'Can view POS reports', 'category' => 'Point of Sale'],

            // Contacts Permissions
            ['id' => 13, 'name' => 'contact_view', 'display_name' => 'View Contacts', 'description' => 'Can view contact list', 'category' => 'Contacts'],
            ['id' => 14, 'name' => 'contact_create', 'display_name' => 'Create Contact', 'description' => 'Can create new contact', 'category' => 'Contacts'],
            ['id' => 15, 'name' => 'contact_edit', 'display_name' => 'Edit Contact', 'description' => 'Can edit contact details', 'category' => 'Contacts'],
            ['id' => 16, 'name' => 'contact_delete', 'display_name' => 'Delete Contact', 'description' => 'Can delete contact', 'category' => 'Contacts'],

            // Customer Permissions
            ['id' => 17, 'name' => 'customer_view', 'display_name' => 'View Customers', 'description' => 'Can view customer list', 'category' => 'Customers'],
            ['id' => 18, 'name' => 'customer_create', 'display_name' => 'Create Customer', 'description' => 'Can create new customer', 'category' => 'Customers'],
            ['id' => 19, 'name' => 'customer_edit', 'display_name' => 'Edit Customer', 'description' => 'Can edit customer details', 'category' => 'Customers'],
            ['id' => 20, 'name' => 'customer_delete', 'display_name' => 'Delete Customer', 'description' => 'Can delete customer', 'category' => 'Customers'],

            // Supplier Permissions
            ['id' => 21, 'name' => 'supplier_view', 'display_name' => 'View Suppliers', 'description' => 'Can view supplier list', 'category' => 'Suppliers'],
            ['id' => 22, 'name' => 'supplier_create', 'display_name' => 'Create Supplier', 'description' => 'Can create new supplier', 'category' => 'Suppliers'],
            ['id' => 23, 'name' => 'supplier_edit', 'display_name' => 'Edit Supplier', 'description' => 'Can edit supplier details', 'category' => 'Suppliers'],
            ['id' => 24, 'name' => 'supplier_delete', 'display_name' => 'Delete Supplier', 'description' => 'Can delete supplier', 'category' => 'Suppliers'],

            // Sell Permissions
            ['id' => 25, 'name' => 'sell_view', 'display_name' => 'View Sells', 'description' => 'Can view sell list', 'category' => 'Sells'],
            ['id' => 26, 'name' => 'sell_create', 'display_name' => 'Create Sell', 'description' => 'Can create new sell', 'category' => 'Sells'],
            ['id' => 27, 'name' => 'sell_edit', 'display_name' => 'Edit Sell', 'description' => 'Can edit sell details', 'category' => 'Sells'],
            ['id' => 28, 'name' => 'sell_delete', 'display_name' => 'Delete Sell', 'description' => 'Can delete sell', 'category' => 'Sells'],

            // Purchase Permissions
            ['id' => 29, 'name' => 'purchase_view', 'display_name' => 'View Purchases', 'description' => 'Can view purchase list', 'category' => 'Purchases'],
            ['id' => 30, 'name' => 'purchase_create', 'display_name' => 'Create Purchase', 'description' => 'Can create new purchase', 'category' => 'Purchases'],
            ['id' => 31, 'name' => 'purchase_edit', 'display_name' => 'Edit Purchase', 'description' => 'Can edit purchase details', 'category' => 'Purchases'],
            ['id' => 32, 'name' => 'purchase_delete', 'display_name' => 'Delete Purchase', 'description' => 'Can delete purchase', 'category' => 'Purchases'],

            // Product Category Permissions
            ['id' => 33, 'name' => 'product_category_view', 'display_name' => 'View Product Categories', 'description' => 'Can view product category list', 'category' => 'Product Category'],
            ['id' => 34, 'name' => 'product_category_create', 'display_name' => 'Create Product Category', 'description' => 'Can create new product category', 'category' => 'Product Category'],
            ['id' => 35, 'name' => 'product_category_edit', 'display_name' => 'Edit Product Category', 'description' => 'Can edit product category details', 'category' => 'Product Category'],
            ['id' => 36, 'name' => 'product_category_delete', 'display_name' => 'Delete Product Category', 'description' => 'Can delete product category', 'category' => 'Product Category'],

            // Product Permissions
            ['id' => 37, 'name' => 'product_view', 'display_name' => 'View Products', 'description' => 'Can view product list', 'category' => 'Products'],
            ['id' => 38, 'name' => 'product_create', 'display_name' => 'Create Product', 'description' => 'Can create new product', 'category' => 'Products'],
            ['id' => 39, 'name' => 'product_edit', 'display_name' => 'Edit Product', 'description' => 'Can edit product details', 'category' => 'Products'],
            ['id' => 40, 'name' => 'product_delete', 'display_name' => 'Delete Product', 'description' => 'Can delete product', 'category' => 'Products'],

            // Continue adding IDs in a similar manner for the remaining permissions...

            // Expense Permissions
            ['id' => 41, 'name' => 'expense_view', 'display_name' => 'View Expenses', 'description' => 'Can view expense list', 'category' => 'Expenses'],
            ['id' => 42, 'name' => 'expense_create', 'display_name' => 'Create Expense', 'description' => 'Can create new expense', 'category' => 'Expenses'],
            ['id' => 43, 'name' => 'expense_edit', 'display_name' => 'Edit Expense', 'description' => 'Can edit expense details', 'category' => 'Expenses'],
            ['id' => 44, 'name' => 'expense_delete', 'display_name' => 'Delete Expense', 'description' => 'Can delete expense', 'category' => 'Expenses'],

            // Expense Category Permissions
            ['id' => 45, 'name' => 'expense_category_view', 'display_name' => 'View Expense Categories', 'description' => 'Can view expense category list', 'category' => 'Expense Categories'],
            ['id' => 46,  'name' => 'expense_category_create', 'display_name' => 'Create Expense Category', 'description' => 'Can create new expense category', 'category' => 'Expense Categories'],
            ['id' => 47, 'name' => 'expense_category_edit', 'display_name' => 'Edit Expense Category', 'description' => 'Can edit expense category details', 'category' => 'Expense Categories'],
            ['id' => 48, 'name' => 'expense_category_delete', 'display_name' => 'Delete Expense Category', 'description' => 'Can delete expense category', 'category' => 'Expense Categories'],

            // Report Permissions
            ['id' => 49, 'name' => 'expense_report_view', 'display_name' => 'View Expense Reports', 'description' => 'Can view expense reports', 'category' => 'Reports'],
            ['id' => 50, 'name' => 'profit_loss_report_view', 'display_name' => 'View Profit/Loss Reports', 'description' => 'Can view profit and loss reports', 'category' => 'Reports'],

            // Settings Permissions
            ['id' => 51, 'name' => 'role_assignment_manage', 'display_name' => 'Manage Role Assignments', 'description' => 'Can manage user role assignments', 'category' => 'Settings'],

            // Business Location Permissions
            ['id' => 52, 'name' => 'business_location_view', 'display_name' => 'View Business Locations', 'description' => 'Can view business location list', 'category' => 'Business Location'],
            ['id' => 53, 'name' => 'business_location_create', 'display_name' => 'Create Business Location', 'description' => 'Can create new business location', 'category' => 'Business Location'],
            ['id' => 54, 'name' => 'business_location_edit', 'display_name' => 'Edit Business Location', 'description' => 'Can edit business location details', 'category' => 'Business Location'],
            ['id' => 55, 'name' => 'business_location_delete', 'display_name' => 'Delete Business Location', 'description' => 'Can delete business location', 'category' => 'Business Location'],

            // User Permissions
            ['id' => 56, 'name' => 'user_view', 'display_name' => 'View Users', 'description' => 'Can view user list', 'category' => 'Users'],
            ['id' => 57, 'name' => 'user_create', 'display_name' => 'Create User', 'description' => 'Can create new user', 'category' => 'Users'],
            ['id' => 58, 'name' => 'user_edit', 'display_name' => 'Edit User', 'description' => 'Can edit user details', 'category' => 'Users'],
            ['id' => 59, 'name' => 'user_delete', 'display_name' => 'Delete User', 'description' => 'Can delete user', 'category' => 'Users'],
        ];

        DB::table('permissions')->insert($permissions);
    }
}
