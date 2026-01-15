import { User, HostingPlan, Domain, UserRole, DiscountCode, TunnelRoute } from '../types';

// --- LOCAL STORAGE HELPERS ---
export const DELAY = 600; // Simulate network latency (ms)

export const DB_KEYS = {
    USERS: 'kp_users',
    PASSWORDS: 'kp_passwords',
    SITES: 'kp_sites',
    FILES: 'kp_files',
    PLANS: 'kp_plans',
    DOMAINS: 'kp_domains',
    PAYMENTS: 'kp_payments',
    TICKETS: 'kp_tickets',
    MESSAGES: 'kp_messages',
    DISCOUNTS: 'kp_discounts',
    TOKEN: 'kp_token',
    TUNNELS: 'kp_tunnels' // Added for persistence
};

// Calculate dates for demo
const futureDate = new Date();
futureDate.setDate(futureDate.getDate() + 365); // 1 year
const nearExpiryDate = new Date();
nearExpiryDate.setDate(nearExpiryDate.getDate() + 5); // 5 days (Trigger Warning)

// Initial Data
export const INITIAL_USERS: User[] = [
    { 
        id: 'u1', 
        username: 'demo_user', 
        email: 'user@example.com', 
        role: UserRole.USER, 
        plan: 'Basic', 
        avatar: 'https://picsum.photos/200', 
        status: 'ACTIVE',
        planExpiresAt: nearExpiryDate.toISOString() // Set to expire in 5 days for demo
    },
    { 
        id: 'a1', 
        username: 'sys_admin', 
        email: 'admin@kolabpanel.com', 
        role: UserRole.ADMIN, 
        plan: 'Premium', 
        avatar: 'https://picsum.photos/201', 
        status: 'ACTIVE',
        planExpiresAt: futureDate.toISOString()
    }
];

export const INITIAL_PASSWORDS: Record<string, string> = {
    'u1': 'password',
    'a1': 'admin'
};

export const INITIAL_PLANS: HostingPlan[] = [
    { id: 'plan_basic', name: 'Basic', price: 0, currency: 'Rp', features: ['1 Site', '100MB Storage', 'Shared Database'], limits: { sites: 1, storage: 100, databases: 0 }, isPopular: false },
    { id: 'plan_pro', name: 'Pro', price: 50000, currency: 'Rp', features: ['5 Sites', '1GB Storage', 'Private Database'], limits: { sites: 5, storage: 1024, databases: 1 }, isPopular: true },
    { id: 'plan_premium', name: 'Premium', price: 100000, currency: 'Rp', features: ['Unlimited Sites', '10GB Storage'], limits: { sites: 9999, storage: 10240, databases: 5 }, isPopular: false }
];

export const INITIAL_DOMAINS: Domain[] = [
    { id: 'd1', name: 'kolabpanel.com', isPrimary: true }
];

export const INITIAL_TUNNELS: TunnelRoute[] = [
    { hostname: 'api.kolabpanel.com', service: 'http://127.0.0.1:5000' },
    { hostname: 'demo.kolabpanel.com', service: 'http://127.0.0.1:3000' },
    { hostname: 'manager.kolab.top', service: 'http://127.0.0.1:9058' },
    { hostname: 'db.kolabpanel.com', service: 'http://127.0.0.1:8080' }
];

// Helper to simulate async API call
export const delay = <T>(data: T): Promise<T> => {
    return new Promise((resolve) => setTimeout(() => resolve(data), DELAY));
};

// Storage Accessors
export const getStorage = <T>(key: string, defaultVal: T): T => {
    const stored = localStorage.getItem(key);
    if (!stored) {
        localStorage.setItem(key, JSON.stringify(defaultVal));
        return defaultVal;
    }
    return JSON.parse(stored);
};

export const setStorage = (key: string, val: any) => {
    localStorage.setItem(key, JSON.stringify(val));
};