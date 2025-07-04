# 🗂️ Laravel Task Manager (Vue + Vite)

This is a simple Laravel + Vue 3 project using Vite for frontend bundling.

---

## 🚀 How to Run This Project

### 📦 Requirements

-   Docker & Docker Compose
-   Node.js and npm (installed on your local machine, outside Docker)

---

### 🐳 Step 1: Start Laravel Backend

Run Docker:

```bash
docker compose up -d
```

# 🗂️ Laravel Task Manager (Vue + Vite)

This is a simple Laravel + Vue 3 project using Vite for frontend bundling.

---

## 🚀 How to Run This Project

### 📦 Requirements

-   Docker & Docker Compose
-   Node.js and npm (installed on your local machine, outside Docker)

---

### 🐳 Step 1: Start Laravel Backend

Run Docker:

```bash
docker compose up -d

Then enter the container and run Laravel server:

docker exec -it laravel-app bash
php artisan serve --host=0.0.0.0
```

Start Vue + Vite Frontend
On your local machine (not inside Docker):

npm install # Run this once
npm run dev # Vite will start with --host enabled
