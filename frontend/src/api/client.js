const BASE_URL = '/api/v1';
const ACCESS_TOKEN_KEY = 'eward_access_token';
const REFRESH_TOKEN_KEY = 'eward_refresh_token';
const USER_KEY = 'eward_user';

export function getAuthToken() {
  return localStorage.getItem(ACCESS_TOKEN_KEY) || localStorage.getItem('eward_token') || '';
}

export function getRefreshToken() {
  return localStorage.getItem(REFRESH_TOKEN_KEY) || '';
}

export function setAuthTokens(accessToken, refreshToken = null) {
  if (!accessToken) return;
  localStorage.setItem(ACCESS_TOKEN_KEY, accessToken);
  localStorage.removeItem('eward_token');
  sessionStorage.removeItem('eward_token');
  if (refreshToken) {
    localStorage.setItem(REFRESH_TOKEN_KEY, refreshToken);
    sessionStorage.removeItem('eward_refresh_token');
  }
}

export function setAuthToken(token) {
  setAuthTokens(token, token);
}

export function clearAuthToken() {
  localStorage.removeItem(ACCESS_TOKEN_KEY);
  localStorage.removeItem(REFRESH_TOKEN_KEY);
  localStorage.removeItem('eward_token');
  sessionStorage.removeItem('eward_token');
  sessionStorage.removeItem('eward_refresh_token');
  localStorage.removeItem(USER_KEY);
  sessionStorage.removeItem(USER_KEY);
}

export function getStoredUser() {
  try {
    const raw = localStorage.getItem(USER_KEY);
    return raw ? JSON.parse(raw) : null;
  } catch (_) {
    return null;
  }
}

export function setStoredUser(user) {
  if (!user) return;
  const str = typeof user === 'string' ? user : JSON.stringify(user);
  localStorage.setItem(USER_KEY, str);
  sessionStorage.removeItem(USER_KEY);
}

let isRefreshing = false;

async function tryRefreshToken() {
  const refreshToken = getRefreshToken() || getAuthToken();
  if (!refreshToken || isRefreshing) return null;
  isRefreshing = true;
  try {
    const res = await fetch(`${BASE_URL}/auth/refresh`, {
      method: 'POST',
      headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ refresh_token: refreshToken }),
    });
    if (!res.ok) throw new Error('Refresh failed');
    const data = await res.json();
    const newAccessToken = data.data?.access_token || data.access_token;
    const newRefreshToken = data.data?.refresh_token || data.refresh_token || newAccessToken;
    if (newAccessToken) {
      setAuthTokens(newAccessToken, newRefreshToken);
      if (data.data?.user) setStoredUser(data.data.user);
      return newAccessToken;
    }
  } catch (_) {
    clearAuthToken();
    window.dispatchEvent(new CustomEvent('auth:change', { detail: null }));
  } finally {
    isRefreshing = false;
  }
  return null;
}

export async function request(endpoint, options = {}) {
  const url = endpoint.startsWith('http') ? endpoint : `${BASE_URL}${endpoint}`;
  let token = getAuthToken();

  const headers = {
    Accept: 'application/json',
    'X-Request-Id': `req-${Date.now()}-${Math.random().toString(36).substring(2, 7)}`,
    ...options.headers,
  };

  if (token && !headers.Authorization) {
    headers.Authorization = `Bearer ${token}`;
  }

  if (options.body && !(options.body instanceof FormData) && typeof options.body === 'object') {
    headers['Content-Type'] = 'application/json';
    options.body = JSON.stringify(options.body);
  }

  try {
    let response = await fetch(url, { ...options, headers });

    if (response.status === 401 && !endpoint.includes('/auth/login') && !endpoint.includes('/auth/refresh') && token) {
      const newToken = await tryRefreshToken();
      if (newToken) {
        headers.Authorization = `Bearer ${newToken}`;
        response = await fetch(url, { ...options, headers });
      }
    }

    const contentType = response.headers.get('content-type') || '';
    const isJson = contentType.includes('application/json');
    const payload = isJson ? await response.json() : await response.text();

    if (!response.ok) {
      if (response.status === 401 && !endpoint.includes('/auth/login')) {
        clearAuthToken();
        window.dispatchEvent(new CustomEvent('auth:change', { detail: null }));
      }
      const errorMsg = (typeof payload === 'object' && payload.message)
        ? payload.message
        : `Lỗi kết nối máy chủ (${response.status})`;
      const error = new Error(errorMsg);
      error.status = response.status;
      error.payload = payload;
      throw error;
    }

    return payload;
  } catch (err) {
    console.error(`API Error [${endpoint}]:`, err);
    throw err;
  }
}

export const api = {
  get: (url, options) => request(url, { method: 'GET', ...options }),
  post: (url, body, options) => request(url, { method: 'POST', body, ...options }),
  put: (url, body, options) => request(url, { method: 'PUT', body, ...options }),
  patch: (url, body, options) => request(url, { method: 'PATCH', body, ...options }),
  delete: (url, options) => request(url, { method: 'DELETE', ...options }),
};
