#!/bin/bash

# Скрипт для полной настройки и запуска проекта на Yii2
# Использование: ./bin/setup-and-run.sh [skip-tests] [port]

set -e

# Цвета для вывода
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Параметры
SKIP_TESTS=${1:-false}
PORT=${2:-8080}
PROJECT_DIR="short-yii2"
REPO_URL=${REPO_URL:-""} # Можно передать URL репо через переменную окружения

echo -e "${GREEN}=== Настройка и запуск проекта Yii2 ===${NC}"

# 1. Клонирование репозитория (если не существует)
if [ ! -d "$PROJECT_DIR" ]; then
    if [ -z "$REPO_URL" ]; then
        echo -e "${RED}Ошибка: Переменная REPO_URL не установлена и директория $PROJECT_DIR не найдена.${NC}"
        echo "Укажите URL репозитория: export REPO_URL='git@github.com:user/repo.git'"
        exit 1
    fi
    
    echo -e "${YELLOW}Клонирование репозитория...${NC}"
    git clone "$REPO_URL" "$PROJECT_DIR"
    cd "$PROJECT_DIR"
else
    echo -e "${YELLOW}Директория $PROJECT_DIR найдена. Переходим в неё...${NC}"
    cd "$PROJECT_DIR"
    
    # Если это git-репо, подтянем изменения
    if [ -d ".git" ]; then
        echo -e "${YELLOW}Обновление репозитория (git pull)...${NC}"
        git pull || echo -e "${YELLOW}Предупреждение: Не удалось обновить репозиторий (возможно, нет прав или изменены локальные файлы).${NC}"
    fi
fi

# 2. Установка зависимостей Composer
echo -e "${YELLOW}Установка зависимостей Composer...${NC}"
if ! command -v composer &> /dev/null; then
    echo -e "${RED}Ошибка: Composer не установлен. Пожалуйста, установите Composer.${NC}"
    exit 1
fi
composer install --no-interaction --prefer-dist

# 3. Настройка окружения (.env)
if [ ! -f ".env" ]; then
    echo -e "${YELLOW}Файл .env не найден. Копируем из .env.example...${NC}"
    if [ -f ".env.example" ]; then
        cp .env.example .env
        echo -e "${GREEN}Файл .env создан. Пожалуйста, отредактируйте его (особенно настройки БД).${NC}"
        
        # Генерация ключа шифрования (если есть команда yii)
        if [ -f "yii" ]; then
            chmod +x yii
            # Попытка сгенерировать ключ, если в Yii2 есть такая команда или через PHP
            echo -e "${YELLOW}Генерация ключа шифрования...${NC}"
            php -r "echo 'APP_KEY=' . bin2hex(random_bytes(32)) . PHP_EOL;" > .env_key.tmp
            # Простая замена или добавление в файл (упрощенно)
            # В реальном проекте лучше использовать конкретную команду Yii2 или скрипт
        fi
        
        echo -e "${RED}ВНИМАНИЕ: Отредактируйте файл .env перед продолжением!${NC}"
        echo "Настройки БД находятся в файле .env. Убедитесь, что база данных создана."
        read -p "Нажмите Enter, когда закончите редактирование .env..."
    else
        echo -e "${RED}Ошибка: Файл .env.example не найден!${NC}"
        exit 1
    fi
else
    echo -e "${GREEN}Файл .env уже существует.${NC}"
fi

# 4. Применение миграций
echo -e "${YELLOW}Применение миграций базы данных...${NC}"
if [ -f "yii" ]; then
    chmod +x yii
    ./yii migrate --interactive=0 || {
        echo -e "${RED}Ошибка при применении миграций. Проверьте настройки БД в .env.${NC}"
        exit 1
    }
    echo -e "${GREEN}Миграции успешно применены.${NC}"
else
    echo -e "${RED}Ошибка: Файл yii (console entry script) не найден.${NC}"
    exit 1
fi

# 5. Запуск тестов (опционально)
if [ "$SKIP_TESTS" != "skip-tests" ] && [ "$SKIP_TESTS" != "true" ]; then
    echo -e "${YELLOW}Запуск тестов...${NC}"
    if [ -f "vendor/bin/phpunit" ]; then
        vendor/bin/phpunit --colors=always || {
            echo -e "${RED}Тесты не прошли. Продолжение работы невозможно.${NC}"
            exit 1
        }
        echo -e "${GREEN}Все тесты пройдены успешно.${NC}"
    else
        echo -e "${YELLOW}PHPUnit не найден. Пропускаем тесты.${NC}"
    fi
else
    echo -e "${YELLOW}Тесты пропущены (флаг skip-tests).${NC}"
fi

# 6. Запуск встроенного PHP сервера
echo -e "${GREEN}=== Все готово к запуску! ===${NC}"
echo -e "${YELLOW}Запуск встроенного PHP сервера на порту $PORT...${NC}"
echo -e "Откройте в браузере: ${GREEN}http://localhost:$PORT${NC}"
echo -e "Для остановки нажмите Ctrl+C"

# Проверка наличия веб-директории
if [ ! -d "web" ]; then
    echo -e "${RED}Ошибка: Директория web не найдена!${NC}"
    exit 1
fi

php -S localhost:$PORT -t web
