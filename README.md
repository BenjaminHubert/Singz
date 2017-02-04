SINGZ
========================
What's inside?
--------------
Singz permet de poster des vidéos de soi chantant ou jouant d’un instrument ou bien les deux en même temps pour les plus 
doués, pour une durée maximum de 30 secondes. En tant que Singzer (membre), vous avez la possibilité d’être suivi par des 
fans, lesquels peuvent vous donner une note allant de 1 à 5. Ainsi, votre notoriété augmente et vous aurez plus de chance 
d’apparaître en tête parmi les posts de la page d’accueil de vos fans. En plus de cette note, les fans ont la possibilité 
de commenter et le partager du contenu.

Une autre catégorie en plus des Singzer existe sur Singz: Starz. Les Starz sont des artistes reconnu dans le domaine de 
la musique ou bien validés par la communauté Singz et en aval validés par le staff Singz. Finalement, Bruno Mars, Beyoncé 
ou bien un Singzer aimé des fans Singz, pourront se retrouver sur la même page Starz.

En plus de cette interface utilisateur il sera possible aux artistes de demander à se faire supporter via des dons. 
Ainsi de nouvelles étoiles pourront parcourir les cieux en quête de célébrité et les adeptes de talents musicaux en 
auront pour leur argent et leurs oreilles.  

Prerequisites
-------
You need some installations to run Singz on your computer: 
* A web server working with PHP >=5.5.9
* Composer
* A database server
* Git

How to?
-------
* `git clone git@github.com:BenjaminHubert/Singz.git`
* `cd Singz`
* Create an empty file named `app/config/parameters.yml`
* Copy and paste the content from `app/config/parameters.yml.dist` to `app/config/parameters.yml`. Set the parameters.
* Download the FFMPEG binaries files 
  * On windows: https://ffmpeg.zeranoe.com/builds/. After downloading the zip file, you will need only two files. Copy them in any directory you want:
    * bin/ffmpeg.exe
    * bin/ffprobe.exe
  * On Mac OS: http://www.ffmpegmac.net/. After downloading the zip file, you will need only two files. Copy them in any directory you want:
    * ffmpeg
    * ffprobe
  * On Linux Debian 8 **(It takes a while, up to 70 minutes...)**:
    * `cd /opt`
    * `sudo apt-get install yasm nasm \ 
            build-essential automake autoconf \
            libtool pkg-config libcurl4-openssl-dev \
            intltool libxml2-dev libgtk2.0-dev \
            libnotify-dev libglib2.0-dev libevent-dev \
            checkinstall`
    * `git clone git://git.videolan.org/ffmpeg.git`
    * `cd ffmpeg`
    * `./configure --prefix=/usr --enable-gpl --enable-libx264`
    * `time make -j 8`
    * `mkdir /usr/share/ffmpeg`
    * `cat RELEASE`
    * `sudo checkinstall`. A menu is displayed. Enter the number version indicated by the previous command
    * `dpkg --install ffmpeg_*.deb`
    * `apt-get install ffmpeg`
  
* Fill the ffmpeg and ffprobe parameters in the parameters.yml file by adding the absolute path of that program 
* `composer install --no-scripts`
* `php bin/console doctrine:database:create`. Sometimes, this command is faced some issue ('unknown database'). Try it again and again or check your database parameters
* `php bin/console doctrine:schema:update --force`
* `php bin/console assets:install`
* `composer install`
* If you're not working on Windows, `chown -R www-data:www-data /var/www/Singz`

Fake data
-------
In order to play with the application and instead of trying to add content by yourself, we put in place an easy command line to do the job for you.
`bin/console doctrine:fixtures:load -vvv`. Enter `Y` to accept to delete all previous data (the very first time, you won't have any data). Furthermore, be aware that **it might take a while, up to 3 minutes.**

Credits
-------

Created with Love in [**Paris**][6] 

* [**Bertrand Freylin**][1]
* [**Younes Sadmi**][2]
* [**Benjamin Hubert**][3]
* [**Thibault Lenormand**][4]
* [**Axel Delannay**][5]

[1]:  https://github.com/BertrandFreylin
[2]:  https://github.com/younessadmi
[3]:  https://github.com/BenjaminHubert
[4]:  https://github.com/ThibaultLenormand
[5]:  https://github.com/axeldelannay/
[6]:  https://en.wikipedia.org/wiki/Paris


