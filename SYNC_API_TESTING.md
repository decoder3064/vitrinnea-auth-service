# User Sync API - Testing Guide

## Endpoint Information

**URL:** `POST http://localhost:8000/api/sync/user`  
**Authentication:** Service-to-service (API Key required)  
**Purpose:** Sync users from main Vitrinnea app to auth microservice

---

## Required Headers

```
X-API-Key: vitrinnea-dev-api-key-2025
X-API-Secret: your-api-secret-here
Content-Type: application/json
```

> **Note:** Get your `X-API-Secret` from `.env` file: `AUTH_API_SECRET`

---

## Test Case 1: Create New User (No External ID)

### Request
```bash
curl -X POST http://localhost:8000/api/sync/user \
  -H "Content-Type: application/json" \
  -H "X-API-Key: vitrinnea-dev-api-key-2025" \
  -H "X-API-Secret: your-secret-here" \
  -d '{
    "name": "John Doe",
    "email": "john@vitrinnea.com",
    "country": "SV",
    "allowed_countries": ["SV", "GT"],
    "role": "employee",
    "user_type": "employee",
    "active": true
  }'
```

### Expected Response (201 Created)
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 123,
      "external_id": null,
      "name": "John Doe",
      "email": "john@vitrinnea.com",
      "country": "SV",
      "allowed_countries": ["SV", "GT"],
      "user_type": "employee",
      "active": true,
      "roles": ["employee"],
      "permissions": []
    },
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "token_type": "bearer",
    "expires_in": 3600
  },
  "message": "User created successfully"
}
```

---

## Test Case 2: Create New User (With External ID)

### Request
```bash
curl -X POST http://localhost:8000/api/sync/user \
  -H "Content-Type: application/json" \
  -H "X-API-Key: vitrinnea-dev-api-key-2025" \
  -H "X-API-Secret: your-secret-here" \
  -d '{
    "external_id": "main-app-user-456",
    "name": "Jane Smith",
    "email": "jane@vitrinnea.com",
    "country": "GT",
    "allowed_countries": ["GT", "SV", "CR"],
    "role": "admin_gt",
    "user_type": "admin",
    "active": true
  }'
```

### Expected Response (201 Created)
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 124,
      "external_id": "main-app-user-456",
      "name": "Jane Smith",
      "email": "jane@vitrinnea.com",
      "country": "GT",
      "allowed_countries": ["GT", "SV", "CR"],
      "user_type": "admin",
      "active": true,
      "roles": ["admin_gt"],
      "permissions": ["view-users", "manage-users"]
    },
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "token_type": "bearer",
    "expires_in": 3600
  },
  "message": "User created successfully"
}
```

---

## Test Case 3: Update Existing User (By Email)

### Request
```bash
curl -X POST http://localhost:8000/api/sync/user \
  -H "Content-Type: application/json" \
  -H "X-API-Key: vitrinnea-dev-api-key-2025" \
  -H "X-API-Secret: your-secret-here" \
  -d '{
    "name": "John Doe Updated",
    "email": "john@vitrinnea.com",
    "country": "GT",
    "allowed_countries": ["SV", "GT", "CR"],
    "role": "admin_sv",
    "user_type": "admin",
    "active": true
  }'
```

### Expected Response (200 OK)
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 123,
      "external_id": null,
      "name": "John Doe Updated",
      "email": "john@vitrinnea.com",
      "country": "GT",
      "allowed_countries": ["SV", "GT", "CR"],
      "user_type": "admin",
      "active": true,
      "roles": ["admin_sv"],
      "permissions": ["view-users", "manage-users"]
    },
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "token_type": "bearer",
    "expires_in": 3600
  },
  "message": "User updated successfully"
}
```

---

## Test Case 4: Update Existing User (By External ID)

### Request
```bash
curl -X POST http://localhost:8000/api/sync/user \
  -H "Content-Type: application/json" \
  -H "X-API-Key: vitrinnea-dev-api-key-2025" \
  -H "X-API-Secret: your-secret-here" \
  -d '{
    "external_id": "main-app-user-456",
    "name": "Jane Smith Updated",
    "email": "jane@vitrinnea.com",
    "country": "CR",
    "allowed_countries": ["GT", "SV", "CR"],
    "role": "super_admin",
    "user_type": "admin",
    "active": true
  }'
