# Решение

## Инструкция по запуску сервиса API 
### Локально
1. Установить в системе php 7.4 и composer
1. Перейти в директорию задания 2
    ```shell script
    cd task_2
    ```
1. Установить зависимости
    ```shell script
    composer install
    ```
1. Запустить встроенный сервер
    ```shell script
    php -S localhost:8000
    ```
    результат запуска команды
    ```
    [Sun Dec  6 02:52:17 2020] PHP 7.4.13 Development Server (http://localhost:8000) started
    ```
1. После запуска по ссылке `http://localhost:8000/api/v2` будет доступно API c методами указанными в задании

### На сервере
1. На сервере установить php 7.4 и composer (предполагается, что конфигурация сервера такова, что уже можно запусать php скрипты из браузера)
1. Загрузить файлы из директории `task_2` в корень рабочей директории http
1. Установить зависимости. Выполнить в корне рабочей директории http
    ```shell script
    composer install
    ```
1. По ссылке `http://<server IP>/api/v2` будет доступно API c методами указанными в задании
## Описание API

### Структура директории с исходным кодом
    ```
    .
    ├── README.md                           # Текущий просматриваемый файл
    ├── api
    │   └── v2
    │       └── index.php                   # Точка доступа к API 
    ├── composer.json                       # Файл конфигурации для Composer
    ├── composer.lock                       # Файл c фактическими версиями зависимостей, установленных Composer
    ├── resources                           # Директория с файлами-ресурсами
    │   ├── config.yaml                     # Информация с конфигурацией приложения согласно постановке
    │   └── services.yaml                   # Конфигурация зависимостей приложения
    └── src                                 # Исходный код приложения
        └── App                             # Общий неймспейс для текущего приложения 
            ├── Acl                         # Неймспейс для различных Access Control List (ACL)
            │   └── AccessControl.php       # Класс с реализацией аутентификации пользователей использующих API
            ├── Client                      # Неймспейс для клиентов к внешним ресурсам 
            │   └── BlockchainComClient.php # Клиент для подключения к blockchain.info
            ├── Config.php                  # Класс с реестром настроек API
            ├── Container.php               # Контейнер зависимостей приложения
            └── UseCase                     # Сценарии работы API
                ├── Command.php             # Базовый абстратный класс для всех UseCase
                ├── GetRateCommand.php      # Реализация метода API rate
                └── MakeConvertCommand.php  # Реализация метода API convert
    ```

### Описание работы приложения
При поступлении запроса на файл /api/v2/index.php выполняется следующая последовательность шагов
1. Подключение автолоадера зависимостей и классов приложения 
1. Формирование обхекта Request, содержащее все данные полученные от пользователя API
1. Выполняется проверка токена, если токен некорректный будет отправлен код 401 и выведен ответ
    ```json
    {
        "status": "error",
        "code": 401,
        "message": "Authorization required"
    }
    ```
1. Инициализация роутеров для всех типов HTTP-методов
    - POST
    - GET
    - PUT
    - DELETE и т.д.
1. Для всех HTTP-методов для которых не определен метод API будет возвращен код 400 и ответ, содержащий название HTTP-метода
    ```json
    {
        "status": "error",
        "code": 400,
        "message": "Unknown PUT method"
    }
    ```
1. В случае ошибки (например, сервис blockchain.info не доступен, или произошла внутренняя ошибка), будет возвращен код 503 и ответ содержащий описание ошибки. Например:
    ```json
    {
        "status": "error",
        "code": 503,
        "message": "cURL error 6: Could not resolve host: blockchrrain.info (see https://curl.haxx.se/libcurl/c/libcurl-errors.html) for https://blockchrrain.info/ticker"
    }
    ```
1. В случае некорректных входных параметров будет возвращен код 400 и ответ содержащий описание проблемы. Например:
    ```json
    {
        "status": "error",
        "code": 400,
        "message": "Unknown currency in «currency_to» parameter"
    }
    ```
