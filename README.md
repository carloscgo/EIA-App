## Comandos Composer para usar Docker

`IMPORTANTE:` los comandos de instalacion `docker-install-front`, `docker-install-back` y `docker-install-all` crean una instalacion limpia borrando el archivo `composer.lock` y el directorio `/vendor/`.

```bash
# Construccion de imagen docker
composer run-script docker-build
```

```bash
# Levantar servicios docker
composer run-script docker-up
```

```bash
# Instalar paquetes composer del proyecto frontend
composer run-script docker-install-front
```

```bash
# Instalar paquetes composer del proyecto backend
composer run-script docker-install-back
```

```bash
# Instalar paquetes composer de ambos proyectos frontend y backend simultaneamente
composer run-script docker-install-back
```
