#!/bin/bash

# Переход в корень проекта (на случай запуска из другой директории)
cd "$(dirname "$0")/.." || exit

echo "🚀 Запуск тестов Yii2..."

# Проверка наличия vendor
if [ ! -d "vendor" ]; then
    echo "❌ Зависимости не установлены. Выполните: composer install"
    exit 1
fi

# Запуск PHPUnit с конфигурацией из phpunit.xml
./vendor/bin/phpunit --colors=always "$@"

exit_code=$?

if [ $exit_code -eq 0 ]; then
    echo "✅ Все тесты пройдены успешно!"
else
    echo "⚠️ Тесты завершились с ошибками (код: $exit_code)"
fi

exit $exit_code