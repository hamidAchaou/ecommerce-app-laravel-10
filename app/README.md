# autorization Spatie

1. create Projects
```bash
composer create-project "laravel/laravel:^10.0" spatieDashboard-app
```

2. install spatie :
```bash
composer require spatie/laravel-permission
```

🟦 4. 🧭 الموديولات الرئيسية في لوحة التحكم
الموديول	الوصف
Utilisateurs	إدارة المستخدمين، الأدوار، الصلاحيات
Produits	CRUD المنتجات
Catégories	تصنيفات المنتجات
Commandes	إدارة الطلبات
Clients	العملاء
Statistiques	Dashboard للأرقام والإحصائيات
Paiements	تتبع المدفوعات
Paramètres	إعدادات الموقع أو المتجر

php artisan vendor:publish --tag=laravel-pagination