1. Для существующих методов будет вызван соответствующий сценарий (use case) 
    1. Общее для двух методов: Рассчет курса покупки и продажи с учетом комиссии в 2%
        1. Для продажи выполняется увеличение цены продажи на 2%
        1. Для покупки выполняется уменьшение цены покупки на 2%
    1. Метод rates
        1. Получить данные о курсах с сервиса blockchain.info
        1. Получить массив валют из параметра currency запроса
            1. Если есть хотябы одно значение валюты - отфильтровать массив данных о курсах, оставив только запрошенные валюты
        1. Выполнить расчет курса покупки и продажи валюты с учетом комиссии. Заполняется два поля - sale и buy
        1. Отсортировать массив с курсами в порядке возрастания курса продажи 
        1. Установить код 200 и вернуть в ответ json заданной структуры. 
        1. Примеры работы метода приведены ниже
    1. Метод convert
        1. Выполнить проверку всех входящих параметров:
            - заданы ли параметры
            - существуют ли указанные валюты
            - не отрицательное ли значение колчиества валюты для обмена
            - проверка что обмен выполняется для BTC в любую валюту, и что любая валюта оменивается на BTC, другие обмены не обрабатываются.
            - выполняется проверка, что количество валюты для обмена на BTC не менее 0.01 
        1. Получить информацию о курсах валют с сервиса blockchain.info
        1. Выполнить конвертацию заданной валюты в требуемую валюту с учетом комиссии
        1. Установить код 200 и вернуть в ответ json заданной структуры. 
        1. Примеры работы метода приведены ниже
    
## Ограничения
1. Для решения использовался php 7.4.13.
1. Для запуска API в системе пользователя должен быть установлен php или файлы директории `task_2` должны быть загружены в корневую директорию сервера-httpd
1. Перед запуском необходимо установить зависимости с использованием Composer
1. Не реализовывалась генерация токенов, поэтому случайный набор токенов отчечающих требованиям постановки добавлен в файл [config.yaml](resources/config.yaml)
1. Точка доступа к API имеет следующий путь
    ```
    └─── api
        └── v2
            └── index.php                   # Точка доступа к API 
    ``` 
    и обсусловлено тем, что
    1. для задания точки доступа лучше использовать прокси сервер, например nginx, который позволит "завернуть" все запросы по определенному uri, например `/api/v2` на нужный скрипт php. То есть если у нас добавится API версии 3, то в текущем приложении структура директории будет такая
        ```
        └─── api
            ├── v2
            │   └── index.php                   # Точка доступа к API v2
            └── v3
                └── index.php                   # Точка доступа к API v3
        ``` 
    2. Текущее API согласно постановке является RESTful, а не REST, поэтому не настраивалась обработка путей. То есть приложение не сможет обработать запрос вида `/api/v2/some-method`
1. В роутерах использована конструкция switch..case, так как требуется работать не с REST, а с RESTful в котором метод передается на как часть uri, а как GET-параметр. Если бы реализация методов API соответствовала REST то методы моглибы иметь uri вида '/api/v2/currency/rates', '/api/v2/currency/rate/{currency}' и '/api/v2/currency/{currency_from}/convert/{currency_to}', в этом случае на каждый метод API был бы реализован отдельный роутинг.
1. Не реализован обмен любой валюты на любую, так как не задано в постановке    
1. Для метода rates возвращаются два значения, так как если мы являемся сервисом взимающим комиссию за обработку запросов, то стоимость покупки BTC для клиента возрастает, а стоимость продажи BTC становится меньше.
    
### Пример работы метода rates

###### Запрос 1
    ```
    GET http://localhost:8000/api/v2?method=rates
    Authorization: Bearer 4292c44b-566a-42ef-ac52-a-z_A-Z__0-94292c44b-566a-42ef-ac5454545
    ```

