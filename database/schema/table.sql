CREATE TABLE roles (
  id INT(11) NOT NULL AUTO_INCREMENT,
  role_name VARCHAR(255) NOT NULL,
  created_at DATE NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
);

CREATE TABLE page_access (
  id INT(11) NOT NULL AUTO_INCREMENT,
  role_id INT(11) NOT NULL,
  dashboard INT(11) NOT NULL DEFAULT 1,
  product INT(11) NOT NULL DEFAULT 0,
  categories INT(11) NOT NULL DEFAULT 0,
  slider INT(11) NOT NULL DEFAULT 0,
  banner INT(11) NOT NULL DEFAULT 0,
  discounts INT(11) NOT NULL DEFAULT 0,
  coupons INT(11) NOT NULL DEFAULT 0,
  customers INT(11) NOT NULL DEFAULT 0,
  orders INT(11) NOT NULL DEFAULT 0,
  payments INT(11) NOT NULL DEFAULT 0,
  accounts INT(11) NOT NULL DEFAULT 0,
  inventory INT(11) DEFAULT 0,
  invoice INT(11) DEFAULT 0,
  courier INT(11) DEFAULT 0,
  history INT(11) DEFAULT 0,
  settings INT(11) DEFAULT 1,
  PRIMARY KEY (id),
  FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE admin_info (
  admin_id INT(11) NOT NULL AUTO_INCREMENT,
  admin_username VARCHAR(50) NOT NULL,
  admin_password VARCHAR(255) NOT NULL,
  admin_picture VARCHAR(255) DEFAULT NULL,
  role_id INT(11) NOT NULL,
  created_at DATE NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (admin_id),
  FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE user_info (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    user_fName VARCHAR(50) NOT NULL,
    user_lName VARCHAR(50) NOT NULL,
    user_phone VARCHAR(20) UNIQUE NOT NULL,
    user_email VARCHAR(100) UNIQUE NOT NULL,
    user_gender VARCHAR(20) NOT NULL,
    user_password VARCHAR(255) NOT NULL
);

CREATE TABLE main_category (
    main_ctg_id INT PRIMARY KEY AUTO_INCREMENT,
    main_ctg_name VARCHAR(100) UNIQUE NOT NULL,
    main_ctg_des TEXT,
    main_ctg_img VARCHAR(255),
    main_ctg_slug VARCHAR(255) NOT NULL
);

CREATE TABLE sub_category (
    sub_ctg_id INT PRIMARY KEY AUTO_INCREMENT,
    sub_ctg_name VARCHAR(100) UNIQUE NOT NULL,
    main_ctg_name VARCHAR(100) NOT NULL,
    sub_ctg_slug VARCHAR(255) NOT NULL
);

CREATE TABLE slider (
    slider_id INT PRIMARY KEY AUTO_INCREMENT,
    slider_img VARCHAR(255)
);

CREATE TABLE product_info (
    product_id INT PRIMARY KEY AUTO_INCREMENT,
    product_title VARCHAR(255) NOT NULL,
    product_purchase_price INT NOT NULL,
    product_regular_price INT NOT NULL,
    product_price INT NOT NULL,
    main_ctg_id INT NOT NULL,
    sub_ctg_id INT NOT NULL,
    available_stock INT NOT NULL,
    size_option VARCHAR(50),
    product_keyword VARCHAR(255),
    product_code VARCHAR(255),
    product_short_description TEXT,
    product_description TEXT,
    product_img1 VARCHAR(255),
    product_img2 VARCHAR(255),
    product_img3 VARCHAR(255),
    product_img4 VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    product_type TEXT NOT NULL,
    product_slug VARCHAR(255) NOT NULL,
    FOREIGN KEY (main_ctg_id) REFERENCES main_category(main_ctg_id) ON DELETE CASCADE,
    FOREIGN KEY (sub_ctg_id) REFERENCES sub_category(sub_ctg_id) ON DELETE CASCADE
);

CREATE TABLE order_info (
    order_no INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    user_full_name varchar(255) NOT NULL,
    user_phone VARCHAR(20) NOT NULL,
    user_email VARCHAR(100) NOT NULL,
    user_address TEXT NOT NULL,
    city_address VARCHAR(50) NOT NULL,
    invoice_no VARCHAR(50) NOT NULL,
    product_id INT NOT NULL,
    product_title VARCHAR(255) NOT NULL,
    product_quantity INT NOT NULL,
    product_size VARCHAR(50) DEFAULT 'Default',
    total_price INT NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    order_note TEXT NOT NULL,
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    order_status VARCHAR(50) DEFAULT 'Pending',
    order_visibility VARCHAR(50) DEFAULT 'Show',
    FOREIGN KEY (product_id) REFERENCES product_info(product_id) ON DELETE CASCADE
);

CREATE TABLE payment_info (
    serial_no INT PRIMARY KEY AUTO_INCREMENT,
    invoice_no VARCHAR(50) NOT NULL,
    order_no INT NOT NULL UNIQUE,
    order_status VARCHAR(50) DEFAULT 'Pending',
    order_visibility VARCHAR(50) DEFAULT 'Show',
    payment_method VARCHAR(50) NOT NULL,
    acc_number VARCHAR(50),
    transaction_id VARCHAR(50),
    payment_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    payment_status VARCHAR(50) DEFAULT 'Unpaid',
    FOREIGN KEY (order_no) REFERENCES order_info(order_no) ON DELETE CASCADE
);

CREATE TABLE review_table (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    user_name VARCHAR(255) NOT NULL,
    user_rating INT NOT NULL CHECK (user_rating BETWEEN 1 AND 5),
    user_review TEXT NOT NULL,
    datetime INT NOT NULL,
    FOREIGN KEY (product_id) REFERENCES product_info(product_id) ON DELETE CASCADE
);

CREATE TABLE coupon (
    id INT AUTO_INCREMENT PRIMARY KEY,
    coupon_name VARCHAR(255) NOT NULL,
    coupon_code VARCHAR(100) NOT NULL UNIQUE,
    coupon_discount VARCHAR(50) NOT NULL,
    free_shipping TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE discount (
    id INT AUTO_INCREMENT PRIMARY KEY,
    purchase_amount VARCHAR(50) NOT NULL,
    discount_amount VARCHAR(50) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE order_discount_list (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_no VARCHAR(50) NOT NULL,
    total_order_amount VARCHAR(50) NOT NULL,
    total_discount_amount VARCHAR(50) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE product_size_list (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    size VARCHAR(50) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES product_info(product_id) ON DELETE CASCADE
);

CREATE TABLE themes (
  id int(11) NOT NULL,
  text_color varchar(255) DEFAULT NULL,
  button_color varchar(255) DEFAULT NULL,
  button_text_color varchar(255) DEFAULT NULL,
  button_hover_color varchar(255) DEFAULT NULL,
  navbar_color varchar(255) DEFAULT NULL,
  navbar_text_color varchar(255) DEFAULT NULL,
  indicator_color varchar(255) DEFAULT NULL,
  search_btn_color varchar(255) DEFAULT NULL,
  search_btn_text_color varchar(255) DEFAULT NULL,
  search_btn_hover_color varchar(255) DEFAULT NULL,
  subscribe_btn_color varchar(255) DEFAULT NULL
);

CREATE TABLE footer_info (
  id int(11) NOT NULL,
  about_us text NOT NULL,
  contact_us text NOT NULL,
  faq text NOT NULL,
  terms_of_use text NOT NULL,
  privacy_policy text NOT NULL,
  shipping_delivery text NOT NULL
);

CREATE TABLE website_info (
  id int(11) NOT NULL,
  name varchar(50) NOT NULL,
  logo varchar(255) NOT NULL,
  logo_size varchar(50) NOT NULL,
  fav varchar(255) NOT NULL,
  address text NOT NULL,
  inside_location text NOT NULL,
  inside_delivery_charge int(11) NOT NULL,
  outside_delivery_charge int(11) NOT NULL,
  phone varchar(15) NOT NULL,
  wp_api_num varchar(15) NOT NULL,
  acc_num varchar(15) NOT NULL,
  email varchar(100) NOT NULL,
  fb_link varchar(255) NOT NULL,
  insta_link varchar(255) NOT NULL,
  twitter_link varchar(255) NOT NULL,
  yt_link varchar(255) NOT NULL,
  location varchar(255) NOT NULL,
  vdo_location varchar(255) NOT NULL,
  banner_one varchar(255) NOT NULL,
  banner_two varchar(255) NOT NULL,
  shop_banner varchar(255) NOT NULL,
  about_banner varchar(255) NOT NULL,
  contact_banner varchar(255) NOT NULL,
  faq_banner varchar(255) NOT NULL,
  term_banner varchar(255) NOT NULL,
  privacy_banner varchar(255) NOT NULL,
  shipping_banner varchar(255) NOT NULL,
  top_banner_ad_content TEXT NOT NULL
);

------ Accounts -------------
CREATE TABLE expense_category (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(255) NOT NULL UNIQUE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE expense_info (
    expense_id INT AUTO_INCREMENT PRIMARY KEY,
    expense_title VARCHAR(255) NOT NULL,
    expense_category VARCHAR(100) NOT NULL,
    expense_amount DECIMAL(10, 2) NOT NULL,
    expense_description TEXT,
    expense_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);
----------------------------

CREATE TABLE parcel_info (
    parcel_id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_no VARCHAR(50) NOT NULL,
    tracking_code VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
)

CREATE TABLE steadfast_info (
  id INT AUTO_INCREMENT PRIMARY KEY,
  api_url text NOT NULL,
  api_key text NOT NULL,
  secret_key text NOT NULL
);


-- Landing Page Table
CREATE TABLE landing_pages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    product_slug VARCHAR(255) NOT NULL,
    home_title VARCHAR(255) NOT NULL,
    home_description TEXT NOT NULL,
    home_img VARCHAR(255) NOT NULL,
    feature_img VARCHAR(255) NOT NULL,
    FOREIGN KEY (product_id) REFERENCES product_info(product_id) ON DELETE CASCADE
);

CREATE TABLE features (
  feature_id INT PRIMARY KEY AUTO_INCREMENT,
  product_id INT NOT NULL,
  feature_title varchar(255) NOT NULL,
  feature_description text NOT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  FOREIGN KEY (product_id) REFERENCES product_info(product_id) ON DELETE CASCADE
);

CREATE TABLE reviews (
  review_id INT PRIMARY KEY AUTO_INCREMENT,
  product_id INT NOT NULL,
  review_image varchar(255) NOT NULL,
  FOREIGN KEY (product_id) REFERENCES product_info(product_id) ON DELETE CASCADE
);

CREATE TABLE gallery (
  image_id INT PRIMARY KEY AUTO_INCREMENT,
  product_id INT NOT NULL,
  gallery_image varchar(255) NOT NULL,
  FOREIGN KEY (product_id) REFERENCES product_info(product_id) ON DELETE CASCADE
);

-- END