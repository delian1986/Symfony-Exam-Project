Symfony Project for PHP Web Course 2018 
=========
# Shopping cart site build for SoftUni Symfony MVC Course Sepember 2018

### Functionalities

•	User registration / login and user profiles.

•	User roles (user, administrator, editor)

•	First ever registered user becomes Administrator and shop owner

•	Shop owner owns all products on the site. Receive payments and send emails to customers.

•	Shop owner can be changed from Admin panel. All products will be transfered to the new owner.

•	Only current shop owner have access to change shop owner functionality.

•	Initial cash for users can be setup from admin panel

•	Product categories and	listing products in categories

•	Add to cart functionality

•	Add to wish list functionality

•	When product is edited an email will be send to all users who have the product in their wish list.

•	Promotions for certain time interval

•	Promotions on certain products (% discount)

•	If two or more promotions collide on a date period for certain product – the biggest one applies only

•	Quantity visibility

• View cart and	checkout the cart

•	Editors can add/delete products and product categories

•	Editors can move products between categories

•	Managing the cart

•	Users can make comments on products (review) only if they bought it first

•	Administrators: ban users

## Instalation

#### Prerequisites
  - PHP >= 7.1
  - MySQL
  - Composer

#### Steps
```sh
git clone https://github.com/delian1986/Symfony-Exam-Project.git
composer install  
php bin/console doctrine:database:create
php bin/console doctrine:schema:create
php bin/console doctrine:fixtures:load
php bin/console server:run 
Open http://localhost:8000 
```

#### Demo
https://shop.delyan.eu

Admin:

• demo@demo.com

• demo

