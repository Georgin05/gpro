create database warehouse;
use warehouse;

create table users (
    user_id int auto_increment primary key,
    username varchar(50) not null unique,
    password_hash varchar(255) not null,
    role enum(admin,staff) not null,
    created_at datetime not null default current_timestamp
);


create table customers (
    customer_id int auto_increment primary key,
    cus_name varchar(50) not null,
    email varchar(100) not null unique,
    phone varchar(15)
    billing_address TEXT,
    shipping_address TEXT,
    created at TIMESTAMP DEFAULT current_timestamp
);

create table suppliers (
    supplier_id int auto_increment primary key,
    name varchar(100) not null,
    company_name varchar(100) not null unique,
    contact_name varchar(50),
    phone varchar(15),
    address text,
    email varchar(100)
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

create table products (
    product_id int auto_increment primary key,
    name varchar(100) not null,
    description text,
    price decimal(10, 2) not null,
    reorder_level INT DEFAULT 10,
    stock int not null default 0
);  


create table categories (
    category_id int auto_increment primary key,
    name varchar(50) not null unique,
    description text
);


create table orders (
    order_id int auto_increment primary key,
    customer_id int not null,
    order_date datetime not null default current_timestamp,
    total_amount decimal(10, 2) not null,
     status ENUM('Pending', 'Packed', 'Shipped', 'Delivered', 'Cancelled') DEFAULT 'Pending',
    foreign key (customer_id) references customers(customer_id)
);

create table order_items (
    order_item_id int auto_increment primary key,
    order_id int not null,
    product_id int not null,
    category_id int not null,
    quantity int not null default 1,
    price decimal(10, 2) not null,
    foreign key (category_id) references categories(category_id),
    foreign key (order_id) references orders(order_id),
    foreign key (product_id) references products(product_id)
);
create table inventory (
    inventory_id int auto_increment primary key,
    product_id int not null,
    quantity int not null default 0,
    last_updated datetime not null default current_timestamp on update current_timestamp,
    reorder_level INT NOT NULL DEFAULT 10,
    foreign key (product_id) references products(product_id)
);



-- Main return orders table
CREATE TABLE return_orders (
    return_order_id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    customer_id INT NOT NULL,
    return_date DATE DEFAULT CURRENT_DATE,
    status ENUM('Pending', 'Approved', 'Rejected', 'Completed') DEFAULT 'Pending',
    refund_status ENUM('Not Issued', 'Issued', 'Store Credit') DEFAULT 'Not Issued',
    notes TEXT,

    -- Foreign key relationships
    FOREIGN KEY (order_id) REFERENCES orders(order_id),
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id)
);

-- Return order items table
CREATE TABLE return_order_items (
    return_item_id INT PRIMARY KEY AUTO_INCREMENT,
    return_order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL CHECK (quantity > 0),
    reason VARCHAR(255),

    -- Foreign key relationships
    FOREIGN KEY (return_order_id) REFERENCES return_orders(return_order_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
); 

CREATE TABLE customer_payments (
    payment_id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT NOT NULL,
    order_id INT NOT NULL,
    amount_paid DECIMAL(10,2),
    payment_date DATE DEFAULT CURRENT_DATE,
    payment_method ENUM('Card', 'Bank', 'Cash'),
    status ENUM('Paid', 'Partial', 'Failed') DEFAULT 'Paid',
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id),
    FOREIGN KEY (order_id) REFERENCES orders(order_id)
);

CREATE TABLE supplier_payments (
    payment_id INT PRIMARY KEY AUTO_INCREMENT,
    supplier_id INT NOT NULL,
    purchase_order_id INT NOT NULL,
    amount_paid DECIMAL(10,2),
    payment_date DATE DEFAULT CURRENT_DATE,
    payment_method ENUM('Bank', 'Cash', 'Cheque'),
    status ENUM('Paid', 'Partial', 'Pending'),
    FOREIGN KEY (supplier_id) REFERENCES suppliers(supplier_id),
    FOREIGN KEY (purchase_order_id) REFERENCES purchase_orders(purchase_order_id)
);






-- PURCHASE ORDERS TABLE
CREATE TABLE purchase_orders (
    purchase_order_id INT PRIMARY KEY AUTO_INCREMENT,
    supplier_id INT NOT NULL,
    order_date DATE DEFAULT CURRENT_DATE,
    expected_delivery_date DATE,
    status ENUM('Pending', 'Received', 'Cancelled') DEFAULT 'Pending',
    total_amount DECIMAL(10,2),
    FOREIGN KEY (supplier_id) REFERENCES suppliers(supplier_id)
);

-- PURCHASE ORDER ITEMS TABLE
CREATE TABLE purchase_order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    purchase_order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2),
    FOREIGN KEY (purchase_order_id) REFERENCES purchase_orders(purchase_order_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);


