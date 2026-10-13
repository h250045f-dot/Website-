HIT GAMING CLUB WEBSITE  (ISE 2102 Mini Project)
================================================
FILES
  index.html / about.html / events.html / gallery.html / join.html   - the 5 pages
  process.php   - processes the Join/Contact form, shows a response page
  admin.php     - password-protected view of submissions (retrieval)
  config.php    - database settings + admin password (EDIT THESE)
  setup.sql     - creates the MySQL table
  css/style.css, js/main.js, images/  - styling, scripts, gallery images

HOW TO DEPLOY (any PHP host, e.g. InfinityFree, 000webhost-style free hosts, cPanel hosting)
  1. Create a MySQL database in your host's control panel, open phpMyAdmin and run setup.sql
     (change the DB name if your host adds a prefix).
  2. Edit config.php with your host's DB host, name, user and password.
  3. Upload ALL files and folders (keep the structure) to htdocs / public_html.
  4. Visit your URL, submit the form, then check /admin.php.
  If you skip the database, process.php automatically saves to data/submissions.csv.
  5. Submit your live URL.

BEFORE SUBMITTING
  - Replace images/gallery1-8.png with your own club photos (same file names, or edit galleryItems in js/main.js).
  - Change the club name, committee roles, events and contact email to your real details.
  - Change ADMIN_PASSWORD in config.php.
