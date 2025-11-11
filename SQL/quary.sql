-- table admin
CREATE TABLE admins (
    id int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- table customer
CREATE TABLE `customer` (
  `id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `mobile` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  image VARCHAR(500) NOT NULL
) ;

-- table items
CREATE TABLE items (
    item_id INT(11) PRIMARY KEY AUTO_INCREMENT,
    item_name VARCHAR(255) NOT NULL,
    price VARCHAR(1000) NOT NULL,
    stock INT(11) NOT NULL,
    item_detail VARCHAR(500) NOT NULL,
    image VARCHAR(500) NOT NULL
);

--table order table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    user_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    address TEXT NOT NULL,
    phone VARCHAR(50) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    order_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    payment_status enum('Not Paid','Paid') DEFAULT 'Not Paid',
    status enum('Accept','In process') DEFAULT 'In Process',
    FOREIGN KEY (user_id) REFERENCES customer(id)
);


CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    item_id INT NOT NULL,           
    item_name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES items(item_id) ON DELETE CASCADE 
);
-- admin Email:admin@gmail.com , password :admin
INSERT INTO `admins`(`id`, `name`, `email`, `password`) VALUES (1,'Admin','admin@gmail.com','21232f297a57a5a743894a0e4a801fc3')
