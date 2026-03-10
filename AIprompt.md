# ADDICTECH — Project TODO / Development Roadmap

# USE FOR AI PROMPT FOR THEIR CONTEXT, USE GROK OR CLAUDE, CHATGPT FOR EXPLAINING

Addictech is an **E-Commerce Web Application** that allows users to browse computer peripherals, add items to cart, save favorites, and place orders.
The system includes **authentication, user profile management, and an admin dashboard for store management**.

---

# PROJECT GOALS

- Provide a responsive and user-friendly online store
- Implement a complete e-commerce workflow (browse → cart → checkout → order history)
- Provide administrators with tools to manage products, orders, and users

---

# CORE FEATURES

### Customer Features

- Product browsing and catalog
- Cart functionality
- Favorites / Wishlist
- Order checkout
- Order history
- User profile management

### Admin Features

- Product management
- Category management
- Order management
- User management
- Dashboard analytics

---

# DEVELOPMENT MILESTONES

## MILESTONE 1 — PROJECT SETUP

Priority: **High**

Tasks

- Setup project structure
- Configure database connection
- Setup routing
- Create base layout templates
- Setup authentication middleware
- Setup session management
- Setup environment configuration

Deliverables

- Working application skeleton
- Database connection
- Basic layout with navigation

---

# MILESTONE 2 — AUTHENTICATION SYSTEM

Priority: **High**

Components

- Login
- Register
- Logout
- Session management

Tasks

- Create AuthController
- Implement login form
- Implement register form
- Hash passwords before storing
- Validate login credentials
- Create session after login
- Protect authenticated routes
- Implement logout functionality

Optional Enhancements

- Password reset via email
- Login session timeout

---

# MILESTONE 3 — PRODUCT CATALOG

Priority: **High**

Tasks

- Create product database model
- Create product controller
- Display product catalog
- Implement pagination
- Implement product search
- Implement category filters
- Implement sorting options

Views

- Catalog page
- Product grid layout

Deliverables

- Fully functional product browsing system

---

# MILESTONE 4 — PRODUCT DETAILS

Priority: **High**

Tasks

- Create product details page
- Display product images
- Display product description
- Display price and stock availability
- Add quantity selector
- Add "Add to Cart" button
- Add "Add to Favorites" button

Views

- Product Details Page

---

# MILESTONE 5 — CART SYSTEM

Priority: **High**

Tasks

- Create cart model
- Create cart controller
- Add item to cart
- Update item quantity
- Remove item from cart
- Display cart contents
- Calculate subtotal and total

Views

- Cart Page

---

# MILESTONE 6 — FAVORITES SYSTEM

Priority: **Medium**

Tasks

- Create favorites model
- Create favorites controller
- Add product to favorites
- Remove product from favorites
- Display favorites list

Views

- Favorites Page

---

# MILESTONE 7 — CHECKOUT & ORDER PROCESSING

Priority: **High**

Tasks

- Create orders database tables
- Implement checkout form
- Validate checkout information
- Convert cart items into order
- Save order and order items
- Clear cart after successful order

Views

- Checkout Page
- Order Confirmation Page

---

# MILESTONE 8 — ORDER HISTORY

Priority: **Medium**

Tasks

- Display user order history
- Show order details
- Display order status

Views

- Orders Page

---

# MILESTONE 9 — USER PROFILE

Priority: **Medium**

Tasks

- Display user profile information
- Allow profile updates
- Change password functionality
- Display user's order history

Views

- Profile Page

---

# MILESTONE 10 — ADMIN DASHBOARD

Priority: **High**

Tasks

- Create admin authentication middleware
- Build admin dashboard layout
- Display store statistics
  - Total users
  - Total products
  - Total orders
  - Revenue summary

- Display recent orders

Views

- Admin Dashboard

---

# MILESTONE 11 — PRODUCT MANAGEMENT (ADMIN)

Priority: **High**

Tasks

- Add product
- Edit product
- Delete product
- Upload product image
- Manage stock quantity
- Activate / deactivate product

Views

- Admin Product List
- Add Product Form
- Edit Product Form

---

# MILESTONE 12 — CATEGORY MANAGEMENT

Priority: **Medium**

Tasks

- Add category
- Edit category
- Delete category
- Assign products to categories

Views

- Category Management Page

---

# MILESTONE 13 — ORDER MANAGEMENT (ADMIN)

Priority: **High**

Tasks

- View all orders
- View order details
- Update order status
- Cancel order (optional)

Views

- Order List Page
- Order Details Page

---

# FRONTEND REQUIREMENTS

- Responsive design (mobile/tablet/desktop)
- Clean UI layout
- Pagination for catalog
- Search functionality
- Filter functionality
- Loading states
- Error handling messages

---

# VIEWS REQUIRED

1. Homepage — featured products
2. About page
3. Catalog — product grid with pagination
4. Product details page
5. Cart page
6. Favorites page
7. Checkout page
8. Orders page (order history)
9. Contact page
10. Profile page

Admin Views

- Admin Dashboard
- Product Management
- Category Management
- Order Management
- User Management

---

# DATABASE STRUCTURE

Tables Required

users
products
categories
carts
cart_items
favorites
orders
order_items

Relationships

users → orders
orders → order_items
products → order_items
users → favorites
products → favorites
users → carts
carts → cart_items

---

# FUTURE IMPROVEMENTS

- Product reviews and ratings
- Payment gateway integration
- Email order confirmations
- Inventory alerts
- Product recommendation system
- Sales analytics dashboard

---

# TEAM WORKFLOW

Development Process

- Use Git for version control
- Create feature branches
- Submit pull requests before merging
- Code review before integration

Branch Naming Example

feature/cart-system
feature/product-catalog
feature/admin-dashboard

---

# STATUS TRACKING

Legend

- [ ] Not Started
- [ ] In Progress
- [ ] Completed

All milestones should be tracked using GitHub Issues or Project Boards.