```

### Expected Response (200 OK)
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 124,
      "external_id": "main-app-user-456",
      "name": "Jane Smith Updated",
      "email": "jane@vitrinnea.com",
      "country": "CR",
      "allowed_countries": ["GT", "SV", "CR"],
      "user_type": "admin",
      "active": true,
      "roles": ["super_admin"],
      "permissions": ["*"]
    },
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "token_type": "bearer",
    "expires_in": 3600
  },
  "message": "User updated successfully"
}
```

---

## Test Case 5: Deactivate User

### Request
```bash
curl -X POST http://localhost:8000/api/sync/user \
  -H "Content-Type: application/json" \
  -H "X-API-Key: vitrinnea-dev-api-key-2025" \
  -H "X-API-Secret: your-secret-here" \
  -d '{
    "external_id": "main-app-user-456",
    "name": "Jane Smith",
    "email": "jane@vitrinnea.com",
    "country": "CR",
    "active": false
  }'
```

### Expected Response (200 OK)
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 124,
      "external_id": "main-app-user-456",
      "name": "Jane Smith",
      "email": "jane@vitrinnea.com",
      "country": "CR",
      "allowed_countries": ["CR"],
      "user_type": "employee",
      "active": false,
      "roles": [],
      "permissions": []
    },
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "token_type": "bearer",
    "expires_in": 3600
  },
  "message": "User updated successfully"
}
```

---

## Error Cases

### Missing API Key
```bash
curl -X POST http://localhost:8000/api/sync/user \
  -H "Content-Type: application/json" \
  -d '{"name": "Test", "email": "test@test.com", "country": "SV"}'
```

**Response (401 Unauthorized):**
```json
{
  "success": false,
  "message": "Invalid API credentials"
}
```

---

### Invalid Email Format
```bash
curl -X POST http://localhost:8000/api/sync/user \
  -H "Content-Type: application/json" \
  -H "X-API-Key: vitrinnea-dev-api-key-2025" \
  -H "X-API-Secret: your-secret-here" \
  -d '{
    "name": "Test User",
    "email": "not-an-email",
    "country": "SV"
  }'
```

**Response (422 Unprocessable Entity):**
```json
{
  "success": false,
  "errors": {
    "email": [
      "The email field must be a valid email address."
    ]
  }
}
```

---

### Missing Required Fields
```bash
curl -X POST http://localhost:8000/api/sync/user \
  -H "Content-Type: application/json" \
  -H "X-API-Key: vitrinnea-dev-api-key-2025" \
  -H "X-API-Secret: your-secret-here" \
  -d '{
    "name": "Test User"
  }'
```

**Response (422 Unprocessable Entity):**
```json
{
  "success": false,
  "errors": {
    "email": ["The email field is required."],
    "country": ["The country field is required."]
  }
}
```

---

## Using the JWT Token

After receiving the token from sync endpoint, you can use it to make authenticated requests:

```bash
# Test the token by calling /api/auth/me
curl -X GET http://localhost:8000/api/auth/me \
  -H "Authorization: Bearer YOUR_JWT_TOKEN_HERE" \
  -H "X-Country: SV"
