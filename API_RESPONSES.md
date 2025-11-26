# Vitrinnea Auth API - Successful Response Examples

## Authentication Endpoints

**IMPORTANT:** All authentication endpoints require the following headers:
- `X-API-Key`: Your API key for service authentication
- `X-API-Secret`: Your API secret for service authentication

### POST /api/auth/login
**Headers:**
```
X-API-Key: your-api-key-here
X-API-Secret: your-api-secret-here
Content-Type: application/json
```

**Request:**
```json
{
  "email": "admin@vitrinnea.com",
  "password": "password"
}
```

**Response (200):**
```json
{
  "success": true,
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "token_type": "bearer",
  "expires_in": 3600,
  "user": {
    "id": 1,
    "name": "Super Admin",
    "email": "admin@vitrinnea.com",
    "country": "SV",
    "user_type": "employee",
    "active": true,
    "created_at": "2025-11-09T00:00:00.000000Z",
    "updated_at": "2025-11-18T22:00:00.000000Z",
    "roles": [
      {
        "id": 1,
        "name": "super_admin",
        "guard_name": "api"
      }
    ]
  }
}
```

---

### POST /api/auth/register
**Headers:**
```
X-API-Key: your-api-key-here
X-API-Secret: your-api-secret-here
Content-Type: application/json
```

**Request:**
```json
{
  "name": "New Employee",
  "email": "newemployee@vitrinnea.com",
  "password": "Password123!",
  "password_confirmation": "Password123!",
  "user_type": "employee",
  "country": "SV",
  "role": "employee"
}
```

**Validation Rules:**
- `name`: required, string, max 255 characters
- `email`: required, email, unique, must be @vitrinnea.com domain
- `password`: required, min 8 characters, must be confirmed
- `user_type`: optional, values: "employee" or "admin" (default: "employee")
- `country`: optional, values: "SV" or "GT" (default: "SV")
- `role`: optional, any valid role name (default: "employee")

**Response (201):**
```json
{
  "success": true,
  "data": {
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "token_type": "bearer",
    "expires_in": 3600,
    "user": {
      "id": 6,
      "name": "New Employee",
      "email": "newemployee@vitrinnea.com",
      "user_type": "employee",
      "country": "SV",
      "roles": ["employee"],
      "permissions": [],
      "groups": []
    }
  },
  "message": "Registration successful"
}
```

**Error Response (422 - Validation Failed):**
```json
{
  "success": false,
  "errors": {
    "email": [
      "The email has already been taken."
    ]
  }
}
```

---

### POST /api/auth/logout
**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
  "success": true,
  "message": "Successfully logged out"
}
```

### POST /api/auth/refresh
**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
  "success": true,
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "token_type": "bearer",
  "expires_in": 3600,
  "user": {
    "id": 1,
    "name": "Super Admin",
    "email": "admin@vitrinnea.com",
    "country": "SV",
    "user_type": "employee",
    "active": true,
    "created_at": "2025-11-09T00:00:00.000000Z",
    "updated_at": "2025-11-18T22:00:00.000000Z"
  }
}
```

### GET /api/auth/me
**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
  "success": true,
  "user": {
    "id": 1,
    "name": "Super Admin",
    "email": "admin@vitrinnea.com",
    "country": "SV",
    "user_type": "employee",
    "active": true,
    "created_at": "2025-11-09T00:00:00.000000Z",
    "updated_at": "2025-11-18T22:00:00.000000Z",
    "roles": [
      {
        "id": 1,
        "name": "super_admin",
        "guard_name": "api"
      }
    ]
  }
}
```

### POST /api/auth/verify
**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
  "success": true,
  "message": "Token is valid"
}
```

---

## User Management Endpoints (Admin Only)

### GET /api/admin/users
**Headers:** `Authorization: Bearer {token}`

**Query Parameters (optional):**
- `country` (SV or GT)
- `active` (true/false)
- `search` (searches name and email)
- `page` (pagination)

