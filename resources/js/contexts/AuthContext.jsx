import React, { createContext, useContext, useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import toast from 'react-hot-toast';
import { authApi, setToken, setUser, clearAuth, getToken, getUser, getCountry, setCountry } from '../lib/api.js';

const AuthContext = createContext(null);

export function AuthProvider({ children }) {
  const [user, setUserState] = useState(null);
  const [selectedCountry, setSelectedCountry] = useState(null);
  const [loading, setLoading] = useState(true);
  const navigate = useNavigate();

  useEffect(() => {
    const initAuth = async () => {
      const token = getToken();
      const storedUser = getUser();
      const storedCountry = getCountry();

      if (storedCountry) {
        setSelectedCountry(storedCountry);
      }

      if (token && storedUser) {
        setUserState(storedUser);
        try {
          const response = await authApi.me();
          if (response.success && response.data) {
            setUserState(response.data);
            setUser(response.data);
          } else {
            clearAuth();
            setUserState(null);
            setSelectedCountry(null);
          }
        } catch (error) {
          clearAuth();
          setUserState(null);
          setSelectedCountry(null);
        }
      }
      setLoading(false);
    };

    initAuth();
  }, []);

  const login = async (email, password, country) => {
    try {
      setLoading(true);
      const response = await authApi.login(email, password, country);

      if (response.success && response.data) {
        setToken(response.data.access_token);
        setUser(response.data.user);
        setCountry(country);
        
        setUserState(response.data.user);
        setSelectedCountry(country);
        
        setLoading(false);
        toast.success('Login successful!');
        navigate('/profile');
      } else {
        toast.error('Login failed. Please try again.');
        setLoading(false);
      }
    } catch (error) {
      const errorMessage = 'Login failed. Please check your credentials and country access.';
      toast.error(errorMessage);
      setLoading(false);
      throw error;
    }
  };

  const logout = async () => {
    try {
      await authApi.logout();
      clearAuth();
      setUserState(null);
      setSelectedCountry(null);
      toast.success('Logged out successfully');
      navigate('/login');
    } catch (error) {
      clearAuth();
      setUserState(null);
      setSelectedCountry(null);
      navigate('/login');
    }
  };

  const refreshUser = async () => {
    try {
      const response = await authApi.me();
      if (response.success && response.data) {
        setUserState(response.data);
        setUser(response.data);
      }
    } catch (error) {
      console.error('Failed to refresh user:', error);
    }
  };

  const changeCountry = async (country) => {
    if (!user?.allowed_countries?.includes(country.toUpperCase())) {
      toast.error('You do not have access to this country');
      return;
    }
    setCountry(country);
    setSelectedCountry(country);
    toast.success(`Switched to ${country}`);
    await refreshUser();
  };

  const hasRole = (role) => {
    if (!user || !user.roles) return false;
    
    const roles = Array.isArray(role) ? role : [role];
    return user.roles.some(r => {
      const roleName = typeof r === 'string' ? r : r.name;
      return roles.includes(roleName);
    });
  };

  const hasPermission = (permission) => {
    if (!user || !user.permissions) return false;
    
    const permissions = Array.isArray(permission) ? permission : [permission];
    return user.permissions.some(p => {
      const permName = typeof p === 'string' ? p : p.name;
      return permissions.includes(permName);
    });
  };

  const value = {
    user,
    loading,
    selectedCountry,
    login,
    logout,
    refreshUser,
    changeCountry,
    isAuthenticated: !!user,
    hasRole,
    hasPermission,
  };

  return (
    <AuthContext.Provider value={value}>
      {children}
    </AuthContext.Provider>
  );
}

export function useAuth() {
  const context = useContext(AuthContext);
  if (!context) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
}
