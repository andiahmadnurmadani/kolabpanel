import React, { useState, useRef } from 'react';
import { Card } from '../../components/Shared';
import { api } from '../../services/api';
import { User, UserRole } from '../../types';
import { CreditCard, Calendar, User as UserIcon, Mail, Key, Lock, Check, AlertTriangle, ShieldCheck, Send, Loader2, CheckCircle, XCircle, Crown, Zap, Clock, Camera, Upload, X, AlertOctagon, Hourglass, Terminal, Server } from 'lucide-react';

interface UserProfileProps {
  user: User;
  onUpdate?: () => Promise<void>;
}

export const UserProfile: React.FC<UserProfileProps> = ({ user, onUpdate }) => {
  const [loading, setLoading] = useState(false);
  const [formData, setFormData] = useState({ 
      username: user.username, 
      email: user.email,
      avatar: user.avatar || 'https://picsum.photos/200'
  });
  const [passData, setPassData] = useState({ current: '', new: '', confirm: '' });
  const fileInputRef = useRef<HTMLInputElement>(null);

  // Verification State
  const [otpInput, setOtpInput] = useState('');
  const [systemOtp, setSystemOtp] = useState('');
  const [codeSent, setCodeSent] = useState(false);
  const [sendingCode, setSendingCode] = useState(false);
  const [verifyStatus, setVerifyStatus] = useState<'IDLE' | 'SUCCESS' | 'ERROR'>('IDLE');

  // Feedback Modal State
  const [feedback, setFeedback] = useState<{
      isOpen: boolean;
      type: 'success' | 'error';
      title: string;
      message: string;
  }>({ isOpen: false, type: 'success', title: '', message: '' });

  const TEST_OTP = '123456'; // Master code for testing

  const isEmailChanged = formData.email !== user.email;
  const isAdmin = user.role === UserRole.ADMIN;

  // --- PLAN EXPIRATION LOGIC ---
  const getPlanStatus = () => {
      if (!user.planExpiresAt) return 'LIFETIME';
      const now = new Date();
      const expiry = new Date(user.planExpiresAt);
      const diffTime = expiry.getTime() - now.getTime();
      const days = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
      
      if (days <= 0) return 'EXPIRED';
      if (days <= 7) return 'WARNING';
      return 'HEALTHY';
  };

  const planStatus = getPlanStatus();
  const expiryDateObj = user.planExpiresAt ? new Date(user.planExpiresAt) : null;
  const daysRemaining = expiryDateObj 
      ? Math.ceil((expiryDateObj.getTime() - Date.now()) / (1000 * 60 * 60 * 24)) 
      : 30; // Default for lifetime/new

  // Calculate visual percentage (assuming 30 day cycle for visualization)
  const progressPercent = Math.max(0, Math.min(100, (daysRemaining / 30) * 100));

  // Dynamic Styles Configuration
  const statusConfig = {
      HEALTHY: {
          gradient: 'from-indigo-600 to-violet-700',
          icon: Zap,
          badge: 'Active',
          badgeColor: 'bg-emerald-500/20 text-emerald-100 border-emerald-500/30',
          textColor: 'text-indigo-100',
          barColor: 'from-emerald-400 to-teal-300'
      },
      WARNING: {
          gradient: 'from-amber-500 to-orange-600',
          icon: AlertTriangle,
          badge: 'Expiring Soon',
          badgeColor: 'bg-white/20 text-white border-white/30 animate-pulse',
          textColor: 'text-orange-100',
          barColor: 'from-yellow-300 to-amber-200'
      },
      EXPIRED: {
          gradient: 'from-red-600 to-rose-700',
          icon: XCircle,
          badge: 'Plan Expired',
          badgeColor: 'bg-black/30 text-red-100 border-red-900/30',
          textColor: 'text-rose-100',
          barColor: 'from-red-400 to-red-300'
      },
      LIFETIME: {
          gradient: 'from-slate-700 to-slate-800',
          icon: Crown,
          badge: 'Lifetime',
          badgeColor: 'bg-yellow-500/20 text-yellow-100 border-yellow-500/30',
          textColor: 'text-slate-300',
          barColor: 'from-yellow-400 to-amber-300'
      }
  }[planStatus];

  const StatusIcon = statusConfig.icon;

  const closeFeedback = () => setFeedback(prev => ({ ...prev, isOpen: false }));

  const handleSendCode = async () => {
      if (!isEmailChanged) return;
      
      setSendingCode(true);
      setVerifyStatus('IDLE'); // Reset status on new code
      
      // Simulate network delay
      await new Promise(r => setTimeout(r, 1000));

      const code = Math.floor(100000 + Math.random() * 900000).toString();
      setSystemOtp(code);
      setCodeSent(true);
      setSendingCode(false);
      setOtpInput('');
      
      alert(`[SIMULATION] Verification Code sent to ${formData.email}: ${code}\n\n(Tip: You can also use '${TEST_OTP}')`);
  };

  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (file) {
        if (file.size > 2 * 1024 * 1024) {
            setFeedback({
                isOpen: true,
                type: 'error',
                title: 'File Too Large',
                message: 'The image size exceeds 2MB. Please upload a smaller image.'
            });
            return;
        }
        const reader = new FileReader();
        reader.onloadend = () => {
            setFormData(prev => ({ ...prev, avatar: reader.result as string }));
        };
        reader.readAsDataURL(file);
    }
  };

  const handleUpdateClick = async (e: React.FormEvent) => {
    e.preventDefault();
    setVerifyStatus('IDLE');
    
    if (isEmailChanged) {
        if (!codeSent) {
            setFeedback({
                isOpen: true,
                type: 'error',
                title: 'Verification Required',
                message: "Please click 'Send Code' to verify your new email address before saving."
            });
            return;
        }
        const isValid = otpInput === systemOtp || otpInput === TEST_OTP;
        if (!isValid) {
            setVerifyStatus('ERROR');
            return;
        }
        setVerifyStatus('SUCCESS');
        await new Promise(r => setTimeout(r, 800));
    }

    await saveProfile(formData);
    
    setCodeSent(false);
    setSystemOtp('');
    setOtpInput('');
    setVerifyStatus('IDLE');
  };

  const saveProfile = async (data: typeof formData) => {
    setLoading(true);
    try {
        await api.auth.updateProfile(user.id, data);
        if (onUpdate) await onUpdate();
        
        setFeedback({
            isOpen: true,
            type: 'success',
            title: 'Profile Updated',
            message: 'Your account information has been successfully updated.'
        });

    } catch (e: any) {
        console.error("Profile Update Error:", e);
        let msg = "Failed to update profile. Please try again.";
        if (e.name === 'QuotaExceededError' || e.message?.toLowerCase().includes('quota')) {
             msg = "Storage Limit Reached: The image is too large. Please try a smaller image.";
        }
        setFeedback({ isOpen: true, type: 'error', title: 'Update Failed', message: msg });
    } finally {
        setLoading(false);
    }
  };

  const handlePasswordUpdate = async (e: React.FormEvent) => {
    e.preventDefault();
    if (passData.new !== passData.confirm) {
        setFeedback({ isOpen: true, type: 'error', title: 'Password Mismatch', message: "The new password and confirmation password do not match." });
        return;
    }
    if (passData.new.length < 6) {
        setFeedback({ isOpen: true, type: 'error', title: 'Weak Password', message: "Password must be at least 6 characters long." });
        return;
    }

    setLoading(true);
    try {
        await api.auth.changePassword(user.id, passData.current, passData.new);
        setFeedback({ isOpen: true, type: 'success', title: 'Password Changed', message: 'Your password has been securely updated.' });
        setPassData({ current: '', new: '', confirm: '' });
    } catch (err: any) {
        setFeedback({ isOpen: true, type: 'error', title: 'Change Failed', message: err.message || "The current password provided is incorrect." });
    } finally {
        setLoading(false);
    }
  };

  return (
    <div className="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6 relative animate-in fade-in duration-300">
      
      {/* FEEDBACK POPUP MODAL */}
      {feedback.isOpen && (
        <div className="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div className="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onClick={closeFeedback} />
            <div className="relative w-full max-w-sm bg-white rounded-2xl shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-200">
                <div className={`h-2 w-full ${feedback.type === 'success' ? 'bg-emerald-500' : 'bg-red-500'}`} />
                <div className="p-6">
                    <div className="flex items-start gap-4">
                        <div className={`p-3 rounded-full shrink-0 ${feedback.type === 'success' ? 'bg-emerald-100 text-emerald-600' : 'bg-red-100 text-red-600'}`}>
                            {feedback.type === 'success' ? <CheckCircle className="w-8 h-8" /> : <AlertOctagon className="w-8 h-8" />}
                        </div>
                        <div>
                            <h3 className="text-lg font-bold text-slate-900">{feedback.title}</h3>
                            <p className="text-sm text-slate-500 mt-1 leading-relaxed">{feedback.message}</p>
                        </div>
                    </div>
                    <div className="mt-6 flex justify-end">
                        <button 
                            onClick={closeFeedback} 
                            className={`px-5 py-2 rounded-lg font-bold text-sm text-white shadow-md transition-all hover:scale-105 active:scale-95 ${
                                feedback.type === 'success' 
                                ? 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-200' 
                                : 'bg-slate-900 hover:bg-slate-800'
                            }`}
                        >
                            {feedback.type === 'success' ? 'Great, thanks!' : 'Close'}
                        </button>
                    </div>
                </div>
            </div>
        </div>
      )}

      <div className="space-y-6">
        <div className="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex flex-col items-center text-center">
          
          <div className="relative mb-4 group cursor-pointer" onClick={() => fileInputRef.current?.click()}>
              <div className="w-32 h-32 rounded-full overflow-hidden border-4 border-slate-100 shadow-inner relative">
                  <img src={formData.avatar} alt={formData.username} className="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" />
                  <div className="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center text-white backdrop-blur-[1px]">
                      <Camera className="w-8 h-8 mb-1" />
                      <span className="text-xs font-medium">Change Photo</span>
                  </div>
              </div>
              <div className="absolute bottom-1 right-1 bg-indigo-600 text-white p-2 rounded-full border-2 border-white shadow-sm z-10 group-hover:bg-indigo-700 transition-colors">
                  <Upload className="w-3.5 h-3.5" />
              </div>
          </div>
          <input 
              type="file" 
              ref={fileInputRef} 
              className="hidden" 
              accept="image/*"
              onChange={handleFileChange}
          />

          <h2 className="text-xl font-bold text-slate-900">{formData.username || user.username}</h2>
          <p className="text-sm text-slate-500 mb-4">{formData.email || user.email}</p>
        </div>
        
        {/* CONDITIONAL INFO CARD */}
        {isAdmin ? (
            <div className="bg-slate-900 rounded-xl shadow-lg text-white relative overflow-hidden p-6 border border-slate-700">
                {/* Background Decoration */}
                <div className="absolute -right-6 -top-6 text-slate-800 pointer-events-none">
                    <ShieldCheck className="w-32 h-32 opacity-50" />
                </div>
                
                <div className="relative z-10">
                    <div className="flex items-center justify-between mb-6">
                        <div className="flex items-center gap-3">
                            <div className="p-2 bg-indigo-500/20 rounded-lg border border-indigo-500/30">
                                <Terminal className="w-5 h-5 text-indigo-400" />
                            </div>
                            <h3 className="font-bold text-lg tracking-wide">Administrator</h3>
                        </div>
                        <span className="px-2 py-1 bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 rounded text-xs font-bold shadow-sm backdrop-blur-sm">
                            Root Access
                        </span>
                    </div>

                    <div className="space-y-4">
                        <div className="p-4 bg-slate-950/50 rounded-lg border border-slate-800 space-y-3">
                            <div className="flex justify-between items-center text-sm">
                                <span className="text-slate-400">Access Level</span>
                                <span className="font-mono font-bold text-emerald-400">SUPERUSER</span>
                            </div>
                        </div>

                        <div className="flex items-center gap-2 text-xs text-slate-500 mt-2 bg-slate-800/50 px-3 py-1.5 rounded-full w-fit">
                            <div className="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></div>
                            Secure Session Active
                        </div>
                    </div>
                </div>
            </div>
        ) : (
            <div className={`bg-gradient-to-br ${statusConfig.gradient} rounded-xl shadow-lg text-white relative overflow-hidden p-6 transition-all hover:shadow-xl hover:scale-[1.01]`}>
                {/* Background Decoration */}
                <div className="absolute -right-6 -top-6 text-white/10">
                    <Crown className="w-32 h-32 rotate-12" />
                </div>
                {/* Additional Alert Effect for Warning */}
                {planStatus === 'WARNING' && (
                    <div className="absolute inset-0 border-4 border-white/20 rounded-xl animate-pulse pointer-events-none"></div>
                )}
                
                <div className="absolute bottom-0 left-0 w-full h-24 bg-gradient-to-t from-black/20 to-transparent pointer-events-none"></div>

                {/* Header */}
                <div className="relative z-10 flex items-center justify-between mb-6">
                    <div className="flex items-center gap-3">
                        <div className="p-2 bg-white/20 rounded-lg backdrop-blur-sm shadow-inner border border-white/10">
                            <StatusIcon className="w-5 h-5 text-white" />
                        </div>
                        <h3 className="font-bold text-lg tracking-wide">Subscription</h3>
                    </div>
                    <span className={`px-2.5 py-1 ${statusConfig.badgeColor} border rounded-full text-xs font-bold flex items-center gap-1.5 shadow-sm backdrop-blur-md`}>
                        {planStatus !== 'EXPIRED' && (
                            <span className="relative flex h-2 w-2">
                                <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-current opacity-75"></span>
                                <span className="relative inline-flex rounded-full h-2 w-2 bg-current"></span>
                            </span>
                        )}
                        {statusConfig.badge}
                    </span>
                </div>

                {/* Plan Info */}
                <div className="relative z-10 mb-6">
                    <p className={`${statusConfig.textColor} text-xs font-bold uppercase tracking-wider mb-1`}>Current Plan</p>
                    <div className="text-3xl font-extrabold tracking-tight text-white drop-shadow-sm">{user.plan}</div>
                </div>

                {/* Billing Info & Progress */}
                <div className="relative z-10 bg-black/20 rounded-xl p-6 backdrop-blur-md border border-white/10 shadow-inner flex flex-col justify-between min-h-[180px]">
                    {/* Expiry Info */}
                    <div className="flex items-center justify-between border-b border-white/10 pb-4">
                        <div className={`flex items-center gap-2 ${statusConfig.textColor}`}>
                            {planStatus === 'LIFETIME' ? <Crown className="w-4 h-4" /> : <Calendar className="w-4 h-4" />}
                            <span className="text-sm font-medium">{planStatus === 'EXPIRED' ? 'Expired On' : 'Expires On'}</span>
                        </div>
                        <p className="text-base font-bold text-white tracking-wide">
                            {expiryDateObj ? expiryDateObj.toLocaleDateString() : 'Never'}
                        </p>
                    </div>

                    {/* Progress Bar (Visualizing remaining time) */}
                    {planStatus !== 'LIFETIME' && (
                        <div className="space-y-2 pt-4">
                            <div className={`flex justify-between text-xs ${statusConfig.textColor} mb-2`}>
                                <span>Usage Period</span>
                                <span className="text-white font-bold">{Math.round(100 - progressPercent)}% Used</span>
                            </div>
                            <div className="w-full bg-black/30 h-3 rounded-full overflow-hidden border border-white/5 relative mb-4">
                                {/* Width represents TIME LEFT, so decreasing */}
                                <div 
                                    style={{ width: `${progressPercent}%` }} 
                                    className={`bg-gradient-to-r ${statusConfig.barColor} h-full rounded-full shadow-sm transition-all duration-1000 ease-out`}
                                ></div>
                            </div>
                            
                            <div className="flex justify-end pt-1">
                                <p className="text-xs font-bold text-white flex items-center gap-1.5 bg-white/10 px-3 py-1.5 rounded-lg border border-white/5 shadow-sm">
                                    {planStatus === 'EXPIRED' ? <XCircle className="w-3.5 h-3.5" /> : <Hourglass className="w-3.5 h-3.5" />}
                                    <span>{daysRemaining <= 0 ? `${Math.abs(daysRemaining)} Days Ago` : `${daysRemaining} Days Left`}</span>
                                </p>
                            </div>
                            
                            {planStatus === 'WARNING' && (
                                <p className="text-[10px] text-white text-center mt-2 font-medium bg-red-500/20 py-1 rounded">
                                    Please renew to avoid service interruption.
                                </p>
                            )}
                        </div>
                    )}
                    
                    {planStatus === 'LIFETIME' && (
                        <div className="flex-1 flex items-center justify-center pt-4">
                            <div className="text-center text-yellow-100/80">
                                <Crown className="w-8 h-8 mx-auto mb-2 opacity-50" />
                                <p className="text-xs font-medium">Permanent Access</p>
                            </div>
                        </div>
                    )}
                </div>
            </div>
        )}
      </div>

      <div className="md:col-span-2 space-y-6">
        <Card title="General Information">
          <form onSubmit={handleUpdateClick} className="space-y-6">
             <div className="space-y-2">
                 <label className="text-sm font-medium text-slate-700 flex items-center gap-2"><UserIcon className="w-4 h-4 text-slate-400" /> Username</label>
                 <input type="text" value={formData.username} onChange={(e) => setFormData({...formData, username: e.target.value})} className="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none" />
             </div>
             
             <div className="space-y-2">
                 <label className="text-sm font-medium text-slate-700 flex items-center gap-2"><Mail className="w-4 h-4 text-slate-400" /> Email Address</label>
                 <div className="flex gap-2">
                     <input 
                        type="email" 
                        value={formData.email} 
                        onChange={(e) => {
                            setFormData({...formData, email: e.target.value});
                            if (e.target.value === user.email) {
                                setCodeSent(false); // Reset if changed back
                                setVerifyStatus('IDLE');
                            }
                        }} 
                        className={`flex-1 px-3 py-2 border rounded-lg focus:ring-2 outline-none transition-colors ${isEmailChanged ? 'border-indigo-300 bg-indigo-50 focus:ring-indigo-500' : 'border-slate-300 focus:ring-indigo-500'}`} 
                     />
                     {isEmailChanged && (
                         <button 
                            type="button"
                            onClick={handleSendCode}
                            disabled={sendingCode || codeSent}
                            className={`px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-2 transition-all shadow-sm
                                ${codeSent 
                                    ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' 
                                    : 'bg-indigo-600 text-white hover:bg-indigo-700'
                                } disabled:opacity-70 disabled:cursor-not-allowed whitespace-nowrap`}
                         >
                            {sendingCode ? <Loader2 className="w-4 h-4 animate-spin" /> : codeSent ? <Check className="w-4 h-4" /> : <Send className="w-4 h-4" />}
                            {codeSent ? 'Sent' : 'Send Code'}
                         </button>
                     )}
                 </div>
                 {isEmailChanged && !codeSent && (
                     <p className="text-xs text-amber-600 flex items-center gap-1 mt-1"><AlertTriangle className="w-3 h-3" /> Verify new email to save changes.</p>
                 )}
             </div>

             {/* Verification Code Input */}
             {codeSent && isEmailChanged && (
                 <div className={`space-y-2 animate-in fade-in slide-in-from-top-2 p-4 rounded-xl border transition-colors ${
                     verifyStatus === 'SUCCESS' ? 'bg-emerald-50 border-emerald-200' : 
                     verifyStatus === 'ERROR' ? 'bg-red-50 border-red-200' : 
                     'bg-slate-50 border-slate-200'
                 }`}>
                     <label className={`text-sm font-medium flex items-center gap-2 ${
                         verifyStatus === 'SUCCESS' ? 'text-emerald-700' : 
                         verifyStatus === 'ERROR' ? 'text-red-700' : 
                         'text-slate-700'
                     }`}>
                         <ShieldCheck className={`w-4 h-4 ${verifyStatus === 'SUCCESS' ? 'text-emerald-500' : 'text-slate-400'}`} /> 
                         Enter Verification Code
                     </label>
                     
                     <div className="flex flex-col md:flex-row gap-3 items-start md:items-center">
                         <input 
                             type="text" 
                             value={otpInput}
                             onChange={(e) => {
                                 setOtpInput(e.target.value);
                                 setVerifyStatus('IDLE'); // Reset status when user types
                             }}
                             placeholder="000000"
                             maxLength={6}
                             className={`w-full md:w-48 text-center tracking-[0.3em] font-mono font-bold text-lg px-3 py-2 border rounded-lg focus:ring-2 outline-none uppercase bg-white transition-colors ${
                                 verifyStatus === 'ERROR' ? 'border-red-300 focus:ring-red-200 text-red-600' :
                                 verifyStatus === 'SUCCESS' ? 'border-emerald-300 focus:ring-emerald-200 text-emerald-600' :
                                 'border-slate-300 focus:ring-indigo-500 text-slate-800'
                             }`}
                         />
                         
                         <div className="flex-1">
                            {verifyStatus === 'IDLE' && (
                                <p className="text-xs text-slate-500">
                                    We sent a 6-digit code to <span className="font-bold text-indigo-600">{formData.email}</span>.<br/>
                                    <span className="text-[10px] text-slate-400">(Test Code: <span className="font-mono">{TEST_OTP}</span> or {systemOtp})</span>
                                </p>
                            )}
                            {verifyStatus === 'SUCCESS' && (
                                <p className="text-sm font-bold text-emerald-600 flex items-center gap-1 animate-in slide-in-from-left-2">
                                    <CheckCircle className="w-4 h-4" /> Code Confirmed!
                                </p>
                            )}
                            {verifyStatus === 'ERROR' && (
                                <p className="text-sm font-bold text-red-600 flex items-center gap-1 animate-in slide-in-from-left-2">
                                    <XCircle className="w-4 h-4" /> Invalid Code. Try again.
                                </p>
                            )}
                         </div>
                     </div>
                 </div>
             )}

             <div className="pt-4 flex justify-end">
                 <button 
                    type="submit" 
                    disabled={loading} 
                    className={`px-5 py-2 rounded-lg font-medium shadow-sm transition-all flex items-center gap-2 text-white ${
                        verifyStatus === 'SUCCESS' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-indigo-600 hover:bg-indigo-700'
                    }`}
                 >
                     {loading ? (
                         <><Loader2 className="w-4 h-4 animate-spin" /> Saving...</>
                     ) : verifyStatus === 'SUCCESS' ? (
                         <><Check className="w-4 h-4" /> Verified & Saved</>
                     ) : (
                         'Save Changes'
                     )}
                 </button>
             </div>
          </form>
        </Card>

        <Card title="Security">
            <form onSubmit={handlePasswordUpdate} className="space-y-4">
                <div className="space-y-2"><label className="text-sm font-medium text-slate-700 flex items-center gap-2"><Key className="w-4 h-4 text-slate-400" /> Current Password</label><input type="password" value={passData.current} onChange={(e) => setPassData({...passData, current: e.target.value})} className="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none" required /></div>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div className="space-y-2"><label className="text-sm font-medium text-slate-700 flex items-center gap-2"><Lock className="w-4 h-4 text-slate-400" /> New Password</label><input type="password" value={passData.new} onChange={(e) => setPassData({...passData, new: e.target.value})} className="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none" required /></div>
                    <div className="space-y-2"><label className="text-sm font-medium text-slate-700 flex items-center gap-2"><Check className="w-4 h-4 text-slate-400" /> Confirm Password</label><input type="password" value={passData.confirm} onChange={(e) => setPassData({...passData, confirm: e.target.value})} className="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none" required /></div>
                </div>
                <div className="bg-amber-50 text-amber-800 text-xs p-3 rounded-lg flex gap-2 items-start"><AlertTriangle className="w-4 h-4 shrink-0 mt-0.5" /><p>Changing your password will not log you out of other sessions immediately, but you will need to use the new password for future logins.</p></div>
                <div className="pt-2 flex justify-end"><button type="submit" disabled={loading} className="px-5 py-2 bg-slate-900 text-white rounded-lg font-medium shadow-sm hover:bg-slate-800 transition-colors">Change Password</button></div>
            </form>
        </Card>
      </div>
    </div>
  );
};