**Response (200):**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "name": "Super Admin",
        "email": "admin@vitrinnea.com",
        "country": "SV",
        "user_type": "employee",
        "active": true,
        "created_at": "2025-11-09T00:00:00.000000Z",
        "updated_at": "2025-11-18T22:00:00.000000Z",
        "roles": [
          {
            "id": 1,
            "name": "super_admin",
            "guard_name": "api"
          }
        ],
        "groups": []
      },
      {
        "id": 2,
        "name": "Admin SV",
        "email": "admin.sv@vitrinnea.com",
        "country": "SV",
        "user_type": "employee",
        "active": true,
        "created_at": "2025-11-09T00:00:00.000000Z",
        "updated_at": "2025-11-09T00:00:00.000000Z",
        "roles": [
          {
            "id": 2,
            "name": "admin_sv",
            "guard_name": "api"
          }
        ],
        "groups": []
      }
    ],
    "first_page_url": "http://localhost:8000/api/admin/users?page=1",
    "from": 1,
    "last_page": 1,
    "last_page_url": "http://localhost:8000/api/admin/users?page=1",
    "links": [
      {
        "url": null,
        "label": "&laquo; Previous",
        "active": false
      },
      {
        "url": "http://localhost:8000/api/admin/users?page=1",
        "label": "1",
        "active": true
      },
      {
        "url": null,
        "label": "Next &raquo;",
        "active": false
      }
    ],
    "next_page_url": null,
    "path": "http://localhost:8000/api/admin/users",
    "per_page": 15,
    "prev_page_url": null,
    "to": 5,
    "total": 5
  }
}
```

### GET /api/admin/users/{id}
**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Super Admin",
    "email": "admin@vitrinnea.com",
    "country": "SV",
    "user_type": "employee",
    "active": true,
    "created_at": "2025-11-09T00:00:00.000000Z",
    "updated_at": "2025-11-18T22:00:00.000000Z",
    "roles": [
      {
        "id": 1,
        "name": "super_admin",
        "guard_name": "api"
      }
    ],
    "groups": [
      {
        "id": 1,
        "name": "admin",
        "display_name": "Administrators",
        "description": "System administrators with full access",
        "active": true
      }
    ]
  }
}
```

### POST /api/admin/users
**Headers:** `Authorization: Bearer {token}`

**Request:**
```json
{
  "name": "John Doe",
  "email": "john.doe@vitrinnea.com",
  "country": "SV",
  "user_type": "employee",
  "role": "employee",
  "groups": [1, 2],
  "send_welcome_email": true
}
```

**Response (201):**
```json
{
  "success": true,
  "data": {
    "id": 6,
    "name": "John Doe",
    "email": "john.doe@vitrinnea.com",
    "country": "SV",
    "user_type": "employee",
    "active": true,
    "created_at": "2025-11-18T22:30:00.000000Z",
    "updated_at": "2025-11-18T22:30:00.000000Z",
    "roles": [
      {
        "id": 8,
        "name": "employee",
        "guard_name": "api"
      }
    ],
    "groups": [
      {
        "id": 1,
        "name": "admin",
        "display_name": "Administrators"
      },
      {
        "id": 2,
        "name": "customer_service",
        "display_name": "Customer Service"
      }
    ]
  },
  "message": "User created successfully. Temporary password has been sent via email."
}
```

### PUT /api/admin/users/{id}
**Headers:** `Authorization: Bearer {token}`

**Request:**
```json
{
  "name": "John Doe Updated",
  "email": "john.updated@vitrinnea.com",
  "country": "GT",
  "user_type": "employee",
  "active": true,
  "role": "operations"
}
```

**Response (200):**
```json
{
  "success": true,
  "data": {
    "id": 6,
    "name": "John Doe Updated",
    "email": "john.updated@vitrinnea.com",
    "country": "GT",
    "user_type": "employee",
    "active": true,
    "created_at": "2025-11-18T22:30:00.000000Z",
    "updated_at": "2025-11-18T22:35:00.000000Z",
    "roles": [
      {
        "id": 7,
        "name": "operations",
        "guard_name": "api"
      }
    ],
    "groups": []
  },
  "message": "User updated successfully"
}
```

### DELETE /api/admin/users/{id}
**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
  "success": true,
  "message": "User deactivated successfully"
}
```

### POST /api/admin/users/{id}/groups
**Headers:** `Authorization: Bearer {token}`

**Request:**
```json
{
  "groups": [1, 2, 3]
}
```

**Response (200):**
```json
{
  "success": true,
  "data": {
    "id": 2,
    "name": "Admin SV",
    "email": "admin.sv@vitrinnea.com",
    "groups": [
      {
        "id": 1,
        "name": "admin",
        "display_name": "Administrators",
        "description": "System administrators with full access",
        "active": true,
        "created_at": "2025-11-17T02:00:00.000000Z",
        "updated_at": "2025-11-17T02:00:00.000000Z",
        "pivot": {
          "user_id": 2,
          "group_id": 1
        }
      },
      {
        "id": 2,
        "name": "customer_service",
        "display_name": "Customer Service",
        "description": "Customer service representatives",
        "active": true,
        "created_at": "2025-11-17T02:00:00.000000Z",
        "updated_at": "2025-11-17T02:00:00.000000Z",
        "pivot": {
          "user_id": 2,
          "group_id": 2
        }
      }
    ]
  },
  "message": "Groups assigned successfully"
}
```

### POST /api/admin/users/{id}/reset-password
**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
  "success": true,
  "message": "Password reset successfully. New temporary password has been sent via email."
}
```

