import React, { useState } from 'react';
import { Card } from '../../components/Shared';
import { api } from '../../services/api';
import { User } from '../../types';
import { CreditCard, Calendar, User as UserIcon, Mail, Key, Lock, Check, AlertTriangle, ShieldCheck, Send, Loader2, CheckCircle, XCircle, Crown, Zap, Clock } from 'lucide-react';

interface UserProfileProps {
  user: User;
}

export const UserProfile: React.FC<UserProfileProps> = ({ user }) => {
  const [loading, setLoading] = useState(false);
  const [formData, setFormData] = useState({ username: user.username, email: user.email });
  const [passData, setPassData] = useState({ current: '', new: '', confirm: '' });

  // Verification State
  const [otpInput, setOtpInput] = useState('');
  const [systemOtp, setSystemOtp] = useState('');
  const [codeSent, setCodeSent] = useState(false);
  const [sendingCode, setSendingCode] = useState(false);
  const [verifyStatus, setVerifyStatus] = useState<'IDLE' | 'SUCCESS' | 'ERROR'>('IDLE');

  const TEST_OTP = '123456'; // Master code for testing

  const nextBillingDate = new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
  
  const isEmailChanged = formData.email !== user.email;

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
      
      console.log(`[SYSTEM] Verification Code sent to ${formData.email}: ${code}`);
      // Inform user about the test code
      alert(`[SIMULATION] Code sent to ${formData.email}: ${code}.\n\n(Tip: For testing, you can also use the code '${TEST_OTP}' to verify successfully)`);
  };

  const handleUpdateClick = async (e: React.FormEvent) => {
    e.preventDefault();
    setVerifyStatus('IDLE');
    
    // Check if email has changed
    if (isEmailChanged) {
        if (!codeSent) {
            alert("Please click 'Send Code' to verify your new email address.");
            return;
        }

        // Validate Code (System generated OR Master Test Code)
        const isValid = otpInput === systemOtp || otpInput === TEST_OTP;

        if (!isValid) {
            setVerifyStatus('ERROR');
            return;
        }

        // If valid, show success state briefly before saving
        setVerifyStatus('SUCCESS');
        await new Promise(r => setTimeout(r, 800)); // Small delay to show success tick
    }

    // Proceed with update
    await saveProfile(formData);
    
    // Reset states after successful save
    setCodeSent(false);
    setSystemOtp('');
    setOtpInput('');
    setVerifyStatus('IDLE');
  };

  const saveProfile = async (data: typeof formData) => {
    setLoading(true);
    try {
        await api.auth.updateProfile(user.id, data);
        // alert("Profile updated successfully!"); // Optional: reduced alerts for smoother UX
    } catch (e) {
        alert("Failed to update profile");
    } finally {
        setLoading(false);
    }
  };

  const handlePasswordUpdate = async (e: React.FormEvent) => {
    e.preventDefault();
    if (passData.new !== passData.confirm) {
        alert("New passwords do not match");
        return;
    }
    if (passData.new.length < 6) {
        alert("Password must be at least 6 characters");
        return;
    }
    setLoading(true);
    try {
        await api.auth.changePassword(user.id, passData.current, passData.new);
        alert("Password changed successfully");
        setPassData({ current: '', new: '', confirm: '' });
    } catch (err: any) {
        alert(err.message || "Failed to change password");
    } finally {
        setLoading(false);
    }
  };

  return (
    <div className="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6 relative">
      <div className="space-y-6">
        <div className="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex flex-col items-center text-center">
          <div className="relative mb-4 group"><div className="w-32 h-32 rounded-full overflow-hidden border-4 border-slate-100 shadow-inner"><img src={user.avatar} alt={user.username} className="w-full h-full object-cover" /></div></div>
          <h2 className="text-xl font-bold text-slate-900">{user.username}</h2>
          <p className="text-sm text-slate-500 mb-4">{user.email}</p>
        </div>
        
        {/* PREMIUM SUBSCRIPTION CARD */}
        <div className="bg-gradient-to-br from-indigo-600 to-violet-700 rounded-xl shadow-lg text-white relative overflow-hidden p-6 transition-all hover:shadow-xl hover:scale-[1.01]">
            {/* Background Decoration */}
            <div className="absolute -right-6 -top-6 text-white/10">
                <Crown className="w-32 h-32 rotate-12" />
            </div>
            <div className="absolute bottom-0 left-0 w-full h-24 bg-gradient-to-t from-black/20 to-transparent pointer-events-none"></div>

            {/* Header */}
            <div className="relative z-10 flex items-center justify-between mb-6">
                <div className="flex items-center gap-3">
                    <div className="p-2 bg-white/20 rounded-lg backdrop-blur-sm shadow-inner border border-white/10">
                        <Zap className="w-5 h-5 text-yellow-300 fill-yellow-300" />
                    </div>
                    <h3 className="font-bold text-lg tracking-wide">Subscription</h3>
                </div>
                <span className="px-2.5 py-1 bg-emerald-500/20 text-emerald-100 border border-emerald-500/30 rounded-full text-xs font-bold flex items-center gap-1.5 shadow-sm backdrop-blur-md">
                    <span className="relative flex h-2 w-2">
                        <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span className="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
                    </span>
                    Active
                </span>
            </div>

            {/* Plan Info */}
            <div className="relative z-10 mb-6">
                 <p className="text-indigo-200 text-xs font-bold uppercase tracking-wider mb-1">Current Plan</p>
                 <div className="text-3xl font-extrabold tracking-tight text-white drop-shadow-sm">{user.plan}</div>
            </div>

            {/* Billing Info & Progress */}
            <div className="relative z-10 bg-black/20 rounded-xl p-4 backdrop-blur-md border border-white/10 shadow-inner">
                <div className="flex justify-between items-end mb-3">
                    <div>
                         <p className="text-xs text-indigo-200 mb-1 flex items-center gap-1.5 font-medium"><Calendar className="w-3.5 h-3.5" /> Next Billing</p>
                         <p className="text-sm font-bold text-white tracking-wide">{nextBillingDate}</p>
                    </div>
                    <div className="text-right">
                         <p className="text-xs text-indigo-200 mb-1 flex items-center gap-1.5 justify-end font-medium"><Clock className="w-3.5 h-3.5" /> Remaining</p>
                         <p className="text-sm font-bold text-white">30 Days</p>
                    </div>
                </div>
                {/* Visual Progress Bar */}
                <div className="w-full bg-black/30 h-2 rounded-full overflow-hidden border border-white/5">
                    <div className="bg-gradient-to-r from-emerald-400 to-teal-300 h-full w-[5%] rounded-full shadow-[0_0_10px_rgba(52,211,153,0.5)]"></div>
                </div>
            </div>
        </div>
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
                         
                         {/* Dynamic Feedback Message */}
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