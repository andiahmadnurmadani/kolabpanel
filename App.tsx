import React, { useState, useEffect } from 'react';
import { UserRole, User, Domain, HostingPlan, Site, Framework, FileNode } from './types';
import { MessageSquare, Loader2, AlertTriangle, RefreshCw } from 'lucide-react';

// API
import { api } from './services/api';

// Hooks
import { useFileSystem } from './hooks/useFileSystem';
import { useTerminal } from './hooks/useTerminal';

// Components
import { Sidebar } from './components/layout/Sidebar';
import { Header } from './components/layout/Header';
import { CreateSite } from './components/user/CreateSite';
import { FileManager } from './components/user/FileManager';
import { RestrictedTerminal } from './components/user/RestrictedTerminal';
import { UserDashboardHome, DatabaseManager, Billing, UserProfile, SupportCenter } from './pages/UserPages';
import { AdminDashboard, PaymentQueue, UserManagement, DomainManagement, PlanManagement, AdminSupport } from './pages/AdminPages';
import { ArchitectureDoc } from './pages/ArchitectureDoc';

type ViewState = 'DASHBOARD' | 'CREATE_SITE' | 'FILES' | 'DATABASE' | 'BILLING' | 'PROFILE' | 'TERMINAL' | 'SUPPORT' | 'ADMIN_DASHBOARD' | 'ADMIN_USERS' | 'ADMIN_PAYMENTS' | 'ADMIN_DOMAINS' | 'ADMIN_PLANS' | 'ADMIN_SUPPORT' | 'ARCHITECTURE';

