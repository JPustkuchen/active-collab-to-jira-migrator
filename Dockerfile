# This dockerfile replicates the "how to" steps in this guide:
# https://github.com/JPustkuchen/active-collab-to-jira-migrator#how

FROM php:7.3.28-buster

# ---------- Install requirements ---------- #
RUN apt-get update && apt-get install -y wget && \
    # cleanup to reduce image size
    apt-get clean && rm -rf /var/lib/apt/lists/*

# Install composer
RUN wget -O composer-setup.php https://getcomposer.org/installer && \
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer && \
    composer self-update


# ---------- Set up non-root user ---------- #
ENV USER=migrator
RUN useradd --create-home ${USER} -s /bin/bash && echo "alias ll='ls -alF'" >> /home/${USER}/.bashrc


# ---------- Set up application ---------- #
ARG SECRET=mysecret
ARG ACTIVE_COLLAB_URL=https://app.activecollab.com/123456/
ARG MEMORY_LIMIT=4096M

# copy repo files in to docker
COPY . /home/${USER}/active-collab-to-jira-migrator
RUN chown -R ${USER}:${USER} /home/${USER}

# switch to non-root user
WORKDIR /home/${USER}/active-collab-to-jira-migrator
USER ${USER}

# set up config
RUN mv config/EXAMPLE.config.php config/config.php && \
    mv config/EXAMPLE.modifications.php config/modifications.php && \
    sed -i "s#\$acUrl = 'https://ac.mycompany.com/';#\$acUrl = '${ACTIVE_COLLAB_URL}';#g" config/config.php && \
    sed -i "s#// \$secret = urlencode('');#\$secret = urlencode('${SECRET}');#g" config/config.php && \
    sed -i "s#ini_set('memory_limit', '4GB');#ini_set('memory_limit', '${MEMORY_LIMIT}');#g" config/config.php && \
    composer install


ENTRYPOINT ["php", "-S", "0.0.0.0:8080", "-t", "app/"]
