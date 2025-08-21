Por defecto, XAMPP expone la ruta /opt/lampp/htdocs/ para linus
En mi caso, utilizaré esta ruta: /home/jorge/Teoría_Sistemas/

Para cambiarlo: 
1. Abrir el archivo de configuracion de XAMPP
```
sudo nano /opt/lampp/etc/httpd.conf
```

2. Reemplazar: 
```
DocumentRoot "/opt/lampp/htdocs"
<Directory "/opt/lampp/htdocs">
    ... y el resto hasta la etiqueta
</Directory>
```

Por: 
```
DocumentRoot "/home/jorge/Teoría_Sistemas"
<Directory "/home/jorge/Teoría_Sistemas">
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>
```

3. Dar permisos a la carpeta:
```
chmod -R 755 "/home/jorge/Teoría_Sistemas"
chown -R jorge:jorge "/home/jorge/Teoría_Sistemas"
```


Por comodidad también realicé enlaces simbolicos para xampp y sus utilidades
1. Abrir el archivo zshrc para crear los enlaces: 
```
sudo ~/.zshrc
```
2. Al final del archivo colocar los enlaces:
```
alias xampp='sudo /opt/lampp/manager-linux-x64.run'
alias xampp-mysql='/opt/lampp/bin/mysql -u root -p'
```