const App: React.FC = () => {
  const [user, setUser] = useState<User | null>(null);
  const [currentView, setCurrentView] = useState<ViewState>('DASHBOARD');
  const [isSidebarOpen, setSidebarOpen] = useState(true);
  const [loading, setLoading] = useState(true);
  
  // Data State
  const [domains, setDomains] = useState<Domain[]>([]);
  const [plans, setPlans] = useState<HostingPlan[]>([]);
  const [sites, setSites] = useState<Site[]>([]);
  
  // Hooks
  const { fetchFiles, uploadFile, renameFile, deleteFile, createFolder } = useFileSystem(sites);
  const { siteLogs, isExecuting, executeCommand } = useTerminal();

  // Initial Load
  useEffect(() => {
    init();
  }, []);

  const init = async () => {
    setLoading(true);
    try {
      const [plansData, domainsData] = await Promise.all([
         api.common.getPlans(),
         api.common.getDomains()
      ]);
      setPlans(plansData);
      setDomains(domainsData);

      // Check login
      const token = localStorage.getItem('kp_token');
      if (token) {
         try {
           const userData = await api.auth.me();
           setUser(userData);
           setCurrentView(userData.role === UserRole.ADMIN ? 'ADMIN_DASHBOARD' : 'DASHBOARD');
           
           if (userData.role === UserRole.USER) {
              refreshSites(userData.id);
           }
         } catch(e) {
           // Token expired or invalid
           localStorage.removeItem('kp_token');
           setUser(null);
         }
      }
    } catch (err) {
      console.error("Initialization Error:", err);
    } finally {
      setLoading(false);
    }
  };

  const refreshSites = async (userId: string) => {
      const sitesData = await api.sites.list(userId);
      setSites(sitesData);
  }

  // Update sites when user changes or view changes
  useEffect(() => {
      if (user && user.role === UserRole.USER) {
          refreshSites(user.id).catch(console.error);
      }
  }, [user, currentView]);

  const handleLogin = async (username: string) => {
     try {
       // Mock password check
       const password = username === 'demo_user' ? 'password' : 'admin'; 
       
       const { user: loggedInUser, token } = await api.auth.login(username, password);
       // Token handled inside api login mock, but we set user state here
       setUser(loggedInUser);
       
       if (loggedInUser.role === UserRole.USER) {
           await refreshSites(loggedInUser.id);
       }
       
       setCurrentView(loggedInUser.role === UserRole.ADMIN ? 'ADMIN_DASHBOARD' : 'DASHBOARD');
     } catch (e) {
       alert("Login failed. Check credentials.");
     }
  };

  const handleLogout = () => {
    localStorage.removeItem('kp_token');
    localStorage.removeItem('kp_current_user_id');
    setUser(null);
    setSites([]);
  };

  const handleDeploySuccess = async () => {
    if (!user) return;
    await refreshSites(user.id);
    setCurrentView('FILES');
  };

  if (loading) {
      return (
          <div className="min-h-screen flex items-center justify-center bg-slate-50">
              <div className="flex flex-col items-center gap-4">
                 <Loader2 className="w-8 h-8 animate-spin text-indigo-600" />
                 <p className="text-slate-500 font-medium">Loading KolabPanel...</p>
              </div>
          </div>
      )
  }

  if (!user) {
    return (
      <div className="min-h-screen bg-slate-100 flex items-center justify-center p-4">
        <div className="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md space-y-8">
          <div className="text-center">
            <h1 className="text-3xl font-bold text-slate-900">KolabPanel</h1>
            <p className="text-slate-500 mt-2">Fullstack Hosting Simulation</p>
            <div className="mt-2 inline-block px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">
               Client-Side Mode
            </div>
          </div>
          
          <div className="space-y-4">
            <div className="p-4 border-2 border-slate-200 rounded-xl hover:border-indigo-500 cursor-pointer transition-all group" onClick={() => handleLogin('demo_user')}>
              <h3 className="font-semibold text-slate-800 group-hover:text-indigo-600">Login as User</h3>
              <p className="text-sm text-slate-500">Credential: demo_user / password</p>
            </div>
            <div className="p-4 border-2 border-slate-200 rounded-xl hover:border-indigo-500 cursor-pointer transition-all group" onClick={() => handleLogin('sys_admin')}>
              <h3 className="font-semibold text-slate-800 group-hover:text-indigo-600">Login as Admin</h3>
              <p className="text-sm text-slate-500">Credential: sys_admin / admin</p>
            </div>
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen flex bg-slate-50">
      <Sidebar 
        user={user} 
        isOpen={isSidebarOpen} 
        setIsOpen={setSidebarOpen} 
        currentView={currentView} 
        setCurrentView={setCurrentView} 
        onLogout={handleLogout}
      />

      <main className="flex-1 flex flex-col h-screen overflow-hidden">
        <Header 
          user={user} 
          currentView={currentView} 
          setSidebarOpen={setSidebarOpen} 
          onProfileClick={() => user.role === UserRole.USER && setCurrentView('PROFILE')} 
        />

        <div className="flex-1 overflow-y-auto p-6 scroll-smooth">
           <div className="max-w-6xl mx-auto">
             {currentView === 'DASHBOARD' && <UserDashboardHome sites={sites} user={user} plans={plans} onRefresh={() => user && refreshSites(user.id)} />}
             {currentView === 'CREATE_SITE' && (
                <CreateSite 
                    domains={domains} 
                    onDeploy={handleDeploySuccess} 
                    user={user} 
                    sites={sites} 
                    plans={plans}
                    onUpgrade={() => setCurrentView('BILLING')}
                />
             )}
             {currentView === 'FILES' && 
                <FileManager 
                    sites={sites} 
                    fileSystem={{}} 
                    onRename={renameFile}
                    onDelete={deleteFile}
                    onCreateFolder={createFolder}
                    onUpload={uploadFile}
                />
             }
             {currentView === 'DATABASE' && <DatabaseManager sites={sites} user={user} onRefresh={() => user && refreshSites(user.id)} />}
             {currentView === 'TERMINAL' && <RestrictedTerminal sites={sites} logs={siteLogs} isExecuting={isExecuting} onExecute={executeCommand} />}
             {currentView === 'BILLING' && <Billing plans={plans} userPlanName={user.plan} user={user} />}
             {currentView === 'PROFILE' && <UserProfile user={user} />}
             {currentView === 'SUPPORT' && <SupportCenter user={user} />}
             
             {currentView === 'ADMIN_DASHBOARD' && <AdminDashboard />}
             {currentView === 'ADMIN_USERS' && <UserManagement />}
             {currentView === 'ADMIN_PAYMENTS' && <PaymentQueue />}
             {currentView === 'ADMIN_SUPPORT' && <AdminSupport />}
             {currentView === 'ADMIN_DOMAINS' && <DomainManagement domains={domains} setDomains={setDomains} />}
             {currentView === 'ADMIN_PLANS' && <PlanManagement plans={plans} setPlans={setPlans} />}
             {currentView === 'ARCHITECTURE' && <ArchitectureDoc />}
           </div>
        </div>
      </main>
    </div>
  );
};

export default App;