# hue-cli
A symfony CLI for Philips Hue bulbs


## Install
```
php -r "copy('https://raw.githubusercontent.com/peledies/hue-cli/0.1.3/hue.phar', 'hue'); copy('hue', '/usr/local/bin/hue'); unlink('hue.phar');"
```

## Useage
```
hue user:create [name]

hue list
```




### Install Development Version

Clone the repo
```
composer install
```

### Create User
```
php hue user:create [name]
```

### List Commands
```
php hue list
```

# phar build
### setup build environment
```
sudo cp /etc/php.ini.default /etc/php.ini

phar.readonly = Off

curl -LSs https://box-project.github.io/box2/installer.php | php

cp box.phar /usr/local/bin/box
```

### build a phar
```
box build
```