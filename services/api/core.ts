
import { delay, getStorage, INITIAL_USERS, DB_KEYS, INITIAL_PASSWORDS } from '../mockData';

export const API_URL = 'http://localhost:5000/api';
export const TUNNEL_API_URL = 'https://cloudflare.kolab.top'; 
export const APACHE_API_URL = 'https://api-apache.kolab.top';

export let isBackendOffline = false;

export const setBackendOffline = (status: boolean) => {
    isBackendOffline = status;
};

export const getAuthHeaders = () => {
  const token = localStorage.getItem('kp_token');
  const headers: Record<string, string> = { 'Content-Type': 'application/json' };
  if (token) headers['Authorization'] = `Bearer ${token}`;
  return headers;
};

export const getAuthHeadersMultipart = () => {
  const token = localStorage.getItem('kp_token');
  const headers: Record<string, string> = {};
  if (token) headers['Authorization'] = `Bearer ${token}`;
  return headers;
};

export const handleResponse = async (res: Response) => {
  if (!res.ok) {
    let errorMessage = res.statusText || 'An error occurred';
    try {
        const error = await res.json();
        if (error && error.message) errorMessage = error.message;
        if (error && error.error) errorMessage = error.error; // Cloudflare API often returns { error: "msg" }
    } catch (e) {
        // Response was not JSON, keep statusText
    }
    throw new Error(errorMessage);
  }
  return res.json();
};

export async function fetchWithMockFallback<T>(
    fetchFn: () => Promise<T>, 
    mockFn: () => Promise<T>
): Promise<T> {
    if (isBackendOffline) return mockFn();

    try {
        return await fetchFn();
    } catch (error: any) {
        // Fallback conditions
        const isNetworkError = error.message.includes('Failed to fetch') || 
                               error.message.includes('NetworkError') || 
                               error.name === 'TypeError';
                               
        const isServerError = error.message === 'An error occurred' || 
                              error.message === 'Not Found' ||
                              error.message === 'Internal Server Error' ||
                              error.message.includes('JSON'); 

        if (isNetworkError || isServerError) {
            if (!isBackendOffline) {
                console.warn(`Backend unreachable or erroring (${error.message}). Switching to client-side mock data.`);
                setBackendOffline(true);
            }
            return mockFn();
        }
        throw error;
    }
}
