import React, { useState } from 'react';
import { Card, StatusBadge } from '../../components/Shared';
import { api } from '../../services/api';
import { User, HostingPlan, Site } from '../../types';
import { Globe, HardDrive, Crown, Zap, Server, Cloud, ExternalLink, Trash2, Edit2, Save, X, AlertTriangle } from 'lucide-react';
import { FRAMEWORK_ICONS } from '../../constants';

interface DashboardProps {
  sites: Site[];
  user: User;
  plans: HostingPlan[];
  onRefresh?: () => void;
}

export const UserDashboardHome: React.FC<DashboardProps> = ({ sites = [], user, plans = [], onRefresh }) => {
  const [showAllSites, setShowAllSites] = useState(false);
  const [editingId, setEditingId] = useState<string | null>(null);
  const [editSubdomain, setEditSubdomain] = useState('');
  const [deleteTarget, setDeleteTarget] = useState<{id: string, name: string} | null>(null);

  // Robust safety checks
  const safePlans = Array.isArray(plans) ? plans : [];
  const safeSites = Array.isArray(sites) ? sites : [];
  const safeUser = user || { plan: 'Basic', username: 'Guest' } as User;

  const currentPlan = safePlans.find(p => p.name === safeUser.plan);
  const maxSites = currentPlan?.limits?.sites || 0;
  const maxStorage = currentPlan?.limits?.storage || 0;
  
  const usedSites = safeSites.length;
  const usedStorage = safeSites.reduce((acc, s) => acc + (s.storageUsed || 0), 0);

  const sitesPercentage = maxSites > 0 ? (usedSites / maxSites) * 100 : 0;
  const storagePercentage = maxStorage > 0 ? (usedStorage / maxStorage) * 100 : 0;

  const startEdit = (site: Site) => {
    setEditingId(site.id);
    setEditSubdomain(site.subdomain);
  };

  const cancelEdit = () => {
    setEditingId(null);
    setEditSubdomain('');
  };

  const saveEdit = async (siteId: string) => {
    if (!editSubdomain.trim()) return;
    try {
        await api.sites.update(siteId, { subdomain: editSubdomain.trim() });
        if (onRefresh) onRefresh();
        setEditingId(null);
    } catch (e) {
        alert("Failed to update site url");
    }
  };

  const initiateDelete = (siteId: string, siteName: string) => {
      setDeleteTarget({ id: siteId, name: siteName });
  };

  const confirmDelete = async () => {
      if (!deleteTarget) return;
      try {
          await api.sites.delete(deleteTarget.id);
          if (onRefresh) onRefresh();
          setDeleteTarget(null);
      } catch (e) {
          alert("Failed to delete site");
      }
  };

  return (
    <div className="space-y-6 animate-in fade-in duration-300">
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        {/* Active Sites Card */}
        <div className="bg-gradient-to-br from-blue-600 to-cyan-500 p-6 rounded-xl shadow-lg text-white relative overflow-hidden group">
            <div className="absolute -right-6 -bottom-6 text-white/10 rotate-12 transition-transform group-hover:scale-105 duration-500">
                <Globe className="w-32 h-32" />
            </div>
            <div className="relative z-10 flex items-center justify-between mb-4">
                <h3 className="text-blue-100 font-medium text-sm">Active Sites</h3>
                <div className="p-2 bg-white/20 rounded-lg backdrop-blur-sm border border-white/10">
                    <Server className="w-5 h-5 text-white" />
                </div>
            </div>
            <div className="relative z-10">
                <div className="flex items-baseline gap-1">
                   <span className="text-3xl font-bold tracking-tight">{usedSites}</span>
                   <span className="text-lg text-blue-200">/ {maxSites}</span>
                </div>
                <div className="mt-4 space-y-2">
                    <div className="w-full bg-black/20 h-2 rounded-full overflow-hidden backdrop-blur-sm">
                        <div style={{ width: `${Math.min(sitesPercentage, 100)}%` }} className="bg-white h-full rounded-full transition-all duration-1000 ease-out" />
                    </div>
                    <p className="text-xs text-blue-100 font-medium">{maxSites - usedSites} slots remaining</p>
                </div>
            </div>
        </div>

        {/* Storage Used Card */}
        <div className="bg-gradient-to-br from-emerald-600 to-teal-500 p-6 rounded-xl shadow-lg text-white relative overflow-hidden group">
            <div className="absolute -right-6 -bottom-6 text-white/10 rotate-12 transition-transform group-hover:scale-105 duration-500">
                <HardDrive className="w-32 h-32" />
            </div>
            <div className="relative z-10 flex items-center justify-between mb-4">
                <h3 className="text-emerald-100 font-medium text-sm">Storage Usage</h3>
                <div className="p-2 bg-white/20 rounded-lg backdrop-blur-sm border border-white/10">
                    <Cloud className="w-5 h-5 text-white" />
                </div>
            </div>
            <div className="relative z-10">
                <div className="flex items-baseline gap-1">
                   <span className="text-3xl font-bold tracking-tight">{usedStorage.toFixed(0)}</span>
                   <span className="text-sm font-medium text-emerald-100">MB</span>
                   <span className="text-lg text-emerald-200 ml-1">/ {maxStorage} MB</span>
                </div>
                <div className="mt-4 space-y-2">
                    <div className="w-full bg-black/20 h-2 rounded-full overflow-hidden backdrop-blur-sm">
                        <div style={{ width: `${Math.min(storagePercentage, 100)}%` }} className={`h-full rounded-full transition-all duration-1000 ease-out ${storagePercentage > 90 ? 'bg-red-400' : 'bg-white'}`} />
                    </div>
                    <p className="text-xs text-emerald-100 font-medium">{storagePercentage.toFixed(1)}% usage</p>
                </div>
            </div>
        </div>
        
        {/* Active Plan Card */}
        <div className="bg-gradient-to-br from-violet-600 to-indigo-600 p-6 rounded-xl shadow-lg text-white relative overflow-hidden group">
            <div className="absolute -right-6 -top-6 text-white/10 rotate-12 transition-transform group-hover:scale-105 duration-500">
                <Crown className="w-32 h-32" />
            </div>
            <div className="relative z-10 flex items-center justify-between mb-4">
                <h3 className="text-indigo-100 font-medium text-sm">Current Plan</h3>
                <div className="p-2 bg-white/20 rounded-lg backdrop-blur-sm border border-white/10">
                    <Zap className="w-5 h-5 text-yellow-300 fill-yellow-300" />
                </div>
            </div>
            <div className="relative z-10 mt-auto">
                <div className="text-3xl font-bold tracking-tight">{safeUser.plan}</div>
                <div className="flex items-center gap-1.5 text-indigo-200 text-xs mt-2 font-medium">
                     <span className="relative flex h-2 w-2">
                        <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span className="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
                     </span>
                     Active Subscription
                </div>
                <div className="mt-4 h-6"></div> 
            </div>
        </div>
      </div>

      <Card title="My Sites" action={
          <button 
            onClick={() => setShowAllSites(true)}
            className="text-sm text-indigo-600 hover:text-indigo-800 font-medium transition-colors flex items-center gap-1"
          >
            View All
          </button>
      }>
        <div className="overflow-x-auto">
          <table className="min-w-full text-left text-sm whitespace-nowrap">
            <thead>
              <tr className="border-b border-slate-100 text-slate-500">
                <th className="px-4 py-3 font-medium">Site Name</th>
                <th className="px-4 py-3 font-medium">Framework</th>
                <th className="px-4 py-3 font-medium">Subdomain</th>
                <th className="px-4 py-3 font-medium">Status</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-50">
              {safeSites.length > 0 ? safeSites.slice(0, 5).map(site => (
                  <tr key={site.id}>
                      <td className="px-4 py-3 font-medium text-slate-900">{site.name}</td>
                      <td className="px-4 py-3 text-slate-600">{site.framework}</td>
                      <td className="px-4 py-3 text-slate-500">{site.subdomain}</td>
                      <td className="px-4 py-3"><StatusBadge status={site.status} /></td>
                  </tr>
              )) : (
                <tr>
                   <td colSpan={4} className="px-4 py-6 text-center text-slate-500 italic">No sites found. Click "New Site" to deploy.</td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      </Card>

      {/* All Sites Modal */}
      {showAllSites && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
           <div className="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onClick={() => setShowAllSites(false)} />
           <div className="relative w-full max-w-5xl bg-white rounded-2xl shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-200 flex flex-col max-h-[85vh]">
              <div className="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                 <div>
                    <h3 className="text-xl font-bold text-slate-900">All Active Sites</h3>
                    <p className="text-sm text-slate-500">Manage, edit, and view all your deployed applications.</p>
                 </div>
                 <button onClick={() => setShowAllSites(false)} className="p-2 hover:bg-slate-200 text-slate-500 hover:text-slate-800 rounded-full transition-colors"><X className="w-5 h-5" /></button>
              </div>
              <div className="flex-1 overflow-y-auto p-0">
                 <table className="min-w-full text-left text-sm">
                    <thead className="bg-slate-50 text-slate-500 border-b border-slate-200 sticky top-0 z-10 shadow-sm">
                       <tr>
                          <th className="px-6 py-4 font-medium">Application</th>
                          <th className="px-6 py-4 font-medium">URL (Subdomain)</th>
                          <th className="px-6 py-4 font-medium">Created At</th>
                          <th className="px-6 py-4 font-medium">Storage</th>
                          <th className="px-6 py-4 font-medium">Status</th>
                          <th className="px-6 py-4 font-medium text-right">Actions</th>
                       </tr>
                    </thead>
                    <tbody className="divide-y divide-slate-100">
                       {safeSites.map(site => (
                          <tr key={site.id} className="hover:bg-slate-50 transition-colors group">
                             <td className="px-6 py-4">
                                <div className="flex items-center gap-3">
                                   <div className={`p-2 rounded-lg bg-white border border-slate-200 shadow-sm ${FRAMEWORK_ICONS[site.framework] || 'text-slate-500'}`}>
                                      <Globe className="w-5 h-5" />
                                   </div>
                                   <div>
                                      <div className="font-semibold text-slate-900">{site.name}</div>
                                      <div className="text-xs text-slate-500">{site.framework}</div>
                                   </div>
                                </div>
                             </td>
                             <td className="px-6 py-4">
                                {editingId === site.id ? (
                                    <div className="flex items-center">
                                        <input type="text" value={editSubdomain} onChange={(e) => setEditSubdomain(e.target.value)} className="px-2 py-1 text-xs border border-indigo-300 rounded focus:ring-2 focus:ring-indigo-200 outline-none w-24" autoFocus />
                                        <span className="text-xs text-slate-500 ml-1">.kolabpanel.com</span>
                                    </div>
                                ) : (
                                    <span className="text-indigo-600 font-mono text-xs bg-indigo-50 px-2 py-1 rounded">{site.subdomain}.kolabpanel.com</span>
                                )}
                             </td>
                             <td className="px-6 py-4 text-slate-600">{new Date(site.createdAt).toLocaleDateString()}</td>
                             <td className="px-6 py-4 text-slate-600">{(site.storageUsed || 0).toFixed(2)} MB</td>
                             <td className="px-6 py-4"><StatusBadge status={site.status} /></td>
                             <td className="px-6 py-4 text-right">
                                <div className="flex justify-end items-center gap-2">
                                   {editingId === site.id ? (
                                       <>
                                           <button onClick={() => saveEdit(site.id)} className="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded transition-colors" title="Save"><Save className="w-4 h-4" /></button>
                                           <button onClick={cancelEdit} className="p-1.5 text-slate-400 hover:bg-slate-100 rounded transition-colors" title="Cancel"><X className="w-4 h-4" /></button>
                                       </>
                                   ) : (
                                       <>
                                           <button className="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded transition-colors" title="Edit URL" onClick={() => startEdit(site)}><Edit2 className="w-4 h-4" /></button>
                                           <button className="p-1.5 text-red-500 hover:bg-red-50 rounded transition-colors" title="Delete Site" onClick={() => initiateDelete(site.id, site.name)}><Trash2 className="w-4 h-4" /></button>
                                           <button className="inline-flex items-center gap-1 text-xs font-medium text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-full transition-colors"><ExternalLink className="w-3 h-3" /> Visit</button>
                                       </>
                                   )}
                                </div>
                             </td>
                          </tr>
                       ))}
                       {safeSites.length === 0 && (
                          <tr><td colSpan={6} className="px-6 py-12 text-center text-slate-500"><Server className="w-12 h-12 text-slate-300 mx-auto mb-3" /><p>No sites deployed yet.</p></td></tr>
                       )}
                    </tbody>
                 </table>
              </div>
              <div className="px-6 py-4 border-t border-slate-100 bg-slate-50 flex justify-between items-center text-xs text-slate-500">
                  <span>Showing {safeSites.length} total sites</span>
                  <span>Limits: {usedSites} / {maxSites} used</span>
              </div>
           </div>
        </div>
      )}

      {/* Delete Confirmation Modal */}
      {deleteTarget && (
        <div className="fixed inset-0 z-[60] flex items-center justify-center p-4">
            <div className="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onClick={() => setDeleteTarget(null)} />
            <div className="relative w-full max-w-md bg-white rounded-xl shadow-2xl p-6">
                <div className="flex items-start gap-4">
                    <div className="p-3 bg-red-100 rounded-full shrink-0"><AlertTriangle className="w-6 h-6 text-red-600" /></div>
                    <div>
                        <h3 className="text-lg font-bold text-slate-900">Delete Site?</h3>
                        <p className="text-sm text-slate-500 mt-1">Are you sure you want to delete <span className="font-bold text-slate-800">{deleteTarget.name}</span>? This action will permanently remove all files and databases associated with this site.</p>
                    </div>
                </div>
                <div className="mt-6 flex justify-end gap-3">
                    <button onClick={() => setDeleteTarget(null)} className="px-4 py-2 text-slate-600 hover:bg-slate-50 rounded-lg font-medium text-sm transition-colors">Cancel</button>
                    <button onClick={confirmDelete} className="px-4 py-2 bg-red-600 text-white rounded-lg font-medium text-sm hover:bg-red-700 shadow-sm transition-colors">Delete Site</button>
                </div>
            </div>
        </div>
      )}
    </div>
  );
};
