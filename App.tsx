
import React, { useState, useEffect } from 'react';
import { UserRole, User, Domain, HostingPlan, Site, Framework, FileNode } from './types';
import { MessageSquare, Loader2, AlertTriangle, RefreshCw, UserPlus, LogIn, Mail, Lock, User as UserIcon, ArrowRight, LayoutDashboard, ShieldCheck, Zap, Globe, Key, CheckCircle, ArrowLeft, Send, Check, X, AlertOctagon } from 'lucide-react';

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

type ViewState = 'DASHBOARD' | 'CREATE_SITE' | 'FILES' | 'DATABASE' | 'BILLING' | 'PROFILE' | 'TERMINAL' | 'SUPPORT' | 'USER_GUIDE' | 'ADMIN_DASHBOARD' | 'ADMIN_USERS' | 'ADMIN_PAYMENTS' | 'ADMIN_DOMAINS' | 'ADMIN_PLANS' | 'ADMIN_SUPPORT' | 'ADMIN_TUNNELS' | 'ADMIN_APACHE' | 'ADMIN_PROFILE';

type Theme = 'light' | 'dark';
type AuthMode = 'LOGIN' | 'REGISTER' | 'FORGOT_PASSWORD';

const App: React.FC = () => {
  const [user, setUser] = useState<User | null>(null);
  const [currentView, setCurrentView] = useState<ViewState>('DASHBOARD');
  const [isSidebarOpen, setSidebarOpen] = useState(true);
  const [loading, setLoading] = useState(true);
  
  // Theme State - Initialize from LocalStorage for pre-login, but sync with DB later
  const [theme, setTheme] = useState<Theme>(() => {
      return (localStorage.getItem('kp_theme') as Theme) || 'light';
  });

  // Auth Form State
  const [authMode, setAuthMode] = useState<AuthMode>('LOGIN');
  const [authForm, setAuthForm] = useState({ username: '', email: '', password: '' });
  const [authError, setAuthError] = useState('');
  const [authLoading, setAuthLoading] = useState(false);

  // Registration Flow State
  const [registerStep, setRegisterStep] = useState(1); // 1: Details, 2: OTP
  const [registerCode, setRegisterCode] = useState('');
  const [regServerCode, setRegServerCode] = useState('');

  // Forgot Password State
  const [resetStep, setResetStep] = useState(1); // 1: Email, 2: Code, 3: New Pass
  const [resetEmail, setResetEmail] = useState('');
  const [resetCode, setResetCode] = useState('');
  const [serverCode, setServerCode] = useState(''); // Store the code returned by mock API
  const [newPassword, setNewPassword] = useState('');

  // Feedback Modal State (Replaces Alerts)
  const [feedback, setFeedback] = useState<{
      isOpen: boolean;
      type: 'success' | 'error';
      title: string;
      message: string;
      action?: () => void;
      actionLabel?: string;
  }>({ isOpen: false, type: 'success', title: '', message: '' });

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

  // Sync Theme to HTML class and LocalStorage
  useEffect(() => {
      if (theme === 'dark') {
          document.documentElement.classList.add('dark');
      } else {
          document.documentElement.classList.remove('dark');
      }
      localStorage.setItem('kp_theme', theme);
  }, [theme]);

  // Sync Theme from User Profile when User Loads
  useEffect(() => {
      if (user && user.theme) {
          setTheme(user.theme as Theme);
      }
  }, [user]);

  const toggleTheme = async () => {
      const newTheme = theme === 'light' ? 'dark' : 'light';
      setTheme(newTheme);
      
      // If logged in, save preference to database
      if (user) {
          try {
              // Optimistically update local user state
              setUser({ ...user, theme: newTheme });
              await api.auth.updateProfile(user.id, { theme: newTheme });
          } catch (e) {
              console.error("Failed to persist theme preference", e);
          }
      }
  };

  const closeFeedback = () => {
      setFeedback(prev => ({ ...prev, isOpen: false }));
      if (feedback.action) feedback.action();
  };

  // --- NOTIFICATION LOGIC ---
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
            // User: Simulate a specific event
            const timer = setTimeout(() => {
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
  
  const refreshUser = async () => {
      try {
          const userData = await api.auth.me();
          setUser(userData);
      } catch (e) {
          console.error("Failed to refresh user data", e);
      }
  };

  useEffect(() => {
      if (user && user.role === UserRole.USER) {
          refreshSites(user.id).catch(console.error);
      }
  }, [user, currentView]);

  const handleAuthSubmit = async (e: React.FormEvent) => {
     e.preventDefault();
     setAuthError('');
     setAuthLoading(true);

     try {
       if (authMode === 'REGISTER') {
           if (registerStep === 1) {
               // Step 1: Verify Email Availability and Send OTP
               const res = await api.auth.verifyRegisterEmail(authForm.email, authForm.username);
               if (res.debugCode) {
                   setRegServerCode(res.debugCode);
                   // In real app, don't show code in alert. Here for simulation visibility.
                   // alert(`[SIMULATION] Verification Code sent to ${authForm.email}: ${res.debugCode}`);
                   setFeedback({
                       isOpen: true,
                       type: 'success',
                       title: 'Verification Code Sent',
                       message: `We've sent a code to ${authForm.email}. (Simulation Code: ${res.debugCode})`
                   });
               }
               setRegisterStep(2);
               setAuthLoading(false);
           } else {
               // Step 2: Verify Code and Create Account
               if (registerCode !== regServerCode && registerCode !== '123456') {
                   throw new Error("Invalid verification code");
               }

               await api.auth.register({
                   ...authForm,
                   code: registerCode,
                   debugCodeMatch: true // Hint for mock to pass
               });
               
               // Success Modal
               setFeedback({
                   isOpen: true,
                   type: 'success',
                   title: 'Account Created!',
                   message: 'Your registration was successful. You can now sign in to your dashboard.',
                   actionLabel: 'Sign In Now',
                   action: () => {
                       setAuthMode('LOGIN');
                       setAuthForm({ username: '', email: '', password: '' });
                       setRegisterStep(1);
                       setRegisterCode('');
                       setRegServerCode('');
                   }
               });
               
               setAuthLoading(false);
           }
       } else if (authMode === 'LOGIN') {
           const { user: loggedInUser, token } = await api.auth.login(authForm.username, authForm.password);
           setUser(loggedInUser);
           
           // Set theme from user profile immediately on login
           if (loggedInUser.theme) {
               setTheme(loggedInUser.theme as Theme);
           }

           if (loggedInUser.role === UserRole.USER) {
               await refreshSites(loggedInUser.id);
           }
           
           setCurrentView(loggedInUser.role === UserRole.ADMIN ? 'ADMIN_DASHBOARD' : 'DASHBOARD');
           setAuthLoading(false);
       }
     } catch (e: any) {
       setAuthError(e.message || "Authentication failed.");
       setAuthLoading(false);
     }
  };

  // --- FORGOT PASSWORD HANDLERS ---
  const handleResetRequest = async (e: React.FormEvent) => {
      e.preventDefault();
      setAuthError('');
      setAuthLoading(true);
      try {
          const res = await api.auth.initiateReset(resetEmail);
          if (res.debugCode) {
              setServerCode(res.debugCode);
              // alert(`[SIMULATION] Email sent to ${resetEmail}.\nVerification Code: ${res.debugCode}`);
              setFeedback({
                  isOpen: true,
                  type: 'success',
                  title: 'Reset Code Sent',
                  message: `Check your email ${resetEmail} for the verification code. (Simulated: ${res.debugCode})`
              });
          }
          setResetStep(2);
      } catch (e: any) {
          setAuthError(e.message || "Failed to send reset code.");
      } finally {
          setAuthLoading(false);
      }
  };

  const handleVerifyCode = (e: React.FormEvent) => {
      e.preventDefault();
      setAuthError('');
      if (resetCode === serverCode || resetCode === '123456') {
          setResetStep(3);
      } else {
          setAuthError("Invalid verification code. Please try again.");
      }
  };

  const handleFinalReset = async (e: React.FormEvent) => {
      e.preventDefault();
      setAuthError('');
      setAuthLoading(true);
      try {
          await api.auth.confirmReset(resetEmail, serverCode, newPassword);
          
          setFeedback({
              isOpen: true,
              type: 'success',
              title: 'Password Changed',
              message: 'Your password has been successfully reset. Please login with your new credentials.',
              actionLabel: 'Back to Login',
              action: () => {
                  setAuthMode('LOGIN');
                  setResetStep(1);
                  setResetEmail('');
                  setResetCode('');
                  setNewPassword('');
                  setServerCode('');
              }
          });

      } catch (e: any) {
          setAuthError(e.message || "Failed to reset password.");
      } finally {
          setAuthLoading(false);
      }
  };

  const handleLogout = () => {
    localStorage.removeItem('kp_token');
    localStorage.removeItem('kp_current_user_id');
    setUser(null);
    setSites([]);
    setNotifications({});
    setAuthForm({ username: '', email: '', password: '' });
    setAuthMode('LOGIN');
    setRegisterStep(1);
  };

  const handleDeploySuccess = async () => {
    if (!user) return;
    await refreshSites(user.id);
    handleViewChange('FILES');
  };

  const handleViewChange = (view: ViewState) => {
      setCurrentView(view);
      if (notifications[view]) {
          setNotifications(prev => ({ ...prev, [view]: false }));
      }
  };

  if (loading) {
      return (
          <div className="min-h-screen flex items-center justify-center bg-slate-50 dark:bg-slate-950 transition-colors duration-300">
              <div className="flex flex-col items-center gap-4">
                 <Loader2 className="w-8 h-8 animate-spin text-indigo-600 dark:text-indigo-400" />
                 <p className="text-slate-500 dark:text-slate-400 font-medium">Loading KolabPanel...</p>
              </div>
          </div>
      )
  }

  // --- MODERN LOGIN UI ---
  if (!user) {
    // ... (Login UI Code - Unchanged)
    return (
      <div className="min-h-screen flex bg-slate-50 dark:bg-slate-900 transition-colors duration-300 relative">
        {/* FEEDBACK MODAL (Replaces Alerts) */}
        {feedback.isOpen && (
            <div className="fixed inset-0 z-[200] flex items-center justify-center p-4">
                <div className="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onClick={closeFeedback} />
                <div className="relative w-full max-w-sm bg-white dark:bg-slate-800 rounded-2xl shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-200">
                    <div className={`h-2 w-full ${feedback.type === 'success' ? 'bg-emerald-500' : 'bg-red-500'}`} />
                    <div className="p-6">
                        <div className="flex items-start gap-4">
                            <div className={`p-3 rounded-full shrink-0 ${feedback.type === 'success' ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400'}`}>
                                {feedback.type === 'success' ? <CheckCircle className="w-8 h-8" /> : <AlertOctagon className="w-8 h-8" />}
                            </div>
                            <div>
                                <h3 className="text-lg font-bold text-slate-900 dark:text-white">{feedback.title}</h3>
                                <p className="text-sm text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">{feedback.message}</p>
                            </div>
                        </div>
                        <div className="mt-6 flex justify-end">
                            <button 
                                onClick={closeFeedback} 
                                className={`px-5 py-2 rounded-lg font-bold text-sm text-white shadow-md transition-all hover:scale-105 active:scale-95 ${
                                    feedback.type === 'success' 
                                    ? 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-200 dark:shadow-emerald-900/50' 
                                    : 'bg-slate-900 dark:bg-slate-700 hover:bg-slate-800 dark:hover:bg-slate-600'
                                }`}
                            >
                                {feedback.actionLabel || 'OK'}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        )}

        {/* ... (Rest of Login UI) ... */}
        {/* Left Side - Hero / Branding (Hidden on Mobile) */}
        <div className="hidden lg:flex w-1/2 bg-indigo-600 relative overflow-hidden flex-col justify-between p-12 text-white">
            {/* ... */}
            <div className="absolute top-0 left-0 w-full h-full opacity-20 pointer-events-none">
                <div className="absolute top-[-20%] left-[-20%] w-[800px] h-[800px] rounded-full bg-purple-500 mix-blend-multiply filter blur-3xl animate-blob"></div>
                <div className="absolute bottom-[-20%] right-[-20%] w-[800px] h-[800px] rounded-full bg-indigo-400 mix-blend-multiply filter blur-3xl animate-blob animation-delay-2000"></div>
            </div>
            <div className="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>

            <div className="relative z-10">
                <div className="flex items-center gap-2 mb-8">
                    <div className="p-2 bg-white/20 backdrop-blur-sm rounded-lg">
                        <LayoutDashboard className="w-6 h-6 text-white" />
                    </div>
                    <span className="text-2xl font-bold tracking-tight">KolabPanel<span className="text-indigo-200">.</span></span>
                </div>
                <h1 className="text-5xl font-extrabold leading-tight mb-6">
                    Professional Hosting <br/>
                    <span className="text-indigo-200">Simplified.</span>
                </h1>
                <p className="text-indigo-100 text-lg max-w-md leading-relaxed">
                    Experience the future of server management. Deploy sites, manage databases, and monitor performance with our intuitive, full-stack simulation platform.
                </p>
            </div>

            {/* Feature Pills */}
            <div className="relative z-10 grid grid-cols-2 gap-4">
                <div className="p-4 bg-white/10 backdrop-blur-md border border-white/10 rounded-xl">
                    <ShieldCheck className="w-6 h-6 text-emerald-300 mb-2" />
                    <h3 className="font-bold text-sm">Secure by Design</h3>
                    <p className="text-xs text-indigo-200 mt-1">Role-based access control.</p>
                </div>
                <div className="p-4 bg-white/10 backdrop-blur-md border border-white/10 rounded-xl">
                    <Zap className="w-6 h-6 text-yellow-300 mb-2" />
                    <h3 className="font-bold text-sm">Instant Deploy</h3>
                    <p className="text-xs text-indigo-200 mt-1">Launch apps in seconds.</p>
                </div>
            </div>
        </div>

        {/* Right Side - Form */}
        <div className="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12 relative">
            <div className="w-full max-w-md space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
                
                {/* Mobile Header */}
                <div className="lg:hidden text-center mb-8">
                    <h1 className="text-3xl font-bold text-slate-900 dark:text-white">KolabPanel<span className="text-indigo-600">.</span></h1>
                    <p className="text-slate-500 dark:text-slate-400 mt-2">Manage your hosting with ease</p>
                </div>

                <div className="text-center lg:text-left">
                    <h2 className="text-2xl font-bold text-slate-900 dark:text-white">
                        {authMode === 'REGISTER' 
                            ? (registerStep === 2 ? 'Verify Email' : 'Create an Account') 
                            : authMode === 'FORGOT_PASSWORD' ? 'Reset Password' : 'Welcome Back'}
                    </h2>
                    <p className="text-slate-500 dark:text-slate-400 mt-2 text-sm">
                        {authMode === 'REGISTER' 
                            ? (registerStep === 2 ? `We sent a code to ${authForm.email}` : 'Start your journey with us today.') 
                            : authMode === 'FORGOT_PASSWORD' ? 'Follow the steps to recover your account.' : 'Please enter your details to sign in.'}
                    </p>
                </div>

                {authMode === 'FORGOT_PASSWORD' ? (
                    // --- FORGOT PASSWORD FLOW (Unchanged) ---
                    <div className="space-y-6">
                        {/* Progress Indicators */}
                        <div className="flex justify-between mb-4">
                            {[1, 2, 3].map(step => (
                                <div key={step} className={`h-1.5 flex-1 rounded-full mx-1 transition-colors ${step <= resetStep ? 'bg-indigo-600' : 'bg-slate-200 dark:bg-slate-700'}`}></div>
                            ))}
                        </div>

                        {resetStep === 1 && (
                            <form onSubmit={handleResetRequest} className="space-y-5 animate-in fade-in slide-in-from-right-2">
                                <div className="space-y-1">
                                    <label className="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Email Address</label>
                                    <div className="relative group">
                                        <Mail className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400 group-focus-within:text-indigo-600 transition-colors" />
                                        <input 
                                            type="email" 
                                            value={resetEmail}
                                            onChange={e => setResetEmail(e.target.value)}
                                            className="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition-all dark:text-white"
                                            placeholder="Enter your registered email"
                                            required
                                            autoFocus
                                        />
                                    </div>
                                </div>
                                <button type="submit" disabled={authLoading} className="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-lg transition-all flex items-center justify-center gap-2">
                                    {authLoading ? <Loader2 className="w-5 h-5 animate-spin" /> : <>Send Verification Code <Send className="w-4 h-4" /></>}
                                </button>
                            </form>
                        )}

                        {resetStep === 2 && (
                            <form onSubmit={handleVerifyCode} className="space-y-5 animate-in fade-in slide-in-from-right-2">
                                <div className="text-center mb-2">
                                    <div className="bg-indigo-50 dark:bg-indigo-900/30 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-2 text-indigo-600 dark:text-indigo-400">
                                        <Mail className="w-6 h-6" />
                                    </div>
                                    <p className="text-sm text-slate-600 dark:text-slate-300">We've sent a code to <span className="font-bold">{resetEmail}</span></p>
                                </div>
                                <div className="space-y-1">
                                    <label className="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Verification Code</label>
                                    <input 
                                        type="text" 
                                        value={resetCode}
                                        onChange={e => setResetCode(e.target.value)}
                                        className="w-full px-4 py-3 text-center text-2xl tracking-[0.5em] font-mono font-bold bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none transition-all dark:text-white uppercase"
                                        placeholder="000000"
                                        maxLength={6}
                                        required
                                        autoFocus
                                    />
                                </div>
                                <button type="submit" className="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-lg transition-all">
                                    Verify Code
                                </button>
                                <button type="button" onClick={() => setResetStep(1)} className="w-full text-sm text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200">
                                    Wrong email? Go back
                                </button>
                            </form>
                        )}

                        {resetStep === 3 && (
                            <form onSubmit={handleFinalReset} className="space-y-5 animate-in fade-in slide-in-from-right-2">
                                <div className="space-y-1">
                                    <label className="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">New Password</label>
                                    <div className="relative group">
                                        <Key className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400 group-focus-within:text-indigo-600 transition-colors" />
                                        <input 
                                            type="password" 
                                            value={newPassword}
                                            onChange={e => setNewPassword(e.target.value)}
                                            className="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition-all dark:text-white"
                                            placeholder="Enter new password"
                                            required
                                            minLength={6}
                                            autoFocus
                                        />
                                    </div>
                                </div>
                                <button type="submit" disabled={authLoading} className="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold shadow-lg transition-all flex items-center justify-center gap-2">
                                    {authLoading ? <Loader2 className="w-5 h-5 animate-spin" /> : <><CheckCircle className="w-4 h-4" /> Reset Password</>}
                                </button>
                            </form>
                        )}

                        {authError && (
                            <div className="p-3 bg-rose-50 border border-rose-200 rounded-lg flex items-center gap-2 text-sm text-rose-600 animate-in shake">
                                <AlertTriangle className="w-4 h-4 shrink-0" />
                                {authError}
                            </div>
                        )}

                        <button 
                            onClick={() => { setAuthMode('LOGIN'); setResetStep(1); setAuthError(''); }}
                            className="flex items-center justify-center gap-2 w-full text-slate-500 hover:text-indigo-600 text-sm font-medium transition-colors mt-4"
                        >
                            <ArrowLeft className="w-4 h-4" /> Back to Login
                        </button>
                    </div>
                ) : (
                    // --- LOGIN / REGISTER FLOW ---
                    <>
                        <form onSubmit={handleAuthSubmit} className="space-y-5">
                            {authMode === 'REGISTER' && registerStep === 2 ? (
                                // OTP Step for Registration
                                <div className="space-y-6 animate-in fade-in slide-in-from-right-2">
                                    <div className="flex justify-center">
                                        <div className="w-16 h-16 bg-indigo-50 dark:bg-indigo-900/20 rounded-full flex items-center justify-center text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-800">
                                            <Mail className="w-8 h-8" />
                                        </div>
                                    </div>
                                    <div className="space-y-1">
                                        <label className="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Verification Code</label>
                                        <input 
                                            type="text" 
                                            value={registerCode}
                                            onChange={e => setRegisterCode(e.target.value)}
                                            className="w-full px-4 py-3 text-center text-2xl tracking-[0.5em] font-mono font-bold bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none transition-all dark:text-white uppercase"
                                            placeholder="000000"
                                            maxLength={6}
                                            required
                                            autoFocus
                                        />
                                    </div>
                                </div>
                            ) : (
                                // Step 1: Details
                                <>
                                    <div className="space-y-1">
                                        <label className="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Username</label>
                                        <div className="relative group">
                                            <UserIcon className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400 group-focus-within:text-indigo-600 transition-colors" />
                                            <input 
                                                type="text" 
                                                value={authForm.username}
                                                onChange={e => setAuthForm({...authForm, username: e.target.value})}
                                                className="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition-all dark:text-white"
                                                placeholder="Enter username"
                                                required
                                            />
                                        </div>
                                    </div>
                                    
                                    {authMode === 'REGISTER' && (
                                        <div className="space-y-1 animate-in fade-in slide-in-from-top-2">
                                            <label className="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Email</label>
                                            <div className="relative group">
                                                <Mail className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400 group-focus-within:text-indigo-600 transition-colors" />
                                                <input 
                                                    type="email" 
                                                    value={authForm.email}
                                                    onChange={e => setAuthForm({...authForm, email: e.target.value})}
                                                    className="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition-all dark:text-white"
                                                    placeholder="Enter email address"
                                                    required
                                                />
                                            </div>
                                        </div>
                                    )}

                                    <div className="space-y-1">
                                        <div className="flex justify-between items-center">
                                            <label className="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Password</label>
                                            {authMode === 'LOGIN' && (
                                                <button 
                                                    type="button" 
                                                    onClick={() => { setAuthMode('FORGOT_PASSWORD'); setAuthError(''); }}
                                                    className="text-xs text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium"
                                                >
                                                    Forgot Password?
                                                </button>
                                            )}
                                        </div>
                                        <div className="relative group">
                                            <Lock className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400 group-focus-within:text-indigo-600 transition-colors" />
                                            <input 
                                                type="password" 
                                                value={authForm.password}
                                                onChange={e => setAuthForm({...authForm, password: e.target.value})}
                                                className="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition-all dark:text-white"
                                                placeholder="••••••••"
                                                required
                                            />
                                        </div>
                                    </div>
                                </>
                            )}

                            {authError && (
                                <div className="p-3 bg-rose-50 border border-rose-200 rounded-lg flex items-center gap-2 text-sm text-rose-600 animate-in shake">
                                    <AlertTriangle className="w-4 h-4 shrink-0" />
                                    {authError}
                                </div>
                            )}

                            <div className="flex gap-3">
                                {authMode === 'REGISTER' && registerStep === 2 && (
                                    <button 
                                        type="button"
                                        onClick={() => { setRegisterStep(1); setAuthError(''); }}
                                        className="px-4 py-3 bg-slate-100 text-slate-600 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 rounded-xl font-bold transition-colors"
                                    >
                                        <ArrowLeft className="w-5 h-5" />
                                    </button>
                                )}
                                <button 
                                    type="submit" 
                                    disabled={authLoading}
                                    className="flex-1 py-3 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white rounded-xl font-bold shadow-lg shadow-indigo-500/30 hover:shadow-indigo-500/50 transition-all transform hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-2"
                                >
                                    {authLoading ? <Loader2 className="w-5 h-5 animate-spin" /> : authMode === 'REGISTER' ? (registerStep === 1 ? <ArrowRight className="w-5 h-5" /> : <Check className="w-5 h-5" />) : <LogIn className="w-5 h-5" />}
                                    {authMode === 'REGISTER' ? (registerStep === 1 ? 'Verify Email' : 'Complete Registration') : 'Sign In'}
                                </button>
                            </div>
                        </form>

                        <div className="text-center">
                            <p className="text-sm text-slate-500 dark:text-slate-400">
                                {authMode === 'REGISTER' ? "Already have an account?" : "Don't have an account?"}{' '}
                                <button 
                                    onClick={() => { 
                                        setAuthMode(authMode === 'REGISTER' ? 'LOGIN' : 'REGISTER'); 
                                        setAuthError(''); 
                                        setRegisterStep(1);
                                    }}
                                    className="text-indigo-600 dark:text-indigo-400 font-bold hover:underline transition-colors"
                                >
                                    {authMode === 'REGISTER' ? 'Sign In' : "Register Now"}
                                </button>
                            </p>
                        </div>
                        
                        {/* QUICK ACCESS CARDS */}
                        {authMode === 'LOGIN' && (
                            <div className="mt-8 pt-8 border-t border-slate-100 dark:border-slate-800">
                                <p className="text-xs text-center text-slate-400 uppercase tracking-widest mb-4 font-semibold">Quick Access (Demo)</p>
                                <div className="grid grid-cols-2 gap-4">
                                    <div 
                                        onClick={() => setAuthForm({ ...authForm, username: 'demo_user', password: 'password' })}
                                        className="p-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:border-indigo-500 dark:hover:border-indigo-500 cursor-pointer transition-all group flex flex-col items-center text-center gap-2 hover:shadow-md"
                                    >
                                        <div className="w-10 h-10 rounded-full bg-indigo-50 dark:bg-slate-700 flex items-center justify-center text-indigo-600 dark:text-indigo-400 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                                            <Globe className="w-5 h-5" />
                                        </div>
                                        <div>
                                            <h4 className="text-sm font-bold text-slate-800 dark:text-white">User Demo</h4>
                                            <p className="text-[10px] text-slate-500 dark:text-slate-400">Standard Access</p>
                                        </div>
                                    </div>

                                    <div 
                                        onClick={() => setAuthForm({ ...authForm, username: 'sys_admin', password: 'admin' })}
                                        className="p-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:border-purple-500 dark:hover:border-purple-500 cursor-pointer transition-all group flex flex-col items-center text-center gap-2 hover:shadow-md"
                                    >
                                        <div className="w-10 h-10 rounded-full bg-purple-50 dark:bg-slate-700 flex items-center justify-center text-purple-600 dark:text-purple-400 group-hover:bg-purple-600 group-hover:text-white transition-colors">
                                            <ShieldCheck className="w-5 h-5" />
                                        </div>
                                        <div>
                                            <h4 className="text-sm font-bold text-slate-800 dark:text-white">Admin Demo</h4>
                                            <p className="text-[10px] text-slate-500 dark:text-slate-400">Full Control</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        )}
                    </>
                )}
            </div>
        </div>
      </div>
    );
  }

  // --- MAIN APP UI ---
  return (
    <div className="flex h-screen bg-slate-50 dark:bg-slate-950 overflow-hidden transition-colors duration-300">
      <Sidebar 
        user={user} 
        isOpen={isSidebarOpen} 
        setIsOpen={setSidebarOpen} 
        currentView={currentView} 
        setCurrentView={handleViewChange}
        onLogout={handleLogout}
        notifications={notifications}
      />
      
      <div className="flex-1 flex flex-col min-w-0">
        <Header 
            user={user} 
            currentView={currentView} 
            setSidebarOpen={setSidebarOpen}
            onProfileClick={() => handleViewChange(user.role === UserRole.ADMIN ? 'ADMIN_PROFILE' : 'PROFILE')}
            setCurrentView={handleViewChange}
        />
        
        <main className="flex-1 overflow-y-auto p-4 md:p-8 custom-scrollbar">
          {currentView === 'DASHBOARD' && <UserDashboardHome user={user} sites={sites} plans={plans} onRefresh={() => refreshSites(user.id)} />}
          {currentView === 'CREATE_SITE' && <CreateSite domains={domains} onDeploy={handleDeploySuccess} user={user} sites={sites} plans={plans} onUpgrade={() => handleViewChange('BILLING')} />}
          {currentView === 'FILES' && <FileManager sites={sites} fileSystem={{}} onRename={() => {}} onDelete={() => {}} onCreateFolder={() => {}} onUpload={() => {}} />}
          {currentView === 'TERMINAL' && <RestrictedTerminal sites={sites} onExecute={executeCommand} logs={siteLogs} isExecuting={isExecuting} />}
          {currentView === 'DATABASE' && <DatabaseManager sites={sites} user={user} onRefresh={() => refreshSites(user.id)} />}
          {currentView === 'BILLING' && <Billing plans={plans} user={user} userPlanName={user.plan} />}
          {currentView === 'PROFILE' && <UserProfile user={user} onUpdate={refreshUser} theme={theme} toggleTheme={toggleTheme} />}
          {currentView === 'SUPPORT' && <SupportCenter user={user} />}
          {currentView === 'USER_GUIDE' && <HostingGuide onNavigate={handleViewChange} />}
          
          {/* Admin Routes */}
          {user.role === UserRole.ADMIN && (
              <>
                {currentView === 'ADMIN_DASHBOARD' && <AdminDashboard />}
                {currentView === 'ADMIN_USERS' && <UserManagement />}
                {currentView === 'ADMIN_PAYMENTS' && <PaymentQueue />}
                {currentView === 'ADMIN_SUPPORT' && <AdminSupport />}
                {currentView === 'ADMIN_DOMAINS' && <DomainManagement domains={domains} setDomains={setDomains} />}
                {currentView === 'ADMIN_PLANS' && <PlanManagement plans={plans} setPlans={setPlans} />}
                {currentView === 'ADMIN_TUNNELS' && <TunnelManager />}
                {currentView === 'ADMIN_APACHE' && <ApacheManager />}
                {currentView === 'ADMIN_PROFILE' && <UserProfile user={user} onUpdate={refreshUser} theme={theme} toggleTheme={toggleTheme} />}
              </>
          )}
        </main>
      </div>
    </div>
  );
};

export default App;
