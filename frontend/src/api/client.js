const BASE_URL = '/api/v1';

export function getAuthToken() {
  return localStorage.getItem('eward_access_token') || localStorage.getItem('eward_token') || sessionStorage.getItem('eward_token') || '';
}

export function getRefreshToken() {
  return localStorage.getItem('eward_refresh_token') || sessionStorage.getItem('eward_refresh_token') || '';
}

export function setAuthTokens(accessToken, refreshToken = null) {
  if (!accessToken) return;
  localStorage.setItem('eward_access_token', accessToken);
  localStorage.setItem('eward_token', accessToken);
  sessionStorage.setItem('eward_token', accessToken);
  if (refreshToken) {
    localStorage.setItem('eward_refresh_token', refreshToken);
    sessionStorage.setItem('eward_refresh_token', refreshToken);
  }
}

export function setAuthToken(token) {
  setAuthTokens(token, token);
}

export function clearAuthToken() {
  localStorage.removeItem('eward_access_token');
  localStorage.removeItem('eward_refresh_token');
  localStorage.removeItem('eward_token');
  sessionStorage.removeItem('eward_token');
  sessionStorage.removeItem('eward_refresh_token');
  localStorage.removeItem('eward_user');
  sessionStorage.removeItem('eward_user');
}

export function getStoredUser() {
  try {
    const raw = localStorage.getItem('eward_user') || sessionStorage.getItem('eward_user');
    return raw ? JSON.parse(raw) : null;
  } catch (_) {
    return null;
  }
}

export function setStoredUser(user) {
  if (!user) return;
  const str = typeof user === 'string' ? user : JSON.stringify(user);
  localStorage.setItem('eward_user', str);
  sessionStorage.setItem('eward_user', str);
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
