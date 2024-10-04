php artisan l5-swagger:generate

# Kiểm tra xem lệnh có thành công không
if [ $? -eq 0 ]; then
    echo "Buid Swagger thành công"
else
    echo "Failed to generate Swagger documentation."
fi