# Blog sitesi sunucu API;

## Çalıştırmak için;

- Proje klasörüne gidin:
  `cd blog_app_backend/`
  
- Composer gerekliliklerini indirin:
   `composer install`
 
- DB yi düzenleyin (*):
   `php artisan migrate` 
  
- Testleri koşmak için:
  `php artisan test`
 
- Sunucuyu ayağa kaldırmak için(*):
  `php artisan serve`

  sunucu çalışırken [http://127.0.0.1:8000] adresinde çalışacaktır.

(*) php versiyonuna göre php.ini dosyasındaki aşağıdaki satarılar comment out yapılabilir;
    -extension=pdo_sqlite
    -extension=sqlite3
    -extension=fileinfo
