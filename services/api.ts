import { User, Site, HostingPlan, Domain, Payment, Framework, FileNode, SiteStatus, PaymentStatus, SupportTicket, ChatMessage, TunnelRoute, DiscountCode } from '../types';
import { getMockFiles } from '../constants';
import { 
    DB_KEYS, 
    INITIAL_USERS, 
    INITIAL_PASSWORDS, 
    INITIAL_PLANS, 
    INITIAL_DOMAINS, 
    INITIAL_TUNNELS,
    getStorage, 
    setStorage, 
    delay 
} from './mockData';

const API_URL = 'http://localhost:5000/api';
const TUNNEL_API_URL = 'https://cloudflare.kolab.top';
const APACHE_API_URL = 'https://api-apache.kolab.top';

// --- HYBRID IMPLEMENTATION ---
// Some features (Auth, Sites, Billing) use LocalStorage for Client-Side Demo
// Admin Config features (Tunnels, Apache) use Real External APIs or Fallback Mocks

export const api = {
  auth: {
    login: async (username: string, password: string): Promise<{user: User, token: string}> => {
        const users = getStorage<User[]>(DB_KEYS.USERS, INITIAL_USERS);
        const passwords = getStorage<Record<string, string>>(DB_KEYS.PASSWORDS, INITIAL_PASSWORDS);

        const user = users.find(u => u.username === username);
        
        if (user && passwords[user.id] === password) {
            const token = `mock_token_${user.id}_${Date.now()}`;
            localStorage.setItem(DB_KEYS.TOKEN, token);
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
        const sites = getStorage<Site[]>(DB_KEYS.SITES, []);
        return delay(sites.filter(s => s.userId === userId));
    },
    deploy: async (formData: FormData): Promise<Site> => {
        const userId = formData.get('userId') as string;
        const name = formData.get('name') as string;
        const framework = formData.get('framework') as Framework;
        const subdomain = formData.get('subdomain') as string;
        
        let needsDatabase = formData.get('needsDatabase') === 'true';
        const attachedDatabaseId = formData.get('attachedDatabaseId') as string;

        let sites = getStorage<Site[]>(DB_KEYS.SITES, []);

        if (attachedDatabaseId) {
             sites = sites.filter(s => s.id !== attachedDatabaseId);
             needsDatabase = true;
        }

        const newSite: Site = {
            id: `s_${Date.now()}`,
            userId,
            name,
            framework,
            subdomain: subdomain.split('.')[0],
            status: SiteStatus.ACTIVE,
            createdAt: new Date().toISOString().split('T')[0],
            storageUsed: 15,
            hasDatabase: needsDatabase
        };

        sites.push(newSite);
        setStorage(DB_KEYS.SITES, sites);

        const mockFiles = getMockFiles(framework);
        const siteFiles = mockFiles.map(f => ({ ...f, siteId: newSite.id }));

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
    delete: async (siteId: string, deleteDb: boolean = true) => {
        let sites = getStorage<Site[]>(DB_KEYS.SITES, []);
        
        if (deleteDb) {
            sites = sites.filter(s => s.id !== siteId);
        } else {
            sites = sites.map(s => {
                if (s.id === siteId) {
                    return { ...s, status: SiteStatus.DB_ONLY, storageUsed: 0 };
                }
                return s;
            });
        }
        
        setStorage(DB_KEYS.SITES, sites);

        let files = getStorage<any[]>(DB_KEYS.FILES, []);
        files = files.filter(f => f.siteId !== siteId);
        setStorage(DB_KEYS.FILES, files);

        return delay({ success: true });
    }
  },

  files: {
      list: async (siteId: string, path: string = '/'): Promise<FileNode[]> => {
          const allFiles = getStorage<any[]>(DB_KEYS.FILES, []);
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
          allFiles = allFiles.filter(f => !(f.siteId === siteId && f.path === path && f.name === name));
          
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
      },
      getContent: async (siteId: string, path: string, name: string): Promise<string> => {
          // Simulation: Return mock content based on extension
          let content = "";
          if (name.endsWith('.env')) {
              content = `APP_NAME=KolabApp\nAPP_ENV=production\nAPP_KEY=base64:randomkey123\n\nDB_CONNECTION=mysql\nDB_HOST=127.0.0.1\nDB_PORT=3306\nDB_DATABASE=db_user1\nDB_USERNAME=sql_user1\nDB_PASSWORD=secret`;
          } else if (name.endsWith('.json')) {
              content = `{\n  "name": "my-project",\n  "version": "1.0.0",\n  "private": true,\n  "scripts": {\n    "start": "node index.js"\n  }\n}`;
          } else if (name.endsWith('.js') || name.endsWith('.ts') || name.endsWith('.tsx')) {
              content = `import React from 'react';\n\nexport const App = () => {\n  return <h1>Hello World</h1>;\n};`;
          } else if (name.endsWith('.html')) {
              content = `<!DOCTYPE html>\n<html>\n<head>\n  <title>My Site</title>\n</head>\n<body>\n  <h1>Welcome</h1>\n</body>\n</html>`;
          } else if (name.endsWith('.css')) {
              content = `body {\n  background-color: #f0f0f0;\n  font-family: sans-serif;\n}`;
          } else {
              content = "File content is not available in preview mode.";
          }
          return delay(content);
      },
      saveContent: async (siteId: string, path: string, name: string, content: string): Promise<boolean> => {
          // In a real app, this would write to the filesystem
          // Here we just simulate a success delay
          return delay(true);
      }
  },

  billing: {
    submitPayment: async (userId: string, username: string, planName: string, amount: number, method: 'BANK' | 'QR', proofFile: File): Promise<Payment> => {
        const payments = getStorage<Payment[]>(DB_KEYS.PAYMENTS, []);
        const newPayment: Payment = {
            id: `pay_${Date.now()}`,
            userId,
            username,
            amount,
            plan: planName,
            method,
            status: PaymentStatus.PENDING,
            date: new Date().toISOString().split('T')[0],
            proofUrl: 'mock_proof_url.jpg'
        };
        payments.unshift(newPayment);
        setStorage(DB_KEYS.PAYMENTS, payments);
        return delay(newPayment);
    },
    getHistory: async (userId: string): Promise<Payment[]> => {
        const payments = getStorage<Payment[]>(DB_KEYS.PAYMENTS, []);
        return delay(payments.filter(p => p.userId === userId));
    },
    validateCoupon: async (code: string): Promise<DiscountCode> => {
        const discounts = getStorage<DiscountCode[]>(DB_KEYS.DISCOUNTS, []);
        const found = discounts.find(d => d.code === code && d.status === 'ACTIVE');
        if (found) return delay(found);
        throw new Error('Invalid or expired coupon');
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
          return delay(tickets);
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
       const payments = getStorage<Payment[]>(DB_KEYS.PAYMENTS, []);
       
       const totalRevenue = payments
           .filter(p => p.status === PaymentStatus.VERIFIED)
           .reduce((sum, p) => sum + p.amount, 0);

       let formattedRevenue = totalRevenue.toLocaleString('id-ID');
       if (totalRevenue > 1000000) {
           formattedRevenue = (totalRevenue / 1000000).toFixed(1) + 'M';
       }

       // FETCH REAL TUNNEL COUNT
       let totalTunnels = 4; // Default/Fallback
       try {
           const res = await fetch(`${TUNNEL_API_URL}/routes`);
           if (res.ok) {
               const routes = await res.json();
               if (Array.isArray(routes)) {
                   totalTunnels = routes.length;
               }
           }
       } catch (e) {
           console.warn("Failed to fetch real tunnel count, using fallback.");
       }

       return delay({
           totalUsers: users.length,
           totalSites: sites.length,
           activeRevenue: formattedRevenue,
           totalTunnels: totalTunnels,
           totalApacheSites: 3
       });
    },
    getRevenueAnalytics: async () => {
        const payments = getStorage<Payment[]>(DB_KEYS.PAYMENTS, []);
        const verifiedPayments = payments.filter(p => p.status === PaymentStatus.VERIFIED);
        
        // Generate last 7 days
        const last7Days = [];
        for (let i = 6; i >= 0; i--) {
            const d = new Date();
            d.setDate(d.getDate() - i);
            const dateStr = d.toISOString().split('T')[0];
            const dayName = d.toLocaleDateString('en-US', { weekday: 'short' });
            
            last7Days.push({
                date: dateStr,
                name: dayName,
                income: 0
            });
        }

        verifiedPayments.forEach(p => {
            const day = last7Days.find(d => d.date === p.date);
            if (day) {
                day.income += p.amount;
            }
        });

        return delay(last7Days);
    },
    getTunnelAnalytics: async (limit: number = 5) => {
        try {
            const res = await fetch(`${TUNNEL_API_URL}/analytics/top-hosts?limit=${limit}`);
            if (!res.ok) throw new Error("Analytics API Error");
            const result = await res.json();
            return { data: result.data };
        } catch (e) {
            console.warn("Using LocalStorage fallback for Analytics", e);
            // Return mock data suitable for list and chart
            return delay({ data: [
                { host: 'api.kolabpanel.com', visits: 1250, rank: 1 },
                { host: 'demo.kolabpanel.com', visits: 850, rank: 2 },
                { host: 'admin.kolabpanel.com', visits: 400, rank: 3 },
            ]});
        }
    },
    getUsers: async (): Promise<User[]> => {
       const users = getStorage(DB_KEYS.USERS, INITIAL_USERS);
       const now = new Date();
       let hasUpdates = false;

       // CRON JOB SIMULATION: Check for expired plans
       // If expired, Auto-Suspend and Redirect Tunnels
       const tunnels = getStorage<TunnelRoute[]>(DB_KEYS.TUNNELS, INITIAL_TUNNELS);
       const sites = getStorage<Site[]>(DB_KEYS.SITES, []);

       users.forEach(user => {
           if (user.status === 'ACTIVE' && user.planExpiresAt) {
               const expires = new Date(user.planExpiresAt);
               if (now > expires) {
                   // PLAN EXPIRED: Auto Suspend
                   user.status = 'SUSPENDED';
                   hasUpdates = true;

                   // Trigger Tunnel Redirect Logic
                   const userSites = sites.filter(s => s.userId === user.id);
                   userSites.forEach(site => {
                        const likelyHostname = `${site.subdomain}.kolabpanel.com`;
                        const tunnelIndex = tunnels.findIndex(t => t.hostname === likelyHostname);
                        if (tunnelIndex !== -1) {
                            tunnels[tunnelIndex].service = 'http://127.0.0.1:80'; // Redirect to Maintenance
                        }
                   });
               }
           }
       });

       if (hasUpdates) {
           setStorage(DB_KEYS.USERS, users);
           setStorage(DB_KEYS.TUNNELS, tunnels);
       }

       return delay(users);
    },
    createUser: async (data: any): Promise<User> => {
        const users = getStorage<User[]>(DB_KEYS.USERS, INITIAL_USERS);
        
        // Basic Validation
        if (users.find(u => u.username === data.username)) throw new Error('Username already exists');
        if (users.find(u => u.email === data.email)) throw new Error('Email already exists');

        const newUser: User = {
            id: `u_${Date.now()}`,
            username: data.username,
            email: data.email,
            role: data.role,
            plan: data.plan,
            avatar: `https://ui-avatars.com/api/?name=${data.username}&background=random`,
            status: 'ACTIVE',
            // Default 30 days expiry, or null if logic requires
            planExpiresAt: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString()
        };

        users.push(newUser);
        setStorage(DB_KEYS.USERS, users);

        // Store Password
        const passwords = getStorage<Record<string, string>>(DB_KEYS.PASSWORDS, INITIAL_PASSWORDS);
        passwords[newUser.id] = data.password;
        setStorage(DB_KEYS.PASSWORDS, passwords);

        return delay(newUser);
    },
    deleteUser: async (userId: string) => {
        // 1. Remove User
        let users = getStorage<User[]>(DB_KEYS.USERS, INITIAL_USERS);
        users = users.filter(u => u.id !== userId);
        setStorage(DB_KEYS.USERS, users);

        // 2. Remove Password
        const passwords = getStorage<Record<string, string>>(DB_KEYS.PASSWORDS, INITIAL_PASSWORDS);
        if (passwords[userId]) {
            delete passwords[userId];
            setStorage(DB_KEYS.PASSWORDS, passwords);
        }

        // 3. Cleanup Sites owned by user
        let sites = getStorage<Site[]>(DB_KEYS.SITES, []);
        const userSites = sites.filter(s => s.userId === userId);
        sites = sites.filter(s => s.userId !== userId);
        setStorage(DB_KEYS.SITES, sites);

        // 4. (Optional) Cleanup Files - For simple mock we can skip deep file deletion
        // but let's clear the index references
        let files = getStorage<FileNode[]>(DB_KEYS.FILES, []);
        const userSiteIds = userSites.map(s => s.id);
        files = files.filter(f => !userSiteIds.includes(f.siteId || '')); // Assuming FileNode has siteId
        setStorage(DB_KEYS.FILES, files);

        return delay({ success: true });
    },
    toggleUserStatus: async (userId: string) => {
        const users = getStorage<User[]>(DB_KEYS.USERS, INITIAL_USERS);
        const user = users.find(u => u.id === userId);
        if (user) {
            const newStatus = user.status === 'ACTIVE' ? 'SUSPENDED' : 'ACTIVE';
            user.status = newStatus;
            setStorage(DB_KEYS.USERS, users);

            // AUTOMATED TUNNEL PORT SWITCHING
            // If user is suspended, redirect their tunnels to port 80 (default/suspend page)
            if (newStatus === 'SUSPENDED') {
                const sites = getStorage<Site[]>(DB_KEYS.SITES, []);
                const userSites = sites.filter(s => s.userId === userId);
                const tunnels = getStorage<TunnelRoute[]>(DB_KEYS.TUNNELS, INITIAL_TUNNELS);
                let tunnelsChanged = false;

                userSites.forEach(site => {
                    // Try to find a tunnel that matches the site's subdomain
                    // We assume domain is kolabpanel.com for the demo match, 
                    // or check if tunnel hostname includes the subdomain.
                    const likelyHostname = `${site.subdomain}.kolabpanel.com`;
                    
                    const tunnelIndex = tunnels.findIndex(t => t.hostname === likelyHostname);
                    
                    if (tunnelIndex !== -1) {
                        tunnels[tunnelIndex].service = 'http://127.0.0.1:80'; // Redirect to Apache Default/Suspend Page
                        tunnelsChanged = true;
                    }
                });

                if (tunnelsChanged) {
                    setStorage(DB_KEYS.TUNNELS, tunnels);
                }
            }
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
            
            if (status === PaymentStatus.VERIFIED) {
                const users = getStorage<User[]>(DB_KEYS.USERS, INITIAL_USERS);
                const user = users.find(u => u.id === payment.userId);
                if (user) {
                    user.plan = payment.plan;
                    // Extend plan by 30 days
                    const currentExpiry = user.planExpiresAt ? new Date(user.planExpiresAt) : new Date();
                    const now = new Date();
                    const baseDate = currentExpiry > now ? currentExpiry : now;
                    baseDate.setDate(baseDate.getDate() + 30);
                    user.planExpiresAt = baseDate.toISOString();
                    
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
    updateDomain: async (id: string, data: Partial<Domain>) => {
        const domains = getStorage<Domain[]>(DB_KEYS.DOMAINS, INITIAL_DOMAINS);
        
        // Ensure only one domain is Primary if the update sets isPrimary to true
        if (data.isPrimary) {
            domains.forEach(d => {
                if (d.id !== id) d.isPrimary = false;
            });
        }

        const d = domains.find(x => x.id === id);
        if (d) {
            Object.assign(d, data);
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
    },
    discounts: {
        list: async (): Promise<DiscountCode[]> => {
            return delay(getStorage(DB_KEYS.DISCOUNTS, []));
        },
        create: async (code: string, type: 'PERCENT' | 'FIXED', value: number, validPlans: string[] = []): Promise<DiscountCode> => {
            const discounts = getStorage<DiscountCode[]>(DB_KEYS.DISCOUNTS, []);
            const newDiscount: DiscountCode = {
                id: `disc_${Date.now()}`,
                code: code.toUpperCase(),
                type,
                value,
                status: 'ACTIVE',
                validPlans,
                createdAt: new Date().toISOString()
            };
            discounts.push(newDiscount);
            setStorage(DB_KEYS.DISCOUNTS, discounts);
            return delay(newDiscount);
        },
        delete: async (id: string) => {
            let discounts = getStorage<DiscountCode[]>(DB_KEYS.DISCOUNTS, []);
            discounts = discounts.filter(d => d.id !== id);
            setStorage(DB_KEYS.DISCOUNTS, discounts);
            return delay({ success: true });
        }
    },
    // CLOUDFLARE TUNNEL INTEGRATION
    tunnels: {
        list: async (): Promise<TunnelRoute[]> => {
            try {
                // Try fetching from real API first
                const res = await fetch(`${TUNNEL_API_URL}/routes`);
                if (!res.ok) throw new Error();
                return await res.json();
            } catch (e) {
                // Fallback to LocalStorage persistence for demo if network fails
                console.warn("Using LocalStorage fallback for Tunnels List");
                return delay(getStorage(DB_KEYS.TUNNELS, INITIAL_TUNNELS));
            }
        },
        create: async (hostname: string, service: string) => {
            let res;
            try {
                // POST to Real API
                res = await fetch(`${TUNNEL_API_URL}/routes`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ hostname, service })
                });
            } catch (e) {
                // Network Error -> Fallback
                console.warn("Using LocalStorage fallback for Create Tunnel", e);
                const tunnels = getStorage<TunnelRoute[]>(DB_KEYS.TUNNELS, INITIAL_TUNNELS);
                tunnels.push({ hostname, service });
                setStorage(DB_KEYS.TUNNELS, tunnels);
                return delay(true);
            }

            // Server reachable but might have returned error (e.g. 409 Conflict)
            if (!res.ok) {
                let errorMessage = 'Failed to create route';
                try {
                    const err = await res.json();
                    errorMessage = err.error || errorMessage;
                } catch { /* parse failed */ }
                throw new Error(errorMessage);
            }
            return true;
        },
        delete: async (hostname: string) => {
             let res;
             try {
                res = await fetch(`${TUNNEL_API_URL}/routes`, {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ hostname })
                });
             } catch (e) {
                 // Network Error -> Fallback
                 let tunnels = getStorage<TunnelRoute[]>(DB_KEYS.TUNNELS, INITIAL_TUNNELS);
                 tunnels = tunnels.filter(t => t.hostname !== hostname);
                 setStorage(DB_KEYS.TUNNELS, tunnels);
                 return delay(true);
             }

             if (!res.ok) {
                 let errorMessage = 'Failed to delete route';
                 try { const err = await res.json(); errorMessage = err.error || errorMessage; } catch {}
                 throw new Error(errorMessage);
             }
             return true;
        },
        edit: async (oldHostname: string, newHostname: string, service: string) => {
             let res;
             try {
                res = await fetch(`${TUNNEL_API_URL}/routes/edit`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ hostname: oldHostname, newHostname, service })
                });
             } catch (e) {
                 // Network Error -> Fallback
                 const tunnels = getStorage<TunnelRoute[]>(DB_KEYS.TUNNELS, INITIAL_TUNNELS);
                 const idx = tunnels.findIndex(t => t.hostname === oldHostname);
                 if (idx !== -1) {
                     tunnels[idx] = { hostname: newHostname, service };
                     setStorage(DB_KEYS.TUNNELS, tunnels);
                 }
                 return delay(true);
             }

             if (!res.ok) {
                 let errorMessage = 'Failed to edit route';
                 try { const err = await res.json(); errorMessage = err.error || errorMessage; } catch {}
                 throw new Error(errorMessage);
             }
             return true;
        }
    },
    // APACHE CONFIG MANAGER
    apache: {
        listSites: async (): Promise<string[]> => {
            try {
                const res = await fetch(`${APACHE_API_URL}/sites`);
                if (!res.ok) throw new Error();
                return res.json();
            } catch (e) {
                return delay(['000-default.conf', 'api-server.conf', 'apache-manager.conf']);
            }
        },
        getSite: async (name: string): Promise<{content: string}> => {
             try {
                 const res = await fetch(`${APACHE_API_URL}/sites/${name}`);
                 if (!res.ok) throw new Error();
                 return res.json();
             } catch (e) {
                 return delay({ content: '# Error fetching content' });
             }
        },
        createSite: async (filename: string, content: string) => {
            try {
                const res = await fetch(`${APACHE_API_URL}/sites`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ filename, content })
                });
                if (!res.ok) {
                    const err = await res.json();
                    throw new Error(err.error || 'Failed to create site');
                }
                return await res.json();
            } catch (e: any) {
                throw new Error(e.message);
            }
        },
        updateSite: async (name: string, content: string) => {
             try {
                const res = await fetch(`${APACHE_API_URL}/sites/${name}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ content })
                });
                if (!res.ok) {
                    const err = await res.json();
                    throw new Error(err.error || 'Failed to update site');
                }
                return await res.json();
            } catch (e: any) {
                throw new Error(e.message);
            }
        },
        deleteSite: async (name: string) => {
            try {
                const res = await fetch(`${APACHE_API_URL}/sites/${name}`, {
                    method: 'DELETE'
                });
                if (!res.ok) {
                    const err = await res.json();
                    throw new Error(err.error || 'Failed to delete site');
                }
                return await res.json();
            } catch (e: any) {
                throw new Error(e.message);
            }
        },
        getHttpd: async (): Promise<{content: string}> => {
            try {
                const res = await fetch(`${APACHE_API_URL}/httpd`);
                if (!res.ok) throw new Error();
                return res.json();
            } catch (e) {
                return delay({ content: '# Fallback httpd.conf\nListen 80' });
            }
        },
        updateHttpd: async (content: string) => {
             try {
                const res = await fetch(`${APACHE_API_URL}/httpd`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ content })
                });
                if (!res.ok) {
                    const err = await res.json();
                    throw new Error(err.error || 'Failed to update httpd.conf');
                }
                return await res.json();
            } catch (e: any) {
                throw new Error(e.message);
            }
        },
        reload: async () => {
             try {
                const res = await fetch(`${APACHE_API_URL}/apache/reload`, {
                    method: 'POST'
                });
                if (!res.ok) throw new Error('Reload failed');
                return await res.json();
            } catch (e: any) {
                throw new Error(e.message);
            }
        }
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
