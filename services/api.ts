import { User, Site, HostingPlan, Domain, Payment, Framework, FileNode, SiteStatus, PaymentStatus, SupportTicket, ChatMessage } from '../types';
import { getMockFiles } from '../constants';
import { 
    DB_KEYS, 
    INITIAL_USERS, 
    INITIAL_PASSWORDS, 
    INITIAL_PLANS, 
    INITIAL_DOMAINS, 
    getStorage, 
    setStorage, 
    delay 
} from './mockData';

// --- MOCK IMPLEMENTATION ---

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
       return delay({
           totalUsers: users.length,
           totalSites: sites.length,
           activeRevenue: '0'
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