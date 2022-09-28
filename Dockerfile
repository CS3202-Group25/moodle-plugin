FROM pabasaradilshan/moodle:latest
RUN php /var/www/html/admin/cli/purge_caches.php
