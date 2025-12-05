# Seeders del Microservicio de Autenticación

Este documento explica cómo usar los seeders para poblar la base de datos con datos de ejemplo.

## 📋 Contenido

Los seeders crean:
- ✅ **24 Roles** con sus permisos correspondientes
- ✅ **67+ Permisos** específicos del sistema
- ✅ **14 Usuarios de prueba** con diferentes roles y países
- ✅ **8 Grupos** organizacionales

## 🚀 Cómo Ejecutar los Seeders

### Opción 1: Fresh Migration + Seed (Recomendado para desarrollo)

```bash
# ⚠️ ADVERTENCIA: Esto borrará TODA la información de la base de datos
php artisan migrate:fresh --seed
```

### Opción 2: Solo Seeders (Sin borrar datos)

```bash
php artisan db:seed
```

### Opción 3: Seeder Específico

```bash
# Solo roles y permisos
php artisan db:seed --class=RolePermissionSeeder

# Solo grupos
php artisan db:seed --class=GroupSeeder
```

## 👥 Usuarios de Prueba Creados

### 🔑 ADMINISTRADORES
| Email | Rol | Países Autorizados |
|-------|-----|-------------------|
| admin@vitrinnea.com | Admin | SV, GT, CR, HN, NI, PA (Todos) |
| programador@vitrinnea.com | Programadores | SV, GT, CR, HN, NI, PA (Todos) |
| administracion@vitrinnea.com | Administracion | SV, GT, CR |

### 👥 OPERACIONES
| Email | Rol | Países |
|-------|-----|--------|
| operaciones@vitrinnea.com | Operaciones | SV, GT |
| gestor@vitrinnea.com | Gestores | SV |
| despacho@vitrinnea.com | Despacho | SV |

### 💼 VENTAS Y SERVICIO
| Email | Rol | Países |
|-------|-----|--------|
| vendedor@vitrinnea.com | Vendedor | SV |
| atencion@vitrinnea.com | AtencionCliente | SV, GT |
| cajero@vitrinnea.com | Cajero | SV |

### 📊 SOPORTE
| Email | Rol | Países |
|-------|-----|--------|
| contabilidad@vitrinnea.com | Contabilidad | SV, GT, CR |
| marketing@vitrinnea.com | Marketing | SV, GT, CR |

### 🌎 ESPECÍFICOS POR PAÍS
| Email | Rol | País |
|-------|-----|------|
| admin.gt@vitrinnea.com | Administracion | GT |
| admin.cr@vitrinnea.com | Administracion | CR |

### 👤 USUARIO BÁSICO
| Email | Rol | País |
|-------|-----|------|
| user@vitrinnea.com | User | SV |

**Contraseña para todos:** `password`

## 🎯 Roles Disponibles

### Roles Administrativos
- **Admin** - Acceso completo a todo el sistema
- **Programadores** - Acceso completo (desarrollo)
- **Administracion** - Gestión administrativa completa

### Roles Operativos
- **Operaciones** - Gestión de operaciones y logística
- **Gestores** - Gestión de operaciones básicas
- **GestorDF** - Gestor de dropshipping/fulfillment
- **Despacho** - Gestión de envíos
- **Procesamiento** - Procesamiento de pedidos

### Roles de Ventas
- **Vendedor** - Ventas básicas
- **VendedorTienda** - Ventas en tienda física
- **Store** - Gestión de tienda
- **Cupones** - Gestión de cupones

### Roles de Soporte
- **AtencionCliente** - Atención al cliente
- **Contabilidad** - Contabilidad y finanzas
- **Data** - Análisis de datos
- **Marketing** - Marketing y promociones
- **Influencer** - Influencers con acceso limitado

### Roles Especializados
- **Transferencias** - Transferencias entre bodegas
- **Trasladar** - Traslado de inventario
- **Fotografia** - Gestión de fotografía de productos
- **Digitacion** - Digitación de productos
- **Motorista** - Motoristas de entrega
- **Cajero** - Operaciones de caja

### Rol Básico
- **User** - Usuario básico con permisos mínimos