---

## Group Management Endpoints (Admin Only)

### GET /api/admin/groups
**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "admin",
      "display_name": "Administrators",
      "description": "System administrators with full access",
      "active": true,
      "created_at": "2025-11-17T02:00:00.000000Z",
      "updated_at": "2025-11-17T02:00:00.000000Z"
    },
    {
      "id": 2,
      "name": "customer_service",
      "display_name": "Customer Service",
      "description": "Customer service representatives",
      "active": true,
      "created_at": "2025-11-17T02:00:00.000000Z",
      "updated_at": "2025-11-17T02:00:00.000000Z"
    },
    {
      "id": 3,
      "name": "it",
      "display_name": "IT Department",
      "description": "IT support and technical staff",
      "active": true,
      "created_at": "2025-11-17T02:00:00.000000Z",
      "updated_at": "2025-11-17T02:00:00.000000Z"
    }
  ]
}
```

### GET /api/admin/groups/{id}
**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "admin",
    "display_name": "Administrators",
    "description": "System administrators with full access",
    "active": true,
    "created_at": "2025-11-17T02:00:00.000000Z",
    "updated_at": "2025-11-17T02:00:00.000000Z"
  }
}
```

### POST /api/admin/groups
**Headers:** `Authorization: Bearer {token}`

**Request:**
```json
{
  "name": "warehouse",
  "display_name": "Warehouse Team",
  "description": "Warehouse and logistics staff",
  "active": true
}
```

**Response (201):**
```json
{
  "success": true,
  "data": {
    "id": 4,
    "name": "warehouse",
    "display_name": "Warehouse Team",
    "description": "Warehouse and logistics staff",
    "active": true,
    "created_at": "2025-11-18T22:40:00.000000Z",
    "updated_at": "2025-11-18T22:40:00.000000Z"
  },
  "message": "Group created successfully"
}
```

### PUT /api/admin/groups/{id}
**Headers:** `Authorization: Bearer {token}`

**Request:**
```json
{
  "display_name": "IT Support Team",
  "description": "IT support, technical staff, and developers",
  "active": true
}
```

**Response (200):**
```json
{
  "success": true,
  "data": {
    "id": 3,
    "name": "it",
    "display_name": "IT Support Team",
    "description": "IT support, technical staff, and developers",
    "active": true,
    "created_at": "2025-11-17T02:00:00.000000Z",
    "updated_at": "2025-11-18T22:45:00.000000Z"
  },
  "message": "Group updated successfully"
}
```

### DELETE /api/admin/groups/{id}
**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
  "success": true,
  "message": "Group deactivated successfully"
}
```

---

## Health Check Endpoint

### GET /api/health
**No authentication required**

**Response (200):**
```json
{
  "status": "ok",
  "timestamp": "2025-11-18T22:50:00.000000Z"
}
```

---

## Available Roles
- `super_admin` - Full system access
- `admin_sv` - Administrator for El Salvador
- `admin_gt` - Administrator for Guatemala
- `warehouse_manager_sv` - Warehouse manager for El Salvador
- `warehouse_manager_gt` - Warehouse manager for Guatemala
- `operations` - Operations staff
- `employee` - Regular employee

## Available Permissions
- `view_orders`, `create_orders`, `edit_orders`, `delete_orders`
- `view_inventory`, `create_inventory`, `edit_inventory`, `delete_inventory`
- `view_users`, `create_users`, `edit_users`, `delete_users`
- `view_warehouse`, `edit_warehouse`
- `view_reports`, `export_reports`
- `manage_settings`, `view_settings`

## Error Response Format
All error responses follow this structure:

```json
{
  "success": false,
  "message": "Error message here",
  "errors": {
    "field_name": [
      "Validation error message"
    ]
  }
}
```

## Common HTTP Status Codes
- `200` - Success
- `201` - Created
- `401` - Unauthorized (invalid/missing token)
- `403` - Forbidden (insufficient permissions)
- `404` - Not Found
- `422` - Validation Error
- `500` - Server Error
