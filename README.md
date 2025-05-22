# Kedai Runcit Malaysia

Sistem ecommerce guna vanilla PHP + SQLite3 database. UI dia pakai **Tailwind CSS**.

## Apa yg ada?

- Boleh browse & search barang
- Ada shopping cart
- Boleh track order
- Payment Malaysia punya (dummy je la)
- Stock counting
- Design responsive guna Tailwind
- Semua ikut style Malaysia (RM, no phone MY, address MY)

## Apa yg kena ada?

- PHP 8.0 ke atas
- SQLite3 extension kena on
- Browser latest sikit la
- XAMPP (utk develop kat local)
- Internet (sbb nak pakai CDN Tailwind CSS)

## Untuk Member SDI 🎓

### Setup Git (Kalau belum install)

1. Download Git kat [git-scm.com](https://git-scm.com/downloads)
2. Install je mcm biasa, next-next semua ok (default setting dah ok dah)
3. Lepas install, buka Git Bash, taip command ni untuk setup:
```bash
git config --global user.name "Nama Kau"
git config --global user.email "email.kau@student.com"
```

### Cara Setup Project

1. Download XAMPP dengan PHP 8.0 or later: [apachefriends.org](https://www.apachefriends.org/download.html)
2. Install XAMPP (next-next je, senang)
3. Start Apache kat XAMPP Control Panel
4. Bukak Git Bash, pergi ke folder htdocs:
```bash
cd C:\xampp\htdocs
```
5. Clone repo ni:
```bash
git clone https://github.com/AnthonyMuncherz/grocery_store.git grocery_store
```
6. Bukak browser, pergi ke `http://localhost/grocery_store`

### Command Git Yang Korang Kena Tau

```bash
# Check status project
git status

# Download update terbaru
git pull origin main

# Kalau nak push code baru:
git add .                    # Add semua file yang dah edit
git commit -m "Apa yang ko edit"  # Simpan changes
git push origin main        # Upload ke GitHub

# Kalau nak tukar branch:
git checkout nama_branch

# Kalau nak buat branch baru:
git checkout -b nama_branch_baru

# Kalau ada conflict masa pull:
git stash                   # Simpan changes temporary
git pull origin main        # Pull latest
git stash pop              # Keluarkan balik changes
```

### Troubleshooting Common Problem

1. **Error 404/Page Not Found**
   - Check URL betul ke tak
   - Pastikan Apache dah start kat XAMPP
   - Check folder name sama dgn URL ke tak
   - Check port 80 free ke tak

2. **Cannot Connect to Database**
   - Check `database/grocery_store.db` ada ke tak
   - Pastikan SQLite3 extension enabled kat php.ini
   - Check permission folder database

3. **Git Error**
   - Kalau ada error "Permission denied":
     ```bash
     git config --global credential.helper wincred
     ```
   - Kalau conflict, jgn panic. Chat group discuss dulu

4. **PHP Error**
   - Bukak XAMPP Control Panel
   - Click Config kat Apache
   - Pilih php.ini
   - Pastikan extension=sqlite3 dah uncomment (takde semicolon ';' kat depan)
   - Save, then restart Apache

## Cara pasang

1. Pastikan XAMPP dah start:
   - Start Apache kat XAMPP Control Panel
   - Check takde error kat log

2. Set permission folder database:
```bash
chmod 755 database/
chmod 644 database/grocery_store.db
```

3. Buat folder utk upload file & logs:
```bash
mkdir -p assets/images/products
mkdir -p logs
```

4. Set permission folder upload & log:
```bash
chmod 755 assets/images/products
chmod 755 logs
```

## Structure Folder

```
grocery_store/
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
├── config/
│   └── database.php
├── includes/
│   ├── functions.php
│   └── constants.php
├── modules/
│   ├── products/
│   ├── orders/
│   ├── payments/
│   └── inventory/
├── database/
│   └── grocery_store.db
└── templates/
    ├── header.php
    └── footer.php
```

## Structure Module

Setiap module ada structure mcm ni:

```
module_name/
├── index.php
├── functions.php
├── config.php
└── templates/
    ├── list.php
    ├── view.php
    ├── add.php
    └── edit.php
```

## Database Schema

Guna SQLite3. Table utama dia:

1. products
   - Info barang & stock
2. orders
   - Detail order & status
3. order_items
   - Barang2 dalam order
4. inventory_logs
   - Track pergerakan stock

## Payment Method

Support payment Malaysia (dummy je la):

1. FPX
   - Bank2 Malaysia yg popular
2. Kad Kredit/Debit
   - Visa
   - Mastercard
   - Kad bank local
3. E-wallet
   - TnG eWallet
   - Boost
   - GrabPay
   - MAE

## Guide Development

1. Style Code
   - Ikut standard PSR-12
   - Nama function & variable kena make sense
   - Comment kalau code complicated
   - Function jgn panjang2

2. Security
   - Check semua input
   - Guna prepared statements
   - Ada CSRF protection
   - Sanitize output

3. Error Handling
   - Log semua error
   - Error message kena user-friendly
   - Log error ikut jenis

## Testing

1. Test apa?
   - CRUD products
   - Flow order
   - Flow payment
   - Stock counting
   - Check input
   - Handle error

2. Data Test
   - Nama product Malaysia
   - No phone Malaysia
   - Alamat Malaysia
   - Harga dalam RM

## Nak contribute?

1. Fork repo ni
2. Buat branch baru
3. Commit changes
4. Push ke branch tu
5. Buat Pull Request

## License

Project ni guna MIT License - tengok file LICENSE utk details.

## Support

Kalau ada masalah, whatsapp.. atau buat issue kat repo ni terus.