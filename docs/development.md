# Локальная разработка

## Docker

Требования: Docker с Compose v2.

1. При необходимости создайте `.env` из шаблона:

   ```sh
   cp .env .env
   ```

2. Соберите образы и запустите приложение:

   ```sh
   docker compose up --build
   ```

   Nginx будет доступен по адресу `http://localhost:8080`. Измените APP_PORT в
   .env если этот порт уже занят.

3. Выполняйте команды Composer и PHP внутри PHP-контейнера:

   ```sh
   docker compose exec php composer install
   docker compose exec php php -v
   ```

## Redis

Redis запускается вместе с приложением и доступен из PHP контейнера по адресу
redis:6379. Значения можно переопределить через REDIS_HOST и REDIS_PORT в
.env. Redis не пробрасывает порт на хост, а его данные сохраняются в томе redis-data.
