# Usa una imagen base de Microsoft optimizada para Dev Containers y PHP
FROM mcr.microsoft.com/devcontainers/php:8.2

# Instala las extensiones de PHP que Laravel necesita
RUN sudo apt-get update && export DEBIAN_FRONTEND=noninteractive \
    && sudo apt-get -y install --no-install-recommends libzip-dev libgd-dev libpng-dev \
    && sudo docker-php-ext-install pdo_mysql zip gd bcmath

# Limpia la caché de apt
RUN sudo apt-get clean && sudo rm -rf /var/lib/apt/lists/*

# Establece el directorio de trabajo
WORKDIR /workspaces/${localWorkspaceFolderBasename}