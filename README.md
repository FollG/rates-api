# Rates API

JSON API сервис для получения курсов валют относительно USD и выполнения конвертации между поддерживаемыми валютами.

Сервис получает курсы из внешних провайдеров, нормализует их к единому формату, кеширует актуальные значения и предоставляет защищенный API с поддержкой фильтрации и конвертации.

## Возможности

- Получение списка актуальных курсов валют
- Фильтрация курсов по выбранным валютам
- Конвертация между валютами
- Поддержка комиссии сервиса
- Bearer Token аутентификация
- Кеширование курсов через Redis
- Fallback на резервного провайдера при недоступности основного
- Единый формат JSON ответов
- Docker окружение для быстрого запуска

---

## Технологии

- PHP 8.2+
- Yii2 Framework
- Nginx
- Redis
- Docker / Docker Compose
- PHPUnit

---

## Архитектура

Общая схема взаимодействия:

```
Client
  |
  v
Nginx
  |
  v
Yii2 API Application
  |
  +--> Authentication
  |
  +--> Validation
  |
  +--> RatesService
  |
  +--> ConversionService
          |
          +--> Rate Providers
                  |
                  +--> CoinGate API
                  |
                  +--> CoinCap API
```

Приложение не зависит от конкретного внешнего источника курсов. Провайдеры преобразуют внешние данные в единый внутренний формат.

---

# Запуск проекта

## Требования

Перед началом необходимо установить:

- Docker
- Docker Compose

Проверить установку:

```bash
docker --version
docker compose version
```

---

## Клонирование проекта

```bash
git clone <repository-url>

cd rates-api
```

---

# Запуск через Docker

Собрать контейнеры:

```bash
docker compose build
```

Запустить сервисы:

```bash
docker compose up -d
```

Проверить состояние контейнеров:

```bash
docker compose ps
```

После успешного запуска API будет доступен:

```
http://localhost:8080
```

---

# Установка зависимостей

Composer устанавливается внутри PHP контейнера.

Для установки зависимостей:

```bash
docker compose exec php composer install
```

---

# Остановка проекта

Остановить контейнеры:

```bash
docker compose down
```

Удалить контейнеры вместе с volume:

```bash
docker compose down -v
```

---

# API

Все запросы выполняются через:

```
GET /api/v1
POST /api/v1
```

Все методы требуют авторизацию.

Заголовок:

```
Authorization: Bearer <token>
```

---

# Получение курсов валют

## Метод

```
GET /api/v1?method=rates
```

## Пример запроса

```bash
curl \
-H "Authorization: Bearer your-secret-token" \
"http://localhost:8080/api/v1?method=rates"
```

## Ответ

```json
{
    "status": "success",
    "code": 200,
    "fetched_at": <date>,
    "data": {
        "BTC": "0.000023",
        "ETH": "0.00035",
        "EUR": "0.92"
    }
}
```

---

## Фильтрация валют

Можно получить только необходимые валюты:

```
GET /api/v1?method=rates&currency=BTC,ETH
```

Пример:

```bash
curl \
-H "Authorization: Bearer your-secret-token" \
"http://localhost:8080/api/v1?method=rates&currency=BTC,ETH"
```

Ответ:

```json
{
    "status": "success",
    "code": 200,
    "fetched_at": <date>,
    "data": {
        "BTC": "0.000023",
        "ETH": "0.00035"
    }
}
```

---

# Конвертация валют

## Метод

```
POST /api/v1?method=convert
```

## Параметры

| Параметр | Описание |
|---|---|
| currency_from | исходная валюта |
| currency_to | целевая валюта |
| value | сумма для конвертации |

---

## Пример запроса

```bash
curl \
-X POST \
-H "Authorization: Bearer your-secret-token" \
-H "Content-Type: application/json" \
"http://localhost:8080/api/v1?method=convert&currency_from=USD&currency_to=EUR&value=1000"
```

---

## Ответ

```json
{
    "status": "success",
    "code": 200,
    "data": {
        "currency_from": "USD",
        "currency_to": "EUR",
        "value": "1000",
        "result": "25.1234567890",
        "converted_value": "839.04",
        "rate": "0.85"
    }
}
```

---

# Комиссия

Комиссия сервиса составляет:

```
2%
```

Расчет:

```
converted_value = value * rate * 0.98
```

Порядок операций:

1. Расчет значения по курсу
2. Вычитание комиссии
3. Округление результата

Округление:

- криптовалюты — до 10 знаков после запятой
- фиатные валюты — до 2 знаков после запятой

---

# Провайдеры курсов

Используются два источника:

## Основной

CoinGate Rates API

```
https://api.coingate.com/api/v2/rates
```

## Резервный

CoinCap Rates API

```
https://api.coincap.io/v2/rates
```

Логика работы:

1. Запрос к основному провайдеру
2. При ошибке используется резервный
3. Если оба недоступны — используется последний закешированный snapshot

---

# Redis Cache

Используется для:

- хранения последнего успешного ответа провайдера
- снижения количества внешних запросов
- работы fallback режима

TTL:

| Состояние | TTL |
|-|-|
| Обычный кеш | 30 секунд |
| Недоступность провайдеров | 3600 секунд |

---

# Тестирование

Запуск unit/integration тестов:

```bash
docker compose exec php vendor/bin/phpunit
```

Для интеграционных тестов:

```bash
docker compose exec php vendor/bin/phpunit -c phpunit.integration.xml
```

---

# Логи

Логи приложения находятся в:

```
runtime/logs/
```

---

# Структура проекта

```
.
├── config/              # Конфигурация приложения
├── controllers/         # API контроллеры
├── services/            # Бизнес-логика
├── providers/           # Внешние источники курсов
├── repositories/        # Работа с хранилищами
├── components/          # Общие компоненты
├── dto/                 # Data Transfer Objects
├── tests/               # Тесты
├── docker/              # Docker конфигурация
├── web/                 # Точка входа приложения
└── compose.yaml
```

---

# Возможное расширение

Архитектура позволяет добавлять новые источники курсов без изменения бизнес-логики:

1. Создать новый provider
2. Реализовать общий интерфейс провайдера
3. Добавить его в конфигурацию DI контейнера

Бизнес-сервисы при этом остаются неизменными.

---

# License

MIT
