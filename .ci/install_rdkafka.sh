#!/bin/sh

echo ${LIBRDKAFKA_VERSION}

git clone --depth 1 --branch "v1.0.1" https://github.com/edenhill/librdkafka.git
(
    cd librdkafka
    ./configure
    make
    sudo make install
)
sudo ldconfig

pecl install rdkafka
