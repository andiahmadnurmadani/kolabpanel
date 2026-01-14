import React, { useState } from 'react';
import { Card } from '../../components/Shared';
import { api } from '../../services/api';
import { User } from '../../types';
import { CreditCard, Calendar, User as UserIcon, Mail, Key, Lock, Check, AlertTriangle } from 'lucide-react';

interface UserProfileProps {
  user: User;
}

export const UserProfile: React.FC<UserProfileProps> = ({ user }) => {
  const [loading, setLoading] = useState(false);
  const [formData, setFormData] = useState({ username: user.username, email: user.email });
  const [passData, setPassData] = useState({ current: '', new: '', confirm: '' });

  const nextBillingDate = new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });

  const handleUpdate = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    try {
        await api.auth.updateProfile(user.id, formData);
        alert("Profile updated successfully!");
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
    <div className="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6">
      <div className="space-y-6">
        <div className="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex flex-col items-center text-center">
          <div className="relative mb-4 group"><div className="w-32 h-32 rounded-full overflow-hidden border-4 border-slate-100 shadow-inner"><img src={user.avatar} alt={user.username} className="w-full h-full object-cover" /></div></div>
          <h2 className="text-xl font-bold text-slate-900">{user.username}</h2>
          <p className="text-sm text-slate-500 mb-4">{user.email}</p>
        </div>
        <div className="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
          <h3 className="font-bold text-slate-900 mb-4 flex items-center gap-2"><CreditCard className="w-4 h-4 text-indigo-600" /> Subscription</h3>
          <div className="space-y-4">
              <div className="p-3 bg-indigo-50 border border-indigo-100 rounded-lg">
                  <p className="text-xs text-indigo-600 font-bold uppercase tracking-wider mb-1">Current Plan</p>
                  <div className="flex items-center justify-between"><span className="text-lg font-bold text-indigo-900">{user.plan}</span><span className="px-2 py-0.5 bg-white text-indigo-600 text-xs font-bold rounded shadow-sm border border-indigo-100">Active</span></div>
              </div>
              <div className="space-y-3 pt-2">
                  <div className="flex justify-between text-sm"><span className="text-slate-500 flex items-center gap-2"><Calendar className="w-3.5 h-3.5" /> Renew Date</span></div>
                  <div className="font-medium text-slate-700 bg-slate-50 p-2 rounded text-sm text-center">{nextBillingDate}</div>
              </div>
          </div>
        </div>
      </div>

      <div className="md:col-span-2 space-y-6">
        <Card title="General Information">
          <form onSubmit={handleUpdate} className="space-y-4">
             <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
               <div className="space-y-2"><label className="text-sm font-medium text-slate-700 flex items-center gap-2"><UserIcon className="w-4 h-4 text-slate-400" /> Username</label><input type="text" value={formData.username} onChange={(e) => setFormData({...formData, username: e.target.value})} className="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none" /></div>
               <div className="space-y-2"><label className="text-sm font-medium text-slate-700 flex items-center gap-2"><Mail className="w-4 h-4 text-slate-400" /> Email</label><input type="email" value={formData.email} onChange={(e) => setFormData({...formData, email: e.target.value})} className="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none" /></div>
             </div>
             <div className="pt-4 flex justify-end"><button type="submit" disabled={loading} className="px-5 py-2 bg-indigo-600 text-white rounded-lg font-medium shadow-sm hover:bg-indigo-700 transition-colors flex items-center gap-2">{loading ? 'Saving...' : 'Save Changes'}</button></div>
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
