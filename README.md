# 🌱 SLSeeds.lk - Agricultural E-Commerce Platform

Welcome to **SLSeeds.lk**! This is a modern, fully-featured online e-commerce platform built specifically for selling agricultural seeds (vegetable, fruit, and flower seeds) in Sri Lanka. 

It is built using the **Laravel** framework, **Tailwind CSS**, and **Alpine.js**, providing a fast, secure, and mobile-friendly shopping experience.

## ✨ Key Features

### 🛒 For Customers:
* **Multi-Language Support**: Browse the website in English, Sinhala, or Tamil.
* **Smart Search & Advanced Filters**: Easily find products using the search bar, category filters, and price range filters.
* **Product Image Gallery**: View multiple high-quality images for a product with a hover-to-zoom (magnifier) effect.
* **Reviews & Ratings**: Customers can leave 1-5 star ratings and comments on products they purchased.
* **Shopping Cart & Wishlist**: Save favorite items for later or add them to the cart for immediate purchase.
* **Secure Checkout**: Integrated with PayHere for secure online payments.

### 🛠️ For Administrators (Admin Panel):
* **Dashboard Overview**: View sales, total customers, and recent activities at a glance.
* **Product & Category Management**: Add, edit, or delete products and categories easily. Upload multiple gallery images per product.
* **Stock Management**: Keep track of inventory with low-stock alerts.
* **Order Management**: View and process customer orders in real-time.
* **Review Control**: Admins can monitor customer reviews, delete inappropriate ones, and reply to customer feedback.

## 💻 Technology Stack

* **Backend**: Laravel (PHP)
* **Frontend**: Blade Templates, Tailwind CSS, Alpine.js
* **Database**: MySQL / SQLite
* **Payment Gateway**: PayHere (Sandbox/Live)

## 🚀 How to Run Locally

Follow these steps to set up the project on your local machine:

**1. Clone the repository**
```bash
git clone [https://github.com/sandunkumara163/slseeds.lk.git](https://github.com/sandunkumara163/slseeds.lk.git)
cd slseeds.lk
```

**2. Install Composer dependencies**
```bash
composer install
```

**3. Install NPM dependencies**
```bash
npm install
npm run build
```

**4. Environment Setup**
* Copy the `.env.example` file and rename it to `.env`.
* Open the `.env` file and update your Database credentials (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).

**5. Generate Application Key**
```bash
php artisan key:generate
```

**6. Run Migrations and Seeders**
```bash
php artisan migrate --seed
```

**7. Create Storage Link**
```bash
php artisan storage:link
```

**8. Start the Local Server**
```bash
php artisan serve
```

Now open your browser and go to `http://127.0.0.1:8000`.
