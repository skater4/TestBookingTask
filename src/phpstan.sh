#!/bin/sh

# Параметры по умолчанию
error_file="phpstan.xml"

show_help() {
    echo
    echo "Использование: phpstan.sh [OPTIONS]"
    echo "Опции:"
    echo "  --error-file-name, -efn <файл>   Указывает файл для ошибок. По умолчанию $error_file"
    echo "  --gr-by-file, -gbf               Группирует ошибки по файлам."
    echo "  --gr-by-file-save, -gbfs <файл>  Сохраняет сгруппированные ошибки по файлам."
    echo "  --gr-by-err, -gbe                Группирует ошибки по их названию."
    echo "  --gr-by-err-save, -gbes <файл>   Сохраняет сгруппированные ошибки по их названию."
    echo "  --help, -h                       Выводит это сообщение."
    echo
    echo "Примеры:"
    echo "  ./phpstan.sh --error-file-name phpstan.xml -gbf"
    echo "  ./phpstan.sh -efn gr-by-file.log --gr-by-err-save gr-by-err.log"
    echo
    echo
}

# Проверка аргументов
while [ "$#" -gt 0 ]; do
    case "$1" in
        --help|-h)
            show_help
            exit 0
            ;;
        --error-file-name|-efn)
            shift # Переход к следующему аргументу
            if [ "$#" -gt 0 ]; then
                error_file="$1"
                shift
            else
                echo "${RED}Error: не указан файл для параметра --error-file-name${NO_COLOR}"
                exit 1
            fi
            ;;
        -efn)
            shift
            if [ "$#" -gt 0 ]; then
                error_file="$1"
                shift
            else
                echo "${RED}Error: не указан файл для параметра -efn${NO_COLOR}"
                exit 1
            fi
            ;;
        --gr-by-file|-gbf)
            shift
            gr_by_file=1
            ;;
        --gr-by-file-save|-gbfs)
            shift
            if [ "$#" -gt 0 ]; then
                gr_by_file=1
                grouped_by_file_output="$1"
                shift
            else
                echo "${RED}Error: не указан файл для параметра --gr-by-file${NO_COLOR}"
                exit 1
            fi
            ;;
        --gr-by-err|-gbe)
            shift
            gr_by_err=1
            ;;
        --gr-by-err-save|-gbes)
            shift
            if [ "$#" -gt 0 ]; then
                gr_by_err=1
                grouped_by_err_output="$1"
                shift
            else
                echo "${RED}Error: не указан файл для параметра --gr-by-err${NO_COLOR}"
                exit 1
            fi
            ;;
        *)
            echo "${RED}Error: не распознанный параметр $1${NO_COLOR}"
            exit 1
            ;;
    esac
done

# Цвета для вывода
RED='\033[0;31m'      # Красный
GREEN='\033[0;32m'    # Зеленый
YELLOW='\033[1;33m'   # Желтый
NO_COLOR='\033[0m'    # Без цвета


echo ""
echo "${GREEN}Start Analyze${NO_COLOR}"
echo ""

# Удаление файла с ошибками
if [ -f "$error_file" ]; then
    rm "$error_file"
    if [ -f "$error_file" ]; then
        echo ""
    else
        echo "🗑️  Removed $error_file file."
        echo ""
    fi
fi

# Запуск PHPStan и сохранение вывода в файл
./vendor/bin/phpstan analyse --error-format=checkstyle --memory-limit=1G > "$error_file"

error_occurred=0
error_count=0
total_files=$(grep -o '<file name=' "$error_file" | wc -l)

# Временные файлы для хранения уникальных ошибок и группировки ошибок
temp_file=$(mktemp)
errors_grouped_file=$(mktemp)
file_grouped_errors=$(mktemp)
unique_messages_file=$(mktemp)