###### Ответ
    ```
    GET http://localhost:8000/api/v2?method=rates
    
    HTTP/1.1 200 OK
    Host: localhost:8000
    Date: Sun, 06 Dec 2020 00:11:42 GMT
    Connection: close
    X-Powered-By: PHP/7.4.13
    Content-Type: application/json;charset=utf-8
    
    {
      "status": "success",
      "code"  : 200,
      "data"  : {
        "GBP": {
          "sell": 14599.3926,
          "buy" : 14026.8674
        },
        "EUR": {
          "sell": 16191.9288,
          "buy" : 15556.951200000001
        },
        "CHF": {
          "sell": 17506.7088,
          "buy" : 16820.171199999997
        },
        "USD": {
          "sell": 19620.852600000002,
          "buy" : 18851.4074
        },
        "CAD": {
          "sell": 25083.503399999998,
          "buy" : 24099.8366
        },
        "SGD": {
          "sell": 26189.9178,
          "buy" : 25162.8622
        },
        "AUD": {
          "sell": 26425.7622,
          "buy" : 25389.4578
        },
        "NZD": {
          "sell": 27840.8796,
          "buy" : 26749.0804
        },
        "PLN": {
          "sell": 72344.061,
          "buy" : 69507.039
        },
        "BRL": {
          "sell": 101181.6234,
          "buy" : 97213.7166
        },
        "DKK": {
          "sell": 120470.0988,
          "buy" : 115745.7812
        },
        "CNY": {
          "sell": 128155.5948,
          "buy" : 123129.8852
        },
        "HKD": {
          "sell": 152068.51559999998,
          "buy" : 146105.0444
        },
        "TRY": {
          "sell": 153052.4994,
          "buy" : 147050.4406
        },
        "SEK": {
          "sell": 165923.28780000002,
          "buy" : 159416.4922
        },
        "TWD": {
          "sell": 553631.9178,
          "buy" : 531920.8622
        },
        "THB": {
          "sell": 592942.3404,
          "buy" : 569689.6996
        },
        "INR": {
          "sell": 1447990.8168000001,
          "buy" : 1391206.8632
        },
        "RUB": {
          "sell": 1454337.1854,
          "buy" : 1397304.3546
        },
        "JPY": {
          "sell": 2043841.1772,
          "buy" : 1963690.5428000002
        },
        "ISK": {
          "sell": 2462221.3806,
          "buy" : 2365663.6794
        },
        "CLP": {
          "sell": 14595968.7558,
          "buy" : 14023577.824199999
        },
        "KRW": {
          "sell": 21266458.5432,
          "buy" : 20432479.7768
        }
      }
    }
    
    Response code: 200 (OK); Time: 506ms; Content length: 1133 bytes
    ```

###### Запрос 2
    ```
    GET http://localhost:8000/api/v2?method=rates&currency=USD,RUB,EUR,KRW
    Authorization: Bearer 4292c44b-566a-42ef-ac52-a-z_A-Z__0-94292c44b-566a-42ef-ac5454545
    
    ```

###### Ответ
    ```
    GET http://localhost:8000/api/v2?method=rates&currency=USD%2CRUB%2CEUR%2CKRW
    
    HTTP/1.1 200 OK
    Host: localhost:8000
    Date: Sun, 06 Dec 2020 00:13:10 GMT
    Connection: close
    X-Powered-By: PHP/7.4.13
    Content-Type: application/json;charset=utf-8
    
    {
      "status": "success",
      "code"  : 200,
      "data"  : {
        "EUR": {
          "sell": 16186.2474,
          "buy" : 15551.492600000001
        },
        "USD": {
          "sell": 19615.518,
          "buy" : 18846.282000000003
        },
        "RUB": {
          "sell": 1453941.2112,
          "buy" : 1396923.9088
        },
        "KRW": {
          "sell": 21260668.3296,
          "buy" : 20426916.630400002
        }
      }
    }
    
    Response code: 200 (OK); Time: 620ms; Content length: 242 bytes
    ```

###### Запрос 3
    ```
    GET http://localhost:8000/api/v2?method=rates&currency=UAH
    Authorization: Bearer 4292c44b-566a-42ef-ac52-a-z_A-Z__0-94292c44b-566a-42ef-ac5454545
    ```

###### Ответ
    ```
    GET http://localhost:8000/api/v2?method=rates&currency=UAH
    
    HTTP/1.1 200 OK
    Host: localhost:8000
    Date: Sun, 06 Dec 2020 00:13:50 GMT
    Connection: close
    X-Powered-By: PHP/7.4.13
    Content-Type: application/json;charset=utf-8
    
    {
      "status": "success",
      "code"  : 200,
      "data"  : []
    }
    
    Response code: 200 (OK); Time: 209ms; Content length: 41 bytes
    ```

### Пример работы метода convert

###### Запрос 1
    ```
    POST http://localhost:8000/api/v2?method=convert
    Authorization: Bearer 4292c44b-566a-42ef-ac52-a-z_A-Z__0-94292c44b-566a-42ef-ac5454545
    Content-Type: application/x-www-form-urlencoded
    
    currency_from=BTC&currency_to=USD&value=100.00
    ```

###### Ответ
    ```
    POST http://localhost:8000/api/v2?method=convert
    
    HTTP/1.1 200 OK
    Host: localhost:8000
    Date: Sun, 06 Dec 2020 00:33:05 GMT
    Connection: close
    X-Powered-By: PHP/7.4.13
    Content-Type: application/json;charset=utf-8
    
    {
      "status": "success",
      "code"  : 200,
      "data"  : {
        "currency_from": "BTC",
        "currency_to": "USD",
        "value": 100,
        "converted_value": 1959425.1,
        "rate": 19594.251
      }
    }
    
    Response code: 200 (OK); Time: 203ms; Content length: 139 bytes
    ```

