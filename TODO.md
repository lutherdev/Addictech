# ADDICTECH — TODO.md

Addictech is an **E-Commerce Web Application** that features product ordering, cart functionality, favourites (wishlist), user profile management, authentication, and an admin dashboard.

FRONTEND MUST HAVE:

- Pagination for catalog
- Responsive design (mobile / tablet / desktop)

---

# ACCOUNTS FOR TESTING

- Admin = admin - admin123
- Customer = user - user123
- god = god - god /// SUPER ADMIN

---

# FRONTEND =================================================================

## NAVBAR

- highlight active link based on current URL
- show different navbar options depending on login state
- show cart icon with item count
- show profile dropdown when logged in

---

# CUSTOMER FEATURES

## HOMEPAGE

- display featured products
- display categories
- show promotional banner
- quick "Add to Cart" button

## CATALOG

- product grid layout
- pagination (required)
- category filters
- price filter
- search products
- sort products (price, popularity, newest)

## PRODUCT DETAILS

- product image gallery
- product description
- product specifications
- stock availability
- add to cart button
- add to favourites button
- quantity selector

## CART

- view cart items
- update product quantity
- remove item from cart
- show subtotal and total
- proceed to checkout button

## FAVORITES (WISHLIST)

- add product to favourites
- remove product from favourites
- display saved items

## CHECKOUT

- shipping information form
- order summary
- confirm order
- save order to database

## ORDERS

- display order history
- view order details
- show order status

## PROFILE

- view user information
- update profile details
- change password
- view user orders

## CONTACT

- contact form
- email inquiry

---

# ADMIN DASHBOARD ===========================================================

## DASHBOARD

- overview statistics
  - total users
  - total orders
  - total products
  - total revenue

- recent orders table

## PRODUCT MANAGEMENT

- add product
- edit product
- delete product
- upload product image
- activate/deactivate product
- manage stock quantity

## CATEGORY MANAGEMENT

- add category
- update category
- delete category

## ORDER MANAGEMENT

- view orders
- view order details
- update order status

## USER MANAGEMENT

- view users
- activate/deactivate users
- reset user password (optional)

---

# VIEWS NEEDED

1. homepage — featured products
2. about
3. catalog — product grid with pagination
4. product details page
5. cart page
6. favorites page
7. checkout page
8. orders page (order history)
9. contact
10. profile

---

# BACKEND ===================================================================

## AUTH CONTROLLER

- login
- register
- logout

## USER CONTROLLER

- view profile
- update profile
- change password

## PRODUCT CONTROLLER

- create product
- read products
- update product
- delete product

## CATEGORY CONTROLLER

- manage categories

## CART CONTROLLER

- add item to cart
- update cart quantity
- remove item from cart

## FAVORITES CONTROLLER

- add product to favorites
- remove product from favorites

## ORDER CONTROLLER

- create order
- view orders
- view order details
- update order status

## DASHBOARD CONTROLLER

- admin statistics
- recent orders

---

# AUTHENTICATION

## LOGIN

- POST form with email/username and password
- fetch user from database
- verify password using password_verify()
- create session if successful
- redirect to dashboard or homepage

## REGISTER

- form validation
- hash password
- insert user into database

## LOGOUT

- destroy session
- redirect to homepage

---

# SESSIONS

- store user id
- store user role
- restrict admin routes
- auto logout after inactivity (optional)

---

# FORM VALIDATIONS

- register form
- login form
- product add/edit form
- checkout form
- profile update form

---

# FILE UPLOAD

- product image upload
- validate file type (jpg, png, webp)
- validate file size
- store image path in database
- optional: generate thumbnails

---

# DATABASE =================================================================

Tables required:

- users
- products
- categories
- carts
- cart_items
- favorites
- orders
- order_items

Relationships:

users → orders
orders → order_items
products → order_items
users → favorites
products → favorites
users → carts
carts → cart_items