## 📦 Grupos Organizacionales

- **admin** - Administradores
- **customer_service** - Atención al Cliente
- **it** - Tecnología y Programadores
- **operations** - Operaciones y Logística
- **sales** - Ventas
- **warehouse** - Bodega
- **finance** - Finanzas
- **marketing** - Marketing

## 🔐 Permisos Creados

### Pedidos (Orders)
- `view_orders`, `create_orders`, `edit_orders`, `delete_orders`

### Inventario
- `view_inventory`, `edit_inventory`, `transfer_inventory`

### Usuarios
- `view_users`, `create_users`, `edit_users`, `delete_users`

### Bodega
- `view_warehouse`, `manage_warehouse`

### Reportes
- `view_reports`, `export_reports`

### Configuración
- `manage_settings`, `manage_roles`

### Finanzas
- `view_financials`, `manage_transfers`

### Contenido
- `manage_photography`, `manage_marketing`

### Atención al Cliente
- `manage_customer_service`

### Operaciones de Tienda
- `manage_store`, `manage_cashier`, `manage_dispatch`

## 🔄 Resetear la Base de Datos

Si necesitas resetear completamente la base de datos:

```bash
# ⚠️ ADVERTENCIA: Esto borrará TODO
php artisan migrate:fresh --seed
```

## 📝 Notas Importantes

1. **Contraseña de Prueba**: Todos los usuarios tienen la contraseña `password`
2. **Países**: Los usuarios tienen diferentes combinaciones de países autorizados
3. **Roles Spatie**: Se usa Spatie Permission para gestión de roles y permisos
4. **Guard**: Todos los roles y permisos usan el guard `api`
5. **Grupos**: Los usuarios se asignan automáticamente a grupos según su rol

## 🧪 Probar Diferentes Escenarios

### Escenario 1: Admin con todos los países
```
Email: admin@vitrinnea.com
Password: password
```

### Escenario 2: Usuario limitado a un país
```
Email: vendedor@vitrinnea.com
Password: password
```

### Escenario 3: Usuario multi-país
```
Email: operaciones@vitrinnea.com
Password: password
```

### Escenario 4: Usuario básico
```
Email: user@vitrinnea.com
Password: password
```

## 🔧 Gestión de Roles (Comando Artisan)

### Asignar un rol a un usuario

```bash
# Asignar rol Admin
php artisan user:role admin@vitrinnea.com Admin

# Asignar rol y eliminar todos los demás (sync)
php artisan user:role service.sv@vitrinnea.com Admin --sync

# Remover un rol
php artisan user:role user@vitrinnea.com Admin --remove
```

### Ejemplos Comunes

```bash
# Cambiar usuario de AtencionCliente a Admin
php artisan user:role atencion@vitrinnea.com Admin --sync

# Agregar rol adicional (sin eliminar los existentes)
php artisan user:role admin@vitrinnea.com Programadores

# Ver roles disponibles (el comando los muestra si el rol no existe)
php artisan user:role test@test.com RolInvalido
```

## 🔧 Personalizar Seeders

Para agregar más usuarios de prueba, edita:
```
database/seeders/RolePermissionSeeder.php
```

Busca el array `$testUsers` y agrega nuevos usuarios siguiendo el formato:

```php
[
    'name' => 'Nombre del Usuario',
    'email' => 'email@vitrinnea.com',
    'password' => Hash::make('password'),
    'user_type' => 'employee',
    'country' => 'SV',
    'allowed_countries' => ['SV', 'GT'],
    'active' => true,
    'role' => 'NombreDelRol',
],
```

## 🐛 Troubleshooting

### Error: "Class 'Spatie\Permission\Models\Role' not found"
```bash
php artisan config:clear
php artisan cache:clear
composer dump-autoload
```

### Error: "SQLSTATE[23000]: Integrity constraint violation"
La tabla ya tiene datos. Usa `migrate:fresh --seed` o elimina los datos manualmente.

### Los roles no se están aplicando
```bash
php artisan permission:cache-reset
```
