# CMS Dashboard Setup (PHP + MySQL)

Your website now includes an admin dashboard so you can change text and images without editing HTML.

## 1. Upload files

Upload the whole project to your hosting (public_html or domain root), including:

- `admin/`
- `api/`
- `config/`
- `includes/`
- `sql/`
- `uploads/`
- `install.php`
- `cms.js`

## 2. Create MySQL database

In your hosting panel (**Manage MySQL Databases** / cPanel):

1. Create a database (you already have one on StackCP)
2. **Set a password** for that database and click **Save** (required)
3. Note the **Server** hostname (on StackCP this is like `sdb-68.hosting.stackcp.net`, not `localhost`)

## 3. Edit database config

Open `config/database.php` and set your credentials from the panel:

```php
return [
    'host'     => 'sdb-68.hosting.stackcp.net', // Server from MySQL panel
    'name'     => 'naseeb_cms-353034398fd6',    // Database/Username
    'user'     => 'naseeb_cms-353034398fd6',    // same as name on StackCP
    'pass'     => 'your_real_password_here',    // password you saved in the panel
    'charset'  => 'utf8mb4',
];
```

Do **not** leave `pass` as `CHANGE_THIS_PASSWORD`.

## 4. Run installer

Visit:

`https://yourdomain.com/install.php`

- Choose an admin username and password
- Click **Install & Seed Content**
- This creates tables and loads your current website text/images into the database

## 5. Delete installer

After success, **delete `install.php`** from the server.

## 6. Log in to the dashboard

Visit:

`https://yourdomain.com/admin/login.php`

Sections you can edit:

| Menu | What you change |
|------|-----------------|
| Home Page | Hero text, stats, features, CTA, university ticker |
| Destinations | Country names, descriptions, tuition, intakes, images |
| Testimonials | Student quotes, names, photos |
| Contact & Footer | Phone, email, address, social links, page banners |
| Site Settings | Logo, header button, WhatsApp number/message, admin password |

## 7. Folder permissions

Make sure `uploads/` is writable (usually `755` or `775`) so image uploads work.

---

## How the live site updates

1. You save content in `/admin`
2. Public pages load `cms.js`
3. `cms.js` fetches `/api/content.php`
4. Text and images on the website refresh

If MySQL is not configured yet, the original static HTML still shows (safe fallback).

## Security tips

- Use a strong admin password
- Delete `install.php` after setup
- Do not share `config/database.php`
- Change the admin password from **Site Settings** after first login
