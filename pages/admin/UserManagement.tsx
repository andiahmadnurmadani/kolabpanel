import React, { useState, useEffect } from 'react';
import { Card, StatusBadge } from '../../components/Shared';
import { api } from '../../services/api';
import { User, UserRole, Site } from '../../types';
import { Search, Eye, Ban, Shield, X, Activity } from 'lucide-react';
import { FRAMEWORK_ICONS } from '../../constants';

export const UserManagement: React.FC = () => {
  const [users, setUsers] = useState<User[]>([]);
  const [selectedUserId, setSelectedUserId] = useState<string | null>(null);
  const [selectedUserSites, setSelectedUserSites] = useState<Site[]>([]);

  useEffect(() => {
      api.admin.getUsers().then(setUsers);
  }, []);

  useEffect(() => {
      if (selectedUserId) {
          api.sites.list(selectedUserId).then(setSelectedUserSites);
      }
  }, [selectedUserId]);

  const toggleStatus = async (id: string) => {
    try {
        await api.admin.toggleUserStatus(id);
        setUsers(users.map(u => 
          u.id === id 
            ? { ...u, status: u.status === 'ACTIVE' ? 'SUSPENDED' : 'ACTIVE' } 
            : u
        ));
    } catch(e) {
        alert("Failed to update status");
    }
  };

  const selectedUser = users.find(u => u.id === selectedUserId);

  return (
    <>
      <div className="space-y-6 animate-in fade-in duration-300">
        <div className="flex justify-between items-center">
          <h2 className="text-xl font-bold text-slate-800">User Management</h2>
          <div className="relative">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
            <input 
              type="text" 
              placeholder="Search users..." 
              className="pl-10 pr-4 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none w-64"
            />
          </div>
        </div>

        <Card>
          <div className="overflow-x-auto">
            <table className="min-w-full text-left text-sm">
              <thead className="bg-slate-50 text-slate-500 border-b border-slate-200">
                <tr>
                  <th className="px-6 py-4 font-medium">User Profile</th>
                  <th className="px-6 py-4 font-medium">Role</th>
                  <th className="px-6 py-4 font-medium">Plan</th>
                  <th className="px-6 py-4 font-medium">Status</th>
                  <th className="px-6 py-4 font-medium text-right">Actions</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-100">
                {users.map((user) => (
                  <tr key={user.id} className="hover:bg-slate-50/50 transition-colors">
                    <td className="px-6 py-4">
                      <div className="flex items-center gap-3">
                        <img src={user.avatar} alt="" className="w-10 h-10 rounded-full bg-slate-200 object-cover" />
                        <div>
                          <div className="font-semibold text-slate-900">{user.username}</div>
                          <div className="text-slate-500 text-xs">{user.email}</div>
                        </div>
                      </div>
                    </td>
                    <td className="px-6 py-4">
                      <span className={`inline-flex items-center px-2 py-1 rounded text-xs font-medium ${user.role === UserRole.ADMIN ? 'bg-purple-100 text-purple-700' : 'bg-slate-100 text-slate-600'}`}>
                        {user.role}
                      </span>
                    </td>
                    <td className="px-6 py-4">
                      <span className="font-medium text-slate-700">{user.plan}</span>
                    </td>
                    <td className="px-6 py-4">
                      <StatusBadge status={user.status} />
                    </td>
                    <td className="px-6 py-4">
                      <div className="flex justify-end gap-2">
                        <button 
                          onClick={() => setSelectedUserId(user.id)}
                          className="p-2 text-slate-600 hover:bg-slate-100 rounded-lg transition-colors"
                          title="View Details"
                        >
                          <Eye className="w-4 h-4" />
                        </button>
                        <button 
                          onClick={() => toggleStatus(user.id)}
                          className={`p-2 rounded-lg transition-colors ${user.status === 'ACTIVE' ? 'text-rose-600 hover:bg-rose-50' : 'text-emerald-600 hover:bg-emerald-50'}`}
                          title={user.status === 'ACTIVE' ? 'Suspend User' : 'Activate User'}
                        >
                          {user.status === 'ACTIVE' ? <Ban className="w-4 h-4" /> : <Shield className="w-4 h-4" />}
                        </button>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </Card>
      </div>

      {/* User Details Modal */}
      {selectedUser && (
        <div className="fixed inset-0 z-[60] flex items-center justify-center p-4">
          <div className="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" onClick={() => setSelectedUserId(null)}></div>
          <div className="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full overflow-hidden animate-in fade-in zoom-in-95 duration-200">
            <div className="flex items-center justify-between p-6 border-b border-slate-100">
              <h3 className="text-lg font-bold text-slate-800">User Details</h3>
              <button onClick={() => setSelectedUserId(null)} className="p-2 hover:bg-slate-100 rounded-full text-slate-500">
                <X className="w-5 h-5" />
              </button>
            </div>
            
            <div className="p-6 space-y-6">
              <div className="flex items-center gap-4">
                 <img src={selectedUser.avatar} className="w-16 h-16 rounded-full border-2 border-slate-100" />
                 <div>
                    <h4 className="text-xl font-bold text-slate-900">{selectedUser.username}</h4>
                    <p className="text-slate-500">{selectedUser.email}</p>
                 </div>
                 <div className="ml-auto flex flex-col items-end gap-2">
                    <StatusBadge status={selectedUser.status} />
                    <span className="text-sm font-medium text-slate-600">Plan: {selectedUser.plan}</span>
                 </div>
              </div>

              <div>
                <h4 className="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                  <Activity className="w-4 h-4" /> Owned Sites
                </h4>
                {selectedUserSites.length > 0 ? (
                  <div className="grid gap-3">
                    {selectedUserSites.map(site => (
                      <div key={site.id} className="flex items-center justify-between p-4 border border-slate-200 rounded-xl bg-slate-50/50">
                        <div className="flex items-center gap-3">
                           <div className={`p-2 rounded-lg bg-white shadow-sm border border-slate-100 ${FRAMEWORK_ICONS[site.framework]}`}>
                              <span className="text-xl">●</span>
                           </div>
                           <div>
                             <div className="font-semibold text-slate-900">{site.name}</div>
                             <div className="text-xs text-slate-500">{site.subdomain}.kolabpanel.com</div>
                           </div>
                        </div>
                        <StatusBadge status={site.status} />
                      </div>
                    ))}
                  </div>
                ) : (
                  <div className="text-center py-8 bg-slate-50 rounded-xl border border-dashed border-slate-200 text-slate-500">
                    No sites deployed yet.
                  </div>
                )}
              </div>
            </div>
          </div>
        </div>
      )}
    </>
  );
};
