import { User, HostingPlan, Domain, UserRole } from '../types';

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
    TOKEN: 'kp_token'
};

// Initial Data
export const INITIAL_USERS: User[] = [
    { id: 'u1', username: 'demo_user', email: 'user@example.com', role: UserRole.USER, plan: 'Basic', avatar: 'https://picsum.photos/200', status: 'ACTIVE' },
    { id: 'a1', username: 'sys_admin', email: 'admin@kolabpanel.com', role: UserRole.ADMIN, plan: 'Premium', avatar: 'https://picsum.photos/201', status: 'ACTIVE' }
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
