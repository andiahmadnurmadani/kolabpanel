import React, { useState, useEffect } from 'react';
import { Card } from '../../components/Shared';
import { api } from '../../services/api';
import { TunnelRoute } from '../../types';
import { Network, Plus, RefreshCw, Trash2, Edit2, Save, X, Loader2, Globe, Server, Search, AlertTriangle, ArrowUpDown, ChevronUp, ChevronDown } from 'lucide-react';

export const TunnelManager: React.FC = () => {
  const [tunnels, setTunnels] = useState<TunnelRoute[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [searchQuery, setSearchQuery] = useState('');
  
  // Sort State
  const [sortConfig, setSortConfig] = useState<{ key: keyof TunnelRoute; direction: 'asc' | 'desc' } | null>(null);
  
  // Modal State
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [modalMode, setModalMode] = useState<'CREATE' | 'EDIT'>('CREATE');
  const [currentHostname, setCurrentHostname] = useState(''); // Only used for finding item in edit mode
  const [formData, setFormData] = useState({ hostname: '', service: 'http://127.0.0.1:' });
  const [isSubmitting, setIsSubmitting] = useState(false);

  // Delete State
  const [tunnelToDelete, setTunnelToDelete] = useState<TunnelRoute | null>(null);
  const [isDeleting, setIsDeleting] = useState(false);

  useEffect(() => {
    loadTunnels();
  }, []);

  const loadTunnels = async () => {
    try {
      setLoading(true);
      const data = await api.admin.tunnels.list();
      setTunnels(data);
    } catch (e) {
      console.error("Failed to load tunnels", e);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  const handleRefresh = () => {
    setRefreshing(true);
    loadTunnels();
  };

  const handleSort = (key: keyof TunnelRoute) => {
    setSortConfig(current => {
      if (current?.key === key) {
        return { key, direction: current.direction === 'asc' ? 'desc' : 'asc' };
      }
      return { key, direction: 'asc' };
    });
  };

  const openCreateModal = () => {
    setModalMode('CREATE');
    setFormData({ hostname: '', service: 'http://127.0.0.1:' });
    setIsModalOpen(true);
  };

  const openEditModal = (tunnel: TunnelRoute) => {
    setModalMode('EDIT');
    setCurrentHostname(tunnel.hostname);
    setFormData({ hostname: tunnel.hostname, service: tunnel.service });
    setIsModalOpen(true);
  };

  const initiateDelete = (tunnel: TunnelRoute) => {
    setTunnelToDelete(tunnel);
  };

  const confirmDelete = async () => {
    if (!tunnelToDelete) return;
    setIsDeleting(true);
    try {
      await api.admin.tunnels.delete(tunnelToDelete.hostname);
      setTunnels(tunnels.filter(t => t.hostname !== tunnelToDelete.hostname));
      setTunnelToDelete(null);
    } catch (e) {
      alert("Failed to delete tunnel");
    } finally {
      setIsDeleting(false);
    }
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsSubmitting(true);
    try {
      if (modalMode === 'CREATE') {
        await api.admin.tunnels.create(formData.hostname, formData.service);
      } else {
        await api.admin.tunnels.edit(currentHostname, formData.hostname, formData.service);
      }
      setIsModalOpen(false);
      loadTunnels(); // Refresh list
    } catch (e) {
      alert(`Failed to ${modalMode === 'CREATE' ? 'create' : 'update'} tunnel`);
    } finally {
      setIsSubmitting(false);
    }
  };

  // Filter & Sort logic
  const processedTunnels = [...tunnels]
    .filter(tunnel => 
      tunnel.hostname.toLowerCase().includes(searchQuery.toLowerCase()) || 
      tunnel.service.toLowerCase().includes(searchQuery.toLowerCase())
    )
    .sort((a, b) => {
      if (!sortConfig) return 0;
      
      const valA = a[sortConfig.key].toLowerCase();
      const valB = b[sortConfig.key].toLowerCase();

      if (valA < valB) return sortConfig.direction === 'asc' ? -1 : 1;
      if (valA > valB) return sortConfig.direction === 'asc' ? 1 : -1;
      return 0;
    });

  return (
    <div className="space-y-6 animate-in fade-in duration-300">
      <div className="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
           <h2 className="text-xl font-bold text-slate-800 flex items-center gap-2">
             <Network className="w-6 h-6 text-indigo-600" /> Cloudflare Tunnels
           </h2>
           <p className="text-sm text-slate-500 mt-1">Manage ingress rules and external routing.</p>
        </div>
        
        <div className="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            {/* Search Bar */}
            <div className="relative">
                <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
                <input 
                    type="text" 
                    placeholder="Search hostname or service..." 
                    value={searchQuery}
                    onChange={(e) => setSearchQuery(e.target.value)}
                    className="pl-9 pr-4 py-2 w-full sm:w-64 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none bg-white"
                />
            </div>

            <div className="flex gap-2">
                <button 
                    onClick={handleRefresh} 
                    className="p-2 bg-white border border-slate-200 text-slate-600 rounded-lg hover:bg-slate-50 hover:text-indigo-600 transition-colors shadow-sm"
                    title="Refresh List"
                >
                    <RefreshCw className={`w-5 h-5 ${refreshing ? 'animate-spin' : ''}`} />
                </button>
                <button 
                    onClick={openCreateModal} 
                    className="px-4 py-2 bg-indigo-600 text-white rounded-lg font-medium shadow-sm hover:bg-indigo-700 flex items-center gap-2 whitespace-nowrap"
                >
                    <Plus className="w-4 h-4" /> Add Route
                </button>
            </div>
        </div>
      </div>

      <Card>
         {loading && !refreshing ? (
             <div className="py-12 flex justify-center">
                 <Loader2 className="w-8 h-8 text-indigo-600 animate-spin" />
             </div>
         ) : (
            <div className="overflow-x-auto">
                <table className="min-w-full text-left text-sm">
                    <thead className="bg-slate-50 text-slate-500 border-b border-slate-200">
                    <tr>
                        <th 
                            className="px-6 py-3 font-medium cursor-pointer hover:bg-slate-100 transition-colors group select-none"
                            onClick={() => handleSort('hostname')}
                        >
                            <div className="flex items-center gap-2">
                                Hostname (Public)
                                {sortConfig?.key === 'hostname' ? (
                                    sortConfig.direction === 'asc' ? <ChevronUp className="w-4 h-4 text-indigo-600" /> : <ChevronDown className="w-4 h-4 text-indigo-600" />
                                ) : (
                                    <ArrowUpDown className="w-4 h-4 text-slate-300 group-hover:text-slate-500" />
                                )}
                            </div>
                        </th>
                        <th 
                            className="px-6 py-3 font-medium cursor-pointer hover:bg-slate-100 transition-colors group select-none"
                            onClick={() => handleSort('service')}
                        >
                            <div className="flex items-center gap-2">
                                Internal Service
                                {sortConfig?.key === 'service' ? (
                                    sortConfig.direction === 'asc' ? <ChevronUp className="w-4 h-4 text-indigo-600" /> : <ChevronDown className="w-4 h-4 text-indigo-600" />
                                ) : (
                                    <ArrowUpDown className="w-4 h-4 text-slate-300 group-hover:text-slate-500" />
                                )}
                            </div>
                        </th>
                        <th className="px-6 py-3 font-medium text-right">Actions</th>
                    </tr>
                    </thead>
                    <tbody className="divide-y divide-slate-100">
                    {processedTunnels.map((tunnel, idx) => (
                        <tr key={idx} className="hover:bg-slate-50/50 group">
                            <td className="px-6 py-4 font-medium text-slate-800">
                                <div className="flex items-center gap-2">
                                    <Globe className="w-4 h-4 text-slate-400" />
                                    {tunnel.hostname}
                                </div>
                            </td>
                            <td className="px-6 py-4 font-mono text-slate-600">
                                <div className="flex items-center gap-2">
                                    <Server className="w-4 h-4 text-slate-400" />
                                    {tunnel.service}
                                </div>
                            </td>
                            <td className="px-6 py-4 text-right">
                                <div className="flex justify-end gap-2 opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity">
                                    <button onClick={() => openEditModal(tunnel)} className="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Edit">
                                        <Edit2 className="w-4 h-4" />
                                    </button>
                                    <button onClick={() => initiateDelete(tunnel)} className="p-2 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Delete">
                                        <Trash2 className="w-4 h-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    ))}
                    {processedTunnels.length === 0 && (
                        <tr>
                            <td colSpan={3} className="px-6 py-12 text-center text-slate-500 italic">
                                {tunnels.length === 0 ? "No active tunnel routes found." : "No routes match your search."}
                            </td>
                        </tr>
                    )}
                    </tbody>
                </table>
            </div>
         )}
      </Card>

      {/* Create/Edit Modal */}
      {isModalOpen && (
          <div className="fixed inset-0 z-[70] flex items-center justify-center p-4">
              <div className="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onClick={() => setIsModalOpen(false)} />
              <div className="relative w-full max-w-md bg-white rounded-xl shadow-2xl p-6 animate-in fade-in zoom-in-95 duration-200">
                  <div className="flex justify-between items-center mb-6">
                      <h3 className="text-lg font-bold text-slate-900">{modalMode === 'CREATE' ? 'Create New Route' : 'Edit Route'}</h3>
                      <button onClick={() => setIsModalOpen(false)} className="text-slate-400 hover:text-slate-600"><X className="w-5 h-5" /></button>
                  </div>
                  
                  <form onSubmit={handleSubmit} className="space-y-4">
                      <div className="space-y-2">
                          <label className="text-sm font-medium text-slate-700">Hostname</label>
                          <input 
                              type="text" 
                              required
                              value={formData.hostname} 
                              onChange={(e) => setFormData({...formData, hostname: e.target.value})}
                              placeholder="api.domain.kolab.top"
                              className="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"
                          />
                      </div>
                      <div className="space-y-2">
                          <label className="text-sm font-medium text-slate-700">Internal Service</label>
                          <input 
                              type="text" 
                              required
                              value={formData.service} 
                              onChange={(e) => setFormData({...formData, service: e.target.value})}
                              placeholder="http://127.0.0.1:9000"
                              className="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none font-mono text-sm"
                          />
                          <p className="text-xs text-slate-500">Usually points to a localhost port (e.g., http://127.0.0.1:3000)</p>
                      </div>

                      <div className="pt-4 flex justify-end gap-3">
                          <button 
                             type="button" 
                             onClick={() => setIsModalOpen(false)} 
                             className="px-4 py-2 text-slate-600 hover:bg-slate-50 rounded-lg font-medium text-sm transition-colors"
                          >
                              Cancel
                          </button>
                          <button 
                              type="submit" 
                              disabled={isSubmitting}
                              className="px-4 py-2 bg-indigo-600 text-white rounded-lg font-medium text-sm shadow-sm hover:bg-indigo-700 transition-colors flex items-center gap-2 disabled:opacity-50"
                          >
                              {isSubmitting && <Loader2 className="w-4 h-4 animate-spin" />}
                              {modalMode === 'CREATE' ? 'Create Route' : 'Save Changes'}
                          </button>
                      </div>
                  </form>
              </div>
          </div>
      )}

      {/* Delete Confirmation Modal */}
      {tunnelToDelete && (
          <div className="fixed inset-0 z-[80] flex items-center justify-center p-4">
              <div className="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onClick={() => !isDeleting && setTunnelToDelete(null)} />
              <div className="relative w-full max-w-sm bg-white rounded-xl shadow-2xl p-6 animate-in fade-in zoom-in-95 duration-200">
                  <div className="flex items-start gap-4">
                      <div className="p-3 bg-red-100 rounded-full shrink-0">
                          <AlertTriangle className="w-6 h-6 text-red-600" />
                      </div>
                      <div>
                          <h3 className="text-lg font-bold text-slate-900">Delete Route?</h3>
                          <p className="text-sm text-slate-500 mt-1">
                              Are you sure you want to delete the route for <span className="font-bold text-slate-800">{tunnelToDelete.hostname}</span>?
                          </p>
                          <p className="text-xs text-red-600 mt-2 bg-red-50 p-2 rounded border border-red-100">
                              External traffic to this hostname will no longer be forwarded.
                          </p>
                      </div>
                  </div>
                  <div className="mt-6 flex justify-end gap-3">
                      <button 
                          onClick={() => setTunnelToDelete(null)} 
                          disabled={isDeleting}
                          className="px-4 py-2 text-slate-600 hover:bg-slate-50 rounded-lg font-medium text-sm transition-colors"
                      >
                          Cancel
                      </button>
                      <button 
                          onClick={confirmDelete} 
                          disabled={isDeleting}
                          className="px-4 py-2 bg-red-600 text-white rounded-lg font-medium text-sm hover:bg-red-700 shadow-sm transition-colors flex items-center gap-2"
                      >
                          {isDeleting && <Loader2 className="w-4 h-4 animate-spin" />}
                          Delete
                      </button>
                  </div>
              </div>
          </div>
      )}
    </div>
  );
};