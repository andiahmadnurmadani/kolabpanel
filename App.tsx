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
import { UserDashboardHome, DatabaseManager, Billing, UserProfile, SupportCenter, HostingGuide } from './pages/UserPages';
import { AdminDashboard, PaymentQueue, UserManagement, DomainManagement, PlanManagement, AdminSupport, TunnelManager, ApacheManager } from './pages/AdminPages';
import { LoginPage, RegisterPage, VerifyEmailPage } from './pages/AuthPages';

type ViewState = 'DASHBOARD' | 'CREATE_SITE' | 'FILES' | 'DATABASE' | 'BILLING' | 'PROFILE' | 'TERMINAL' | 'SUPPORT' | 'USER_GUIDE' | 'ADMIN_DASHBOARD' | 'ADMIN_USERS' | 'ADMIN_PAYMENTS' | 'ADMIN_DOMAINS' | 'ADMIN_PLANS' | 'ADMIN_SUPPORT' | 'ADMIN_TUNNELS' | 'ADMIN_APACHE' | 'ADMIN_PROFILE';

const App: React.FC = () => {
  const [user, setUser] = useState<User | null>(null);
  const [currentView, setCurrentView] = useState<ViewState>('DASHBOARD');
  const [isSidebarOpen, setSidebarOpen] = useState(true);
  const [loading, setLoading] = useState(true);
  const [authPage, setAuthPage] = useState<'login' | 'register' | 'verify-email'>('login');
  const [verificationToken, setVerificationToken] = useState<string>('');

  // Notification State
  const [notifications, setNotifications] = useState<Record<string, boolean>>({});

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

  // --- NOTIFICATION LOGIC ---
  // Check for actual data (pending payments, open tickets) to set notifications
  useEffect(() => {
    if (!user) return;

    const checkDataNotifications = async () => {
      if (user.role === UserRole.ADMIN) {
        try {
          // Admin: Fetch real counts
          const [payments, tickets] = await Promise.all([
            api.admin.getPayments(),
            api.tickets.list()
          ]);

          const hasPendingPayments = payments.some(p => p.status === 'PENDING');
          const hasOpenTickets = tickets.some(t => t.status === 'OPEN');

          setNotifications(prev => ({
            ...prev,
            'ADMIN_PAYMENTS': hasPendingPayments,
            'ADMIN_SUPPORT': hasOpenTickets
          }));
        } catch (e) {
          console.error("Failed to sync admin notifications", e);
        }
      } else {
        // User: Simulate a specific event (like payment verification) if needed
        // For now, we keep it clean or just simulate one event for demo
        const timer = setTimeout(() => {
          // Example: Notify user to check billing after login
          setNotifications(prev => ({ ...prev, 'BILLING': true }));
        }, 3000);
        return () => clearTimeout(timer);
      }
    };

    checkDataNotifications();
  }, [user]);
  // -------------------------

  const init = async () => {
    setLoading(true);
    try {
      // Check URL for verification token
      const params = new URLSearchParams(window.location.search);
      const token = params.get('token');
      if (token) {
        setVerificationToken(token);
        setAuthPage('verify-email');
        // Clear URL params
        window.history.replaceState({}, '', window.location.pathname);
      }

      const [plansData, domainsData] = await Promise.all([
        api.common.getPlans(),
        api.common.getDomains()
      ]);
      setPlans(plansData);
      setDomains(domainsData);

      // Check login
      const savedToken = localStorage.getItem('kp_token');
      if (savedToken) {
        try {
          const userData = await api.auth.me();
          setUser(userData);
          setCurrentView(userData.role === UserRole.ADMIN ? 'ADMIN_DASHBOARD' : 'DASHBOARD');

          if (userData.role === UserRole.USER) {
            refreshSites(userData.id);
          }
        } catch (e) {
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

  const refreshUser = async () => {
    try {
      const userData = await api.auth.me();
      setUser(userData);
    } catch (e) {
      console.error("Failed to refresh user data", e);
    }
  };

  // Update sites when user changes or view changes
  useEffect(() => {
    if (user && user.role === UserRole.USER) {
      refreshSites(user.id).catch(console.error);
    }
  }, [user, currentView]);

  const handleLogin = async (username: string, password: string) => {
    const { user: loggedInUser, token } = await api.auth.login(username, password);
    setUser(loggedInUser);

    if (loggedInUser.role === UserRole.USER) {
      await refreshSites(loggedInUser.id);
    }

    setCurrentView(loggedInUser.role === UserRole.ADMIN ? 'ADMIN_DASHBOARD' : 'DASHBOARD');
  };

  const handleRegister = async (username: string, email: string, password: string) => {
    const response = await fetch('http://localhost:5000/api/auth/register', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ username, email, password })
    });

    if (!response.ok) {
      const error = await response.json();
      throw new Error(error.message);
    }

    return response.json();
  };

  const handleLogout = () => {
    localStorage.removeItem('kp_token');
    localStorage.removeItem('kp_current_user_id');
    setUser(null);
    setSites([]);
    setNotifications({}); // Clear notifications on logout
  };

  const handleDeploySuccess = async () => {
    if (!user) return;
    await refreshSites(user.id);
    handleViewChange('FILES');
  };

  // Wrapper to handle navigation AND clear notifications
  const handleViewChange = (view: ViewState) => {
    setCurrentView(view);

    // If there was a notification for this view, clear it
    if (notifications[view]) {
      setNotifications(prev => ({
        ...prev,
        [view]: false
      }));
    }
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
    if (authPage === 'verify-email') {
      return (
        <VerifyEmailPage
          token={verificationToken}
          onNavigateToLogin={() => setAuthPage('login')}
        />
      );
    }

    if (authPage === 'register') {
      return (
        <RegisterPage
          onRegister={handleRegister}
          onNavigateToLogin={() => setAuthPage('login')}
        />
      );
    }

    return (
      <LoginPage
        onLogin={handleLogin}
        onNavigateToRegister={() => setAuthPage('register')}
      />
    );
  }

  return (
    <div className="min-h-screen flex bg-slate-50">
      <Sidebar
        user={user}
        isOpen={isSidebarOpen}
        setIsOpen={setSidebarOpen}
        currentView={currentView}
        setCurrentView={handleViewChange}
        onLogout={handleLogout}
        notifications={notifications}
      />

      <main className="flex-1 flex flex-col h-screen overflow-hidden">
        <Header
          user={user}
          currentView={currentView}
          setSidebarOpen={setSidebarOpen}
          onProfileClick={() => handleViewChange(user.role === UserRole.ADMIN ? 'ADMIN_PROFILE' : 'PROFILE')}
        />

        <div className="flex-1 overflow-y-auto p-6 scroll-smooth">
          <div className="max-w-6xl mx-auto">
            {/* USER VIEWS */}
            {currentView === 'DASHBOARD' && <UserDashboardHome sites={sites} user={user} plans={plans} onRefresh={() => user && refreshSites(user.id)} />}
            {currentView === 'CREATE_SITE' && (
              <CreateSite
                domains={domains}
                onDeploy={handleDeploySuccess}
                user={user}
                sites={sites}
                plans={plans}
                onUpgrade={() => handleViewChange('BILLING')}
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
            {currentView === 'PROFILE' && <UserProfile user={user} onUpdate={refreshUser} />}
            {currentView === 'SUPPORT' && <SupportCenter user={user} />}
            {currentView === 'USER_GUIDE' && <HostingGuide onNavigate={handleViewChange} />}

            {/* ADMIN VIEWS */}
            {currentView === 'ADMIN_DASHBOARD' && <AdminDashboard />}
            {currentView === 'ADMIN_USERS' && <UserManagement />}
            {currentView === 'ADMIN_PAYMENTS' && <PaymentQueue />}
            {currentView === 'ADMIN_SUPPORT' && <AdminSupport />}
            {currentView === 'ADMIN_DOMAINS' && <DomainManagement domains={domains} setDomains={setDomains} />}
            {currentView === 'ADMIN_PLANS' && <PlanManagement plans={plans} setPlans={setPlans} />}
            {currentView === 'ADMIN_TUNNELS' && <TunnelManager />}
            {currentView === 'ADMIN_APACHE' && <ApacheManager />}

            {/* SHARED VIEW FOR ADMIN PROFILE */}
            {currentView === 'ADMIN_PROFILE' && <UserProfile user={user} onUpdate={refreshUser} />}
          </div>
        </div>
      </main>
    </div>
  );
};

export default App;