###### Запрос 2
    ```
    POST http://localhost:8000/api/v2?method=convert
    Authorization: Bearer 4292c44b-566a-42ef-ac52-a-z_A-Z__0-94292c44b-566a-42ef-ac5454545
    Content-Type: application/x-www-form-urlencoded
    
    currency_from=BTC&currency_to=RUB&value=100.00
    ```

###### Ответ
    ```
    POST http://localhost:8000/api/v2?method=convert
    
    HTTP/1.1 200 OK
    Host: localhost:8000
    Date: Sun, 06 Dec 2020 00:33:51 GMT
    Connection: close
    X-Powered-By: PHP/7.4.13
    Content-Type: application/json;charset=utf-8
    
    {
      "status": "success",
      "code"  : 200,
      "data"  : {
        "currency_from": "BTC",
        "currency_to": "RUB",
        "value": 100,
        "converted_value": 145301376.6,
        "rate": 1453013.766
      }
    }
    
    Response code: 200 (OK); Time: 197ms; Content length: 143 bytes
    ```

###### Запрос 3
    ```
    POST http://localhost:8000/api/v2?method=convert
    Authorization: Bearer 4292c44b-566a-42ef-ac52-a-z_A-Z__0-94292c44b-566a-42ef-ac5454545
    Content-Type: application/x-www-form-urlencoded
    
    currency_from=USD&currency_to=BTC&value=100.00
    ```

###### Ответ
    ```
    POST http://localhost:8000/api/v2?method=convert
    
    HTTP/1.1 200 OK
    Host: localhost:8000
    Date: Sun, 06 Dec 2020 00:34:21 GMT
    Connection: close
    X-Powered-By: PHP/7.4.13
    Content-Type: application/json;charset=utf-8
    
    {
      "status": "success",
      "code"  : 200,
      "data"  : {
        "currency_from": "USD",
        "currency_to": "BTC",
        "value": 100,
        "converted_value": 0.005309474,
        "rate": 18834.257400000002
      }
    }
    
    Response code: 200 (OK); Time: 231ms; Content length: 150 bytes
    ```


###### Запрос 4
    ```
    POST http://localhost:8000/api/v2?method=convert
    Authorization: Bearer 4292c44b-566a-42ef-ac52-a-z_A-Z__0-94292c44b-566a-42ef-ac5454545
    Content-Type: application/x-www-form-urlencoded
    
    currency_from=USD&currency_to=BTC&value=0.01
    ```

###### Ответ
    ```
    POST http://localhost:8000/api/v2?method=convert
    
    HTTP/1.1 200 OK
    Host: localhost:8000
    Date: Sun, 06 Dec 2020 00:34:45 GMT
    Connection: close
    X-Powered-By: PHP/7.4.13
    Content-Type: application/json;charset=utf-8
    
    {
      "status": "success",
      "code"  : 200,
      "data"  : {
        "currency_from": "USD",
        "currency_to": "BTC",
        "value": 0.01,
        "converted_value": 5.309e-7,
        "rate": 18834.257400000002
      }
    }
    
    Response code: 200 (OK); Time: 189ms; Content length: 148 bytes
    ```


###### Запрос 5
    ```
    POST http://localhost:8000/api/v2?method=convert
    Authorization: Bearer 4292c44b-566a-42ef-ac52-a-z_A-Z__0-94292c44b-566a-42ef-ac5454545
    Content-Type: application/x-www-form-urlencoded
    
    currency_from=BTC&currency_to=USD&value=0.01
    ```

###### Ответ
    ```
    POST http://localhost:8000/api/v2?method=convert
    
    HTTP/1.1 200 OK
    Host: localhost:8000
    Date: Sun, 06 Dec 2020 00:35:22 GMT
    Connection: close
    X-Powered-By: PHP/7.4.13
    Content-Type: application/json;charset=utf-8
    
    {
      "status": "success",
      "code"  : 200,
      "data"  : {
        "currency_from": "BTC",
        "currency_to": "USD",
        "value": 0.01,
        "converted_value": 196.01,
        "rate": 19601.350199999997
      }
    }
    
    Response code: 200 (OK); Time: 193ms; Content length: 146 bytes
    ```