# Анализ содержимого phpstan.xml
current_file=""
while IFS= read -r line; do
    # Если строка содержит тег <file>, извлекаем имя файла
    if echo "$line" | grep -q '<file name='; then
        current_file=$(echo "$line" | grep -o 'name="[^"]*"' | sed 's/name="//;s/"//')
    fi

    # Если строка содержит тег <error>, извлекаем информацию об ошибке
    if echo "$line" | grep -q '<error'; then
        line_num=$(echo "$line" | grep -o 'line="[0-9]*"' | sed 's/line="//;s/"//')
        error_message=$(echo "$line" | grep -o 'message="[^"]*"' | sed 's/message="//;s/"//')

        # Если имя файла не определено, используем "Unknown file"
        if [ -z "$current_file" ]; then
            current_file="Unknown file"
        fi

        # Формируем уникальный ключ для ошибки
        unique_error_key="${current_file}:${line_num}:${error_message}"

        # Проверяем, была ли такая ошибка уже выведена
        if ! grep -Fxq "$unique_error_key" "$temp_file"; then
            # Если ошибка уникальна, добавляем её в временный файл и выводим
            echo "$unique_error_key" >> "$temp_file"
            #echo "  ${YELLOW}Error in file ${NO_COLOR}$current_file ${YELLOW}on line ${NO_COLOR}$line_num${YELLOW}:${RED} $error_message"
            error_occurred=1
            error_count=$((error_count + 1))

            # Записываем ошибку для группировки по ошибкам, используя разделитель @@@
            echo "$error_message@@@$current_file:$line_num" >> "$errors_grouped_file"

             # Записываем ошибку для группировки по файлам, используя разделитель @@@
            echo "$current_file@@@$line_num: $error_message" >> "$file_grouped_errors"
        fi

        # Сохраняем уникальное сообщение об ошибке
        if ! grep -Fxq "$error_message" "$unique_messages_file"; then
            echo "$error_message" >> "$unique_messages_file"
        fi
    fi
done < "$error_file"

# Подсчёт количества уникальных ошибок
unique_errors_count=$(wc -l < "$unique_messages_file")


# Группировка по файлам
if [ -n "$gr_by_file" ]; then
    if [ -f "$file_grouped_errors" ]; then
        echo "====================================================================="
        echo "====================================================================="
        echo ""
        echo "${GREEN}Grouped errors by files:${NO_COLOR}"
        echo ""
        echo "====================================================================="
        output=$(echo ""
        sort "$file_grouped_errors" | awk -F'@@@' '
        BEGIN {
            yellow = "'${YELLOW}'"
            nocolor = "'${NO_COLOR}'"
        }
        {
            errors[$1] = errors[$1] "\n  " $2
        }
        END {
            for (msg in errors) {
                print yellow msg nocolor errors[msg] "\n"
            }
        }')

        echo "$output"

        # Сохранение в файл, если указано
        if [ -n "$grouped_by_file_output" ]; then
            rm "$grouped_by_file_output"
            echo "$output" > "$grouped_by_file_output"
            echo "📝 Saved grouped errors by files in: $grouped_by_file_output"
        fi

        if [ -f "$errors_grouped_file" ]; then
            echo ""
            echo ""
        fi
    fi
fi


# Группируем по ошибкам
if [ -n "$gr_by_err" ]; then
    if [ -f "$errors_grouped_file" ]; then
        echo "====================================================================="
        echo "====================================================================="
        echo ""
        echo "${GREEN}Grouped errors by message:${NO_COLOR}"
        echo ""
        echo "====================================================================="
        output=$(echo ""
        sort "$errors_grouped_file" | awk -F'@@@' '
        BEGIN {
            yellow = "'${YELLOW}'"
            nocolor = "'${NO_COLOR}'"
        }
        {
           errors[$1] = errors[$1] "\n  " $2
        }
        END {
            for (msg in errors) {
                print yellow msg nocolor errors[msg] "\n"
            }
        }')

        echo "$output"

        # Сохранение в файл, если указано
        if [ -n "$grouped_by_err_output" ]; then
            rm "$grouped_by_err_output"
            echo "$output" > "$grouped_by_err_output"
            echo "📝 Saved grouped errors by names in: $grouped_by_err_output"
        fi

        if [ -f "$errors_grouped_file" ]; then
            echo ""
            echo ""
        fi
    fi
fi

echo ""
if [ $error_occurred -eq 0 ]; then
    echo "${GREEN}✅ Analysis completed successfully !"
else
    echo "${YELLOW}Check the output for details in: ${NO_COLOR}$error_file"
    echo "${RED}❌ Analysis completed with ${NO_COLOR}$error_count ${YELLOW}(unique ${unique_errors_count}) ${RED}errors in ${NO_COLOR}$total_files ${RED}files.${NO_COLOR}"
fi
echo ""

echo "${NO_COLOR}"

# Удаляем временные файлы
rm -f "$temp_file"
rm -f "$unique_messages_file"
rm -f "$errors_grouped_file"
rm -f "$file_grouped_errors"

exit
