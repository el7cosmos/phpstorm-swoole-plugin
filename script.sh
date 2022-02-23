#!/usr/bin/env sh
rm -rf phpstorm-swoole-plugin.jar swoole-meta
#./vendor/swoole/ide-helper/bin/generator.sh 4.5.2
mv vendor/swoole/ide-helper/src swoole-meta
zip -r phpstorm-swoole-plugin.jar META-INF swoole-meta
$(which composer) install