```

**Expected Response:**
```json
{
  "success": true,
  "data": {
    "id": 123,
    "name": "John Doe",
    "email": "john@vitrinnea.com",
    "country": "SV",
    "allowed_countries": ["SV", "GT"],
    "user_type": "employee",
    "active": true,
    "roles": ["employee"],
    "permissions": []
  }
}
```

---

## Postman Collection

### Setup
1. Create new collection: "Vitrinnea Auth Sync"
2. Add environment variables:
   - `base_url`: `http://localhost:8000`
   - `api_key`: `vitrinnea-dev-api-key-2025`
   - `api_secret`: `your-secret-from-env`
   - `jwt_token`: (will be auto-populated)

### Pre-request Script (for collection)
```javascript
// No pre-request needed for sync endpoint
```

### Tests Script (to save token)
```javascript
if (pm.response.code === 200 || pm.response.code === 201) {
    var jsonData = pm.response.json();
    if (jsonData.success && jsonData.data.access_token) {
        pm.environment.set("jwt_token", jsonData.data.access_token);
        console.log("Token saved:", jsonData.data.access_token);
    }
}
```

---

## Quick Test Script

Save this as `test-sync-api.sh`:

```bash
#!/bin/bash

BASE_URL="http://localhost:8000"
API_KEY="vitrinnea-dev-api-key-2025"
API_SECRET="your-secret-here"  # Replace with actual secret from .env

echo "🧪 Testing User Sync API"
echo "========================"

echo ""
echo "📝 Test 1: Create new user"
curl -X POST "$BASE_URL/api/sync/user" \
  -H "Content-Type: application/json" \
  -H "X-API-Key: $API_KEY" \
  -H "X-API-Secret: $API_SECRET" \
  -d '{
    "external_id": "test-user-001",
    "name": "Test User",
    "email": "test@vitrinnea.com",
    "country": "SV",
    "allowed_countries": ["SV", "GT"],
    "role": "employee",
    "user_type": "employee",
    "active": true
  }' | jq '.'

echo ""
echo "✅ Test completed!"
```

Make it executable:
```bash
chmod +x test-sync-api.sh
./test-sync-api.sh
```

---

## Integration with Main App

When integrating with main Vitrinnea app:

1. **User logs into Main App**
2. **Main App calls sync endpoint:**
   ```javascript
   const response = await fetch('http://auth-service/api/sync/user', {
     method: 'POST',
     headers: {
       'Content-Type': 'application/json',
       'X-API-Key': process.env.AUTH_API_KEY,
       'X-API-Secret': process.env.AUTH_API_SECRET,
     },
     body: JSON.stringify({
       external_id: user.id,  // Main app user ID
       name: user.name,
       email: user.email,
       country: user.country,
       allowed_countries: user.allowed_countries,
       role: user.role,
       user_type: user.user_type,
       active: user.active,
     }),
   });
   
   const { data } = await response.json();
   const jwt_token = data.access_token;
   ```

3. **Main App passes JWT to frontend:**
   - Via cookie: `Set-Cookie: auth_token=${jwt_token}`
   - Via redirect: `redirect(/service?token=${jwt_token})`
   - Via localStorage: Frontend stores token

4. **Frontend uses JWT for all auth service API calls**

---

## Security Notes

⚠️ **IMPORTANT:**
- Never expose `X-API-Key` or `X-API-Secret` in frontend code
- Only use sync endpoint from backend services
- JWT tokens expire after 60 minutes (configurable)
- Use HTTPS in production
- Rotate API keys regularly

---

## Troubleshooting

**Problem:** 401 Invalid API credentials  
**Solution:** Check `.env` file for correct `AUTH_API_KEY` and `AUTH_API_SECRET`

**Problem:** 422 Validation error  
**Solution:** Verify all required fields are present and correctly formatted

**Problem:** User not updated  
**Solution:** Ensure email or external_id matches existing user

**Problem:** Token doesn't work  
**Solution:** Check token hasn't expired, verify `X-Country` header is sent

---

## Next Steps

After testing:
1. ✅ Verify user created in database
2. ✅ Test JWT token with `/api/auth/me`
3. ✅ Try updating existing user
4. ✅ Test with different roles
5. ✅ Integrate with main app
