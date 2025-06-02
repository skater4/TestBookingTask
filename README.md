# README.md

## Шаги установки и запуска

1. **Создать файл окружения `.env`**
   ```bash
   cp .env.example .env
   ````

    ```bash
   cp src/.env.example src/.env
   ```
   
2. **Собрать проект**
   ```bash
   make build && make up
   ```
   
3. **Сидируем базу и получаем токен**
    ```bash
   make seed
   ```

4. **Запуск тестов**
    ```bash
   make test
   ```