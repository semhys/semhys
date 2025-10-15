Diag endpoints disabled

The diagnostic and test endpoints were disabled by creating a .disabled.php copy and removing the original file names from the webroot.

To restore an endpoint:
1. Move the corresponding file from filename.disabled.php to filename.php
   e.g., mv diag_protected.php.disabled.php diag_protected.php
2. Ensure the diagnostic token file `.diag_token` exists outside the webroot and the config file is correctly loaded.
3. Remove or protect the endpoint after use.

Files disabled:
- diag_protected.php.disabled.php
- test_db.php.disabled.php
- test_smtp.php.disabled.php
- init_db.php.disabled.php
- diag.php.disabled.php

Security note: Keep these files out of public_html or keep them accessible only by IP while debugging.
