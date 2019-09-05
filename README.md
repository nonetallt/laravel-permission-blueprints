# laravel-permission-blueprints

Create json blueprints for your application roles and permissions to easily update permissions without having to run manual modifications or multiple seeders for your database.
Built to be used with [spatie/laravel-permission](https://github.com/spatie/laravel-permission) laravel package.

## Permissions

Create your permission json blueprint and set the path in config.

```json
[
    "permission-1",
    "permission-2",
    "permission-3",
    "permission-4"
]
```

Update permissions using this command. Old permissions are removed and new ones are added.

```
php artisan permissions:update
```


## Roles

Create your role json blueprint and set the path  in config.

```json
[
    {
        "name": "superadmin",
        "permissions": "all"
    },
    {
        "name": "admin",
        "permissions": [
            "permission-1",
            "permission-2",
            "permission-3"
        ]
    },
    {
        "name": "user",
        "permissions": [
            "permission-1"
        ]
    }
]
```

Update roles using this command. Old roles are removed and new ones are added. Additionally, permissions for each role are updated. This command also updates permissions first to make sure they exist before attempting to attach any permissions for roles.

```
php artisan roles:update
```
