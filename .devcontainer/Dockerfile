FROM php:8.3-cli

# Install mysqli extension
RUN docker-php-ext-install mysqli pdo_mysql

# Install shit
# RUN apt-get update && apt-get install -y git unzip nasm

# Install Git and Composer
RUN apt-get update && apt-get install -y git unzip nasm \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && apt-get clean && rm -rf /var/lib/apt/lists/* \
    && composer update
# RUN apt-get update && apt-get install -y git unzip \
#    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
#    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install FFMpeg and FFProbe
# RUN apt install -y ffmpeg
# RUN curl -O https://ffmpeg.org/releases/ffmpeg-8.1.1.tar.gz && tar -xvzf ffmpeg-8.1.1.tar.gz && ./ffmpeg-8.1.1/configure && make && make install
RUN curl -O https://ffmpeg.org/releases/ffmpeg-8.1.1.tar.gz && tar -xvzf ffmpeg-8.1.1.tar.gz && ./ffmpeg-8.1.1/configure && make install
# RUN cp bin/ffmpeg /usr/bin/ffmpeg && cp bin/ffprobe /usr/bin/ffprobe && chmod +x /bin/ffmpeg && chmod +x /bin/ffprobe
# RUN curl -O https://raw.githubusercontent.com/NoraBohm/qtools/refs/heads/main/bin/ffmpeg \
#    && curl -O https://raw.githubusercontent.com/NoraBohm/qtools/refs/heads/main/bin/ffprobe \
# RUN curl -O http://dempah.com/bin-upload/ffmpeg/ffmpeg \
#     && curl -O http://dempah.com/bin-upload/ffmpeg/ffprobe \
#     && cp ./ffmpeg /usr/bin/ffmpeg && cp ./ffprobe /usr/bin/ffprobe && chmod +x /usr/bin/ffmpeg && chmod +x /usr/bin/ffprobe
# RUN curl -O https://github.com/NoraBohm/video-streaming-service/raw/refs/heads/main/bin/ffmpeg \
#    && curl -O https://github.com/NoraBohm/video-streaming-service/raw/refs/heads/main/bin/ffprobe \
#    && cp ./ffmpeg /usr/bin/ffmpeg && cp ./ffprobe /usr/bin/ffprobe && chmod +x /usr/bin/ffmpeg && chmod +x /usr/bin/ffprobe

# Install Git and Comsposer
# RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
#    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Git and Composer
# Install FFMpeg and FFProbe
# RUN apt-get update && apt-get install -y git unzip \
#     && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
#     && apt-get clean && rm -rf /var/lib/apt/lists/* \
#     && curl -O https://ffmpeg.org/releases/ffmpeg-8.1.1.tar.gz && tar -xvzf ffmpeg-8.1.1.tar.gz && ./ffmpeg-8.1.1/configure && make install \
#     && composer update