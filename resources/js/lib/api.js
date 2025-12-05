import axios from 'axios';

// Use relative API URL since we're in the same Laravel app
const API_URL = '/api';

// Create axios instance
export const api = axios.create({
  baseURL: API_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-API-Key': 'vitrinnea-dev-api-key-2025',
  },
});

// Request interceptor to add token and country to headers
api.interceptors.request.use(
  (config) => {
    const token = getToken();
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    
    // Add X-Country header to all requests
    const country = getCountry();
    if (country) {
      config.headers['X-Country'] = country;
    }
    
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Response interceptor for error handling
api.interceptors.response.use(
  (response) => response,
  async (error) => {
    const originalRequest = error.config;

    // Handle 401 Unauthorized - but don't retry if it's already a refresh request
    if (error.response?.status === 401 && !originalRequest._retry) {
      // If this is a refresh endpoint failing, don't retry - just logout
      if (originalRequest.url?.includes('/auth/refresh')) {
        clearAuth();
        if (typeof window !== 'undefined') {
          window.location.href = '/login';
        }
        return Promise.reject(new Error('Session expired. Please login again.'));
      }

      originalRequest._retry = true;

      try {
        // Try to refresh token
        const newToken = await refreshToken();
        if (newToken && originalRequest.headers) {
          originalRequest.headers.Authorization = `Bearer ${newToken}`;
          return api(originalRequest);
        }
      } catch (refreshError) {
        // Refresh failed, clear auth and redirect to login
        clearAuth();
        if (typeof window !== 'undefined') {
          window.location.href = '/login';
        }
        return Promise.reject(new Error('Session expired. Please login again.'));
      }
    }

    // Sanitize error messages to prevent information disclosure
    let errorMessage = 'An error occurred';
    if (error.response?.status === 403) {
      errorMessage = 'Access denied';
    } else if (error.response?.status === 404) {
      errorMessage = 'Resource not found';
    } else if (error.response?.status === 422) {
      errorMessage = 'Invalid input data';
    } else if (error.response?.status && error.response.status >= 500) {
      errorMessage = 'Server error. Please try again later';
    }
    
    return Promise.reject(new Error(errorMessage));
  }
);

// Token management helpers
export const getToken = () => {
  if (typeof window !== 'undefined') {
    const token = localStorage.getItem('access_token');
    if (token) {
      return token.trim() !== '' ? token : null;
    }
  }
  return null;
};

export const setToken = (token) => {
  if (typeof window !== 'undefined') {
    localStorage.setItem('access_token', token);
  }
};

export const clearAuth = () => {
  if (typeof window !== 'undefined') {
    localStorage.removeItem('access_token');
    localStorage.removeItem('user');
    localStorage.removeItem('selected_country');
  }
};

export const setCountry = (country) => {
  if (typeof window !== 'undefined') {
    localStorage.setItem('selected_country', country.toUpperCase());
  }
};

export const getCountry = () => {
  if (typeof window !== 'undefined') {
    return localStorage.getItem('selected_country');
  }
  return null;
};

export const setUser = (user) => {
  if (typeof window !== 'undefined') {
    localStorage.setItem('user', JSON.stringify(user));
  }
};

export const getUser = () => {
  if (typeof window !== 'undefined') {
    const user = localStorage.getItem('user');
    if (user) {
      try {
        const parsed = JSON.parse(user);
        if (parsed && typeof parsed === 'object' && parsed.id) {
          return parsed;
        }
      } catch (error) {
        localStorage.removeItem('user');
      }
    }
  }
  return null;
};

// Transform backend user response to frontend format
const transformUser = (user) => {
  return {
    ...user,
    roles: Array.isArray(user.roles)
      ? user.roles.map((role) => 
          typeof role === 'string' 
            ? { id: 0, name: role, guard_name: 'api', created_at: '', updated_at: '' }
            : role
        )
      : [],
    permissions: Array.isArray(user.permissions)
      ? user.permissions.map((permission) => 
          typeof permission === 'string'
            ? { id: 0, name: permission, guard_name: 'api', created_at: '', updated_at: '' }
            : permission
        )
      : [],
  };
};

// Auth API methods
export const authApi = {
  login: async (email, password, country) => {
    const response = await api.post('/auth/login', 
      { email, password }, 
      { 
        headers: { 
          'x-country': country.toUpperCase() 
        } 
      }
    );
    if (response.data.success && response.data.user) {
      return {
        success: true,
        data: {
          access_token: response.data.access_token,
          token_type: response.data.token_type || 'bearer',
          expires_in: response.data.expires_in || 3600,
          user: transformUser(response.data.user)
        }
      };
    }
    return response.data;
  },

  logout: async () => {
    await api.post('/auth/logout');
    clearAuth();
  },

  me: async () => {
    const response = await api.get('/auth/me');
    if (response.data.success && response.data.user) {
      return {
        success: true,
        data: transformUser(response.data.user)
      };
    }
    return response.data;
  },

  refresh: async () => {
    const response = await api.post('/auth/refresh');
    if (response.data.success && response.data.user) {
      return {
        success: true,
        data: {
          access_token: response.data.access_token,
          token_type: response.data.token_type || 'bearer',
          expires_in: response.data.expires_in || 3600,
          user: transformUser(response.data.user)
        }
      };
    }
    return response.data;
  },

  verify: async () => {
    const response = await api.post('/auth/verify');
    return response.data;
  },
};

// Refresh token helper
const refreshToken = async () => {
  try {
    const response = await authApi.refresh();
    if (response.success && response.data?.access_token) {
      setToken(response.data.access_token);
      setUser(response.data.user);
      return response.data.access_token;
    }
    return null;
  } catch (error) {
    return null;
  }
};

// User API methods (for admin)
export const userApi = {
  getAll: async (filters) => {
    const params = new URLSearchParams();
    if (filters?.country) params.append('country', filters.country);
    if (filters?.active !== undefined) params.append('active', filters.active ? '1' : '0');
    if (filters?.search) params.append('search', filters.search);
    
    const queryString = params.toString();
    const url = queryString ? `/admin/users?${queryString}` : '/admin/users';
    const response = await api.get(url);
    
    if (response.data.success && response.data.data && response.data.data.data) {
      return {
        success: true,
        data: response.data.data.data,
        pagination: {
          current_page: response.data.data.current_page,
          last_page: response.data.data.last_page,
          per_page: response.data.data.per_page,
          total: response.data.data.total,
        }
      };
    }
    return response.data;
  },

  getById: async (id) => {
    const response = await api.get(`/admin/users/${id}`);
    return response.data;
  },

  create: async (data) => {
    const response = await api.post('/admin/users', data);
    return response.data;
  },

  update: async (id, data) => {
    const response = await api.put(`/admin/users/${id}`, data);
    return response.data;
  },

  delete: async (id) => {
    const response = await api.delete(`/admin/users/${id}`);
    return response.data;
  },

  activate: async (id) => {
    const response = await api.post(`/admin/users/${id}/activate`);
    return response.data;
  },

  assignGroups: async (userId, groupIds) => {
    const response = await api.post(`/admin/users/${userId}/groups`, { groups: groupIds });
    return response.data;
  },

  resetPassword: async (userId) => {
    const response = await api.post(`/admin/users/${userId}/reset-password`);
    return response.data;
  },
};

// Group API methods (for admin)
export const groupApi = {
  getAll: async () => {
    const response = await api.get('/admin/groups');
    return response.data;
  },

  getById: async (id) => {
    const response = await api.get(`/admin/groups/${id}`);
    return response.data;
  },

  create: async (data) => {
    const response = await api.post('/admin/groups', data);
    return response.data;
  },

  update: async (id, data) => {
    const response = await api.put(`/admin/groups/${id}`, data);
    return response.data;
  },

  delete: async (id) => {
    const response = await api.delete(`/admin/groups/${id}`);
    return response.data;
  },

  assignPermissions: async (groupId, permissionIds) => {
    const response = await api.post(`/admin/groups/${groupId}/permissions`, { permissions: permissionIds });
    return response.data;
  },
};

// Keep roleApi for backward compatibility (maps to groups)
export const roleApi = groupApi;

// Permission API methods
export const permissionApi = {
  getAll: async () => {
    return {
      success: true,
      data: []
    };
  },
};

export default api;
