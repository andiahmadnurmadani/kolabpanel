import { User, Site, HostingPlan, Domain, Payment, Framework, FileNode, UserRole, SiteStatus, PaymentStatus, SupportTicket, ChatMessage } from '../types';
import { getMockFiles } from '../constants';

// --- LOCAL STORAGE HELPERS ---
const DELAY = 600; // Simulate network latency (ms)

const DB_KEYS = {
    USERS: 'kp_users',
    PASSWORDS: 'kp_passwords', // Store passwords separately
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
const INITIAL_USERS: User[] = [
    { id: 'u1', username: 'demo_user', email: 'user@example.com', role: UserRole.USER, plan: 'Basic', avatar: 'https://picsum.photos/200', status: 'ACTIVE' },
    { id: 'a1', username: 'sys_admin', email: 'admin@kolabpanel.com', role: UserRole.ADMIN, plan: 'Premium', avatar: 'https://picsum.photos/201', status: 'ACTIVE' }
];

const INITIAL_PASSWORDS: Record<string, string> = {
    'u1': 'password',
    'a1': 'admin'
};

const INITIAL_PLANS: HostingPlan[] = [
    { id: 'plan_basic', name: 'Basic', price: 0, currency: 'Rp', features: ['1 Site', '100MB Storage', 'Shared Database'], limits: { sites: 1, storage: 100, databases: 0 }, isPopular: false },
    { id: 'plan_pro', name: 'Pro', price: 50000, currency: 'Rp', features: ['5 Sites', '1GB Storage', 'Private Database'], limits: { sites: 5, storage: 1024, databases: 1 }, isPopular: true },
    { id: 'plan_premium', name: 'Premium', price: 100000, currency: 'Rp', features: ['Unlimited Sites', '10GB Storage'], limits: { sites: 9999, storage: 10240, databases: 5 }, isPopular: false }
];

const INITIAL_DOMAINS: Domain[] = [
    { id: 'd1', name: 'kolabpanel.com', isPrimary: true }
];

// Helper to simulate async API call
const delay = <T>(data: T): Promise<T> => {
    return new Promise((resolve) => setTimeout(() => resolve(data), DELAY));
};

// Storage Accessors
const getStorage = <T>(key: string, defaultVal: T): T => {
    const stored = localStorage.getItem(key);
    if (!stored) {
        localStorage.setItem(key, JSON.stringify(defaultVal));
        return defaultVal;
    }
    return JSON.parse(stored);
};

const setStorage = (key: string, val: any) => {
    localStorage.setItem(key, JSON.stringify(val));
};

// --- MOCK IMPLEMENTATION ---

export const api = {
  auth: {
    login: async (username: string, password: string): Promise<{user: User, token: string}> => {
        // Simple mock auth
        const users = getStorage<User[]>(DB_KEYS.USERS, INITIAL_USERS);
        const passwords = getStorage<Record<string, string>>(DB_KEYS.PASSWORDS, INITIAL_PASSWORDS);

        const user = users.find(u => u.username === username);
        
        // Verify user exists AND password matches
        if (user && passwords[user.id] === password) {
            const token = `mock_token_${user.id}_${Date.now()}`;
            localStorage.setItem(DB_KEYS.TOKEN, token);
            // Store current user ID in token for "me" call
            localStorage.setItem('kp_current_user_id', user.id);
            return delay({ user, token });
        }
        throw new Error('Invalid credentials');
    },
    me: async (): Promise<User> => {
        const userId = localStorage.getItem('kp_current_user_id');
        const users = getStorage<User[]>(DB_KEYS.USERS, INITIAL_USERS);
        const user = users.find(u => u.id === userId);
        
        if (user) return delay(user);
        throw new Error('Not authorized');
    },
    updateProfile: async (id: string, data: Partial<User>): Promise<User> => {
        const users = getStorage<User[]>(DB_KEYS.USERS, INITIAL_USERS);
        const idx = users.findIndex(u => u.id === id);
        if (idx !== -1) {
            users[idx] = { ...users[idx], ...data };
            setStorage(DB_KEYS.USERS, users);
            return delay(users[idx]);
        }
        throw new Error('User not found');
    },
    changePassword: async (userId: string, current: string, newPass: string) => {
        const passwords = getStorage<Record<string, string>>(DB_KEYS.PASSWORDS, INITIAL_PASSWORDS);
        
        if (passwords[userId] === current) {
            passwords[userId] = newPass;
            setStorage(DB_KEYS.PASSWORDS, passwords);
            return delay({ success: true });
        }
        throw new Error('Current password is incorrect');
    }
  },

  sites: {
    list: async (userId: string): Promise<Site[]> => {
        const sites = getStorage<Site[]>(DB_KEYS.SITES, []); // Default empty
        return delay(sites.filter(s => s.userId === userId));
    },
    deploy: async (formData: FormData): Promise<Site> => {
        // Simulation of deployment
        const userId = formData.get('userId') as string;
        const name = formData.get('name') as string;
        const framework = formData.get('framework') as Framework;
        const subdomain = formData.get('subdomain') as string;
        const needsDatabase = formData.get('needsDatabase') === 'true';
        // We ignore the actual binary file for localStorage, but we generate the tree
        
        const newSite: Site = {
            id: `s_${Date.now()}`,
            userId,
            name,
            framework,
            subdomain: subdomain.split('.')[0], // Store just the prefix
            status: SiteStatus.ACTIVE, // Immediate active for mock
            createdAt: new Date().toISOString().split('T')[0],
            storageUsed: 15, // Initial size mock
            hasDatabase: needsDatabase
        };

        // Save Site
        const sites = getStorage<Site[]>(DB_KEYS.SITES, []);
        sites.push(newSite);
        setStorage(DB_KEYS.SITES, sites);

        // Generate Files (Auto-Extract Simulation)
        const mockFiles = getMockFiles(framework);
        // Map mock files to this site
        const siteFiles = mockFiles.map(f => ({
            ...f,
            siteId: newSite.id // Add siteId to file record
        }));

        // Retrieve existing files registry (flat list)
        // Structure: { siteId, ...FileNode }
        const allFiles = getStorage<any[]>(DB_KEYS.FILES, []);
        const updatedFiles = [...allFiles, ...siteFiles];
        setStorage(DB_KEYS.FILES, updatedFiles);

        return delay(newSite);
    },
    update: async (siteId: string, data: Partial<Site>): Promise<Site> => {
        const sites = getStorage<Site[]>(DB_KEYS.SITES, []);
        const idx = sites.findIndex(s => s.id === siteId);
        if (idx !== -1) {
            sites[idx] = { ...sites[idx], ...data };
            setStorage(DB_KEYS.SITES, sites);
            return delay(sites[idx]);
        }
        throw new Error('Site not found');
    },
    create: async (data: any) => Promise.reject("Use deploy"),
    delete: async (siteId: string) => {
        let sites = getStorage<Site[]>(DB_KEYS.SITES, []);
        sites = sites.filter(s => s.id !== siteId);
        setStorage(DB_KEYS.SITES, sites);

        // Delete associated files
        let files = getStorage<any[]>(DB_KEYS.FILES, []);
        files = files.filter(f => f.siteId !== siteId);
        setStorage(DB_KEYS.FILES, files);

        return delay({ success: true });
    }
  },

  files: {
      list: async (siteId: string, path: string = '/'): Promise<FileNode[]> => {
          const allFiles = getStorage<any[]>(DB_KEYS.FILES, []);
          // Filter by site and direct children of path
          const files = allFiles.filter(f => f.siteId === siteId && f.path === path);
          return delay(files);
      },
      createFolder: async (siteId: string, path: string, folderName: string) => {
          const allFiles = getStorage<any[]>(DB_KEYS.FILES, []);
          const newFolder = {
              id: `d_${Date.now()}`,
              siteId,
              name: folderName,
              type: 'folder',
              size: '-',
              path: path,
              createdAt: new Date().toISOString()
          };
          allFiles.push(newFolder);
          setStorage(DB_KEYS.FILES, allFiles);
          return delay({ success: true });
      },
      upload: async (siteId: string, path: string, file: File) => {
           const allFiles = getStorage<any[]>(DB_KEYS.FILES, []);
           const newFile = {
              id: `f_${Date.now()}`,
              siteId,
              name: file.name,
              type: 'file',
              size: (file.size / 1024).toFixed(2) + ' KB',
              path: path,
              createdAt: new Date().toISOString()
           };
           allFiles.push(newFile);
           setStorage(DB_KEYS.FILES, allFiles);
           return delay({ success: true });
      },
      delete: async (siteId: string, path: string, name: string) => {
          let allFiles = getStorage<any[]>(DB_KEYS.FILES, []);
          // Remove the specific item
          allFiles = allFiles.filter(f => !(f.siteId === siteId && f.path === path && f.name === name));
          
          // Basic recursive delete simulation (if folder, remove children)
          // Ideally check if type is folder, then regex match path starts with path/name
          const folderPath = path === '/' ? `/${name}` : `${path}/${name}`;
          allFiles = allFiles.filter(f => !(f.siteId === siteId && f.path.startsWith(folderPath)));

          setStorage(DB_KEYS.FILES, allFiles);
          return delay({ success: true });
      },
      rename: async (siteId: string, path: string, oldName: string, newName: string) => {
          const allFiles = getStorage<any[]>(DB_KEYS.FILES, []);
          const file = allFiles.find(f => f.siteId === siteId && f.path === path && f.name === oldName);
          if (file) {
              file.name = newName;
              setStorage(DB_KEYS.FILES, allFiles);
          }
          return delay({ success: true });
      }
  },

  tickets: {
      create: async (userId: string, username: string, subject: string): Promise<SupportTicket> => {
          const tickets = getStorage<SupportTicket[]>(DB_KEYS.TICKETS, []);
          const newTicket: SupportTicket = {
              id: `t_${Date.now()}`,
              userId,
              username,
              subject,
              status: 'OPEN',
              createdAt: new Date().toISOString(),
              lastMessageAt: new Date().toISOString()
          };
          tickets.unshift(newTicket);
          setStorage(DB_KEYS.TICKETS, tickets);
          return delay(newTicket);
      },
      list: async (userId?: string): Promise<SupportTicket[]> => {
          const tickets = getStorage<SupportTicket[]>(DB_KEYS.TICKETS, []);
          if (userId) {
              return delay(tickets.filter(t => t.userId === userId));
          }
          return delay(tickets); // Admin sees all
      },
      getMessages: async (ticketId: string): Promise<ChatMessage[]> => {
          const allMessages = getStorage<ChatMessage[]>(DB_KEYS.MESSAGES, []);
          return delay(allMessages.filter(m => m.ticketId === ticketId));
      },
      sendMessage: async (ticketId: string, senderId: string, senderName: string, text: string, isAdmin: boolean): Promise<ChatMessage> => {
          const messages = getStorage<ChatMessage[]>(DB_KEYS.MESSAGES, []);
          const newMessage: ChatMessage = {
              id: `m_${Date.now()}`,
              ticketId,
              senderId,
              senderName,
              text,
              isAdmin,
              timestamp: new Date().toISOString()
          };
          messages.push(newMessage);
          setStorage(DB_KEYS.MESSAGES, messages);

          // Update ticket timestamp
          const tickets = getStorage<SupportTicket[]>(DB_KEYS.TICKETS, []);
          const ticket = tickets.find(t => t.id === ticketId);
          if (ticket) {
              ticket.lastMessageAt = newMessage.timestamp;
              setStorage(DB_KEYS.TICKETS, tickets);
          }

          return delay(newMessage);
      },
      close: async (ticketId: string) => {
          const tickets = getStorage<SupportTicket[]>(DB_KEYS.TICKETS, []);
          const ticket = tickets.find(t => t.id === ticketId);
          if (ticket) {
              ticket.status = 'CLOSED';
              setStorage(DB_KEYS.TICKETS, tickets);
          }
          return delay({success: true});
      }
  },

  admin: {
    getStats: async () => {
       const users = getStorage(DB_KEYS.USERS, INITIAL_USERS);
       const sites = getStorage(DB_KEYS.SITES, []);
       return delay({
           totalUsers: users.length,
           totalSites: sites.length,
           activeRevenue: '0' // Static for now
       });
    },
    getUsers: async (): Promise<User[]> => {
       return delay(getStorage(DB_KEYS.USERS, INITIAL_USERS));
    },
    toggleUserStatus: async (userId: string) => {
        const users = getStorage<User[]>(DB_KEYS.USERS, INITIAL_USERS);
        const user = users.find(u => u.id === userId);
        if (user) {
            user.status = user.status === 'ACTIVE' ? 'SUSPENDED' : 'ACTIVE';
            setStorage(DB_KEYS.USERS, users);
        }
        return delay(user);
    },
    getPayments: async (): Promise<Payment[]> => {
        return delay(getStorage(DB_KEYS.PAYMENTS, []));
    },
    verifyPayment: async (id: string, status: PaymentStatus) => {
        const payments = getStorage<Payment[]>(DB_KEYS.PAYMENTS, []);
        const payment = payments.find(p => p.id === id);
        if (payment) {
            payment.status = status;
            setStorage(DB_KEYS.PAYMENTS, payments);
            
            // Upgrade user plan if verified
            if (status === PaymentStatus.VERIFIED) {
                const users = getStorage<User[]>(DB_KEYS.USERS, INITIAL_USERS);
                const user = users.find(u => u.id === payment.userId);
                if (user) {
                    user.plan = payment.plan;
                    setStorage(DB_KEYS.USERS, users);
                }
            }
        }
        return delay(payment);
    },
    addDomain: async (name: string): Promise<Domain> => {
        const domains = getStorage<Domain[]>(DB_KEYS.DOMAINS, INITIAL_DOMAINS);
        const newDomain = { id: `d_${Date.now()}`, name, isPrimary: false };
        domains.push(newDomain);
        setStorage(DB_KEYS.DOMAINS, domains);
        return delay(newDomain);
    },
    updateDomain: async (id: string, name: string) => {
        const domains = getStorage<Domain[]>(DB_KEYS.DOMAINS, INITIAL_DOMAINS);
        const d = domains.find(x => x.id === id);
        if (d) {
            d.name = name;
            setStorage(DB_KEYS.DOMAINS, domains);
        }
        return delay(d);
    },
    deleteDomain: async (id: string) => {
        let domains = getStorage<Domain[]>(DB_KEYS.DOMAINS, INITIAL_DOMAINS);
        domains = domains.filter(d => d.id !== id);
        setStorage(DB_KEYS.DOMAINS, domains);
        return delay({ success: true });
    },
    createPlan: async (plan: Partial<HostingPlan>) => {
        const plans = getStorage<HostingPlan[]>(DB_KEYS.PLANS, INITIAL_PLANS);
        const newPlan = { ...plan, id: `p_${Date.now()}` } as HostingPlan;
        plans.push(newPlan);
        setStorage(DB_KEYS.PLANS, plans);
        return delay(newPlan);
    },
    updatePlan: async (id: string, plan: Partial<HostingPlan>) => {
        const plans = getStorage<HostingPlan[]>(DB_KEYS.PLANS, INITIAL_PLANS);
        const idx = plans.findIndex(p => p.id === id);
        if (idx !== -1) {
            plans[idx] = { ...plans[idx], ...plan };
            setStorage(DB_KEYS.PLANS, plans);
        }
        return delay(plans[idx]);
    },
    deletePlan: async (id: string) => {
        let plans = getStorage<HostingPlan[]>(DB_KEYS.PLANS, INITIAL_PLANS);
        plans = plans.filter(p => p.id !== id);
        setStorage(DB_KEYS.PLANS, plans);
        return delay({ success: true });
    }
  },

  common: {
    getPlans: async (): Promise<HostingPlan[]> => {
        return delay(getStorage(DB_KEYS.PLANS, INITIAL_PLANS));
    },
    getDomains: async (): Promise<Domain[]> => {
        return delay(getStorage(DB_KEYS.DOMAINS, INITIAL_DOMAINS));
    }
  }
};
