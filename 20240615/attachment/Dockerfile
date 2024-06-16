FROM httpd:2.4.58-bookworm

COPY app/html /usr/local/apache2/htdocs

COPY app/httpd.conf /usr/local/apache2/conf/httpd.conf
RUN chown -R www-data /usr/local/apache2/htdocs
RUN chgrp -R www-data /usr/local/apache2/htdocs
RUN chmod -R 777 /usr/local/apache2/htdocs

COPY app/entrypoint.sh /

ENTRYPOINT ["bash", "-c", "/entrypoint.sh"]