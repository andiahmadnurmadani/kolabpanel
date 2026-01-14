import React, { useState, useEffect } from 'react';
import { Card } from '../../components/Shared';
import { api } from '../../services/api';
import { FileCode, Settings, RefreshCw, Plus, Trash2, Edit2, Save, X, Loader2, AlertTriangle, CheckCircle2, Network, CheckCircle, AlertOctagon } from 'lucide-react';

export const ApacheManager: React.FC = () => {
  const [activeTab, setActiveTab] = useState<'SITES' | 'HTTPD'>('SITES');
  const [loading, setLoading] = useState(true);
  const [reloading, setReloading] = useState(false);
  
  // Data State
  const [sites, setSites] = useState<string[]>([]);
  const [httpdContent, setHttpdContent] = useState('');
  const [quickPort, setQuickPort] = useState('');
  
  // Editor State
  const [editorMode, setEditorMode] = useState<'EDIT_SITE' | 'CREATE_SITE' | null>(null);
  const [currentFileName, setCurrentFileName] = useState('');
  const [editorContent, setEditorContent] = useState('');
  const [isSaving, setIsSaving] = useState(false);

  // Delete State
  const [fileToDelete, setFileToDelete] = useState<string | null>(null);
  const [isDeleting, setIsDeleting] = useState(false);

  // Feedback/Notification State
  const [feedback, setFeedback] = useState<{
      isOpen: boolean;
      type: 'success' | 'error';
      title: string;
      message: string;
  }>({ isOpen: false, type: 'success', title: '', message: '' });

  const closeFeedback = () => setFeedback(prev => ({ ...prev, isOpen: false }));

  useEffect(() => {
    loadData();
  }, [activeTab]);

  const loadData = async () => {
    setLoading(true);
    try {
        if (activeTab === 'SITES') {
            const data = await api.admin.apache.listSites();
            setSites(data);
        } else {
            const data = await api.admin.apache.getHttpd();
            setHttpdContent(data.content);
        }
    } catch (e) {
        console.error("Failed to load apache data", e);
    } finally {
        setLoading(false);
    }
  };

  const handleReloadApache = async () => {
      setReloading(true);
      try {
          await api.admin.apache.reload();
          setFeedback({
              isOpen: true,
              type: 'success',
              title: 'Apache Reloaded',
              message: 'The Apache service has been reloaded successfully.'
          });
      } catch (e: any) {
          setFeedback({
              isOpen: true,
              type: 'error',
              title: 'Reload Failed',
              message: e.message || 'Failed to reload Apache service.'
          });
      } finally {
          setReloading(false);
      }
  };

  const openCreateModal = () => {
      setEditorMode('CREATE_SITE');
      setCurrentFileName('');
      setEditorContent(`<VirtualHost *:9029>
    ServerName hasiluid.kolab.top
    ServerAlias hasiluid.kolab.top
    DocumentRoot "Z:/www/hasiluid"
    <Directory "Z:/www/hasiluid">
        Options FollowSymLinks
        AllowOverride All
        Require all granted
        DirectoryIndex index.php index.html
    </Directory>
</VirtualHost>`);
  };

  const openEditSite = async (filename: string) => {
      try {
          setLoading(true);
          const data = await api.admin.apache.getSite(filename);
          setCurrentFileName(filename);
          setEditorContent(data.content);
          setEditorMode('EDIT_SITE');
      } catch (e) {
          alert("Failed to load site config");
      } finally {
          setLoading(false);
      }
  };

  const handleSaveSite = async () => {
      if (!currentFileName.trim()) return;
      setIsSaving(true);
      try {
          let successMessage = "";
          if (editorMode === 'CREATE_SITE') {
              // Automatically append .test.conf suffix
              const finalName = `${currentFileName.trim()}.test.conf`;
              await api.admin.apache.createSite(finalName, editorContent);
              setSites([...sites, finalName]);
              successMessage = `Virtual Host "${finalName}" created successfully.`;
          } else {
              await api.admin.apache.updateSite(currentFileName, editorContent);
              successMessage = `Configuration for "${currentFileName}" updated successfully.`;
          }
          setEditorMode(null);
          
          // Show Success Popup
          setFeedback({
              isOpen: true,
              type: 'success',
              title: 'Configuration Saved',
              message: successMessage
          });

      } catch (e: any) {
          setFeedback({
              isOpen: true,
              type: 'error',
              title: 'Save Failed',
              message: e.message || "An error occurred while saving the configuration."
          });
      } finally {
          setIsSaving(false);
      }
  };

  const handleAddPort = () => {
      if (!quickPort || isNaN(Number(quickPort))) {
          // Use feedback modal for validation error too
          setFeedback({
              isOpen: true,
              type: 'error',
              title: 'Invalid Port',
              message: 'Please enter a valid numeric port number (e.g. 8080).'
          });
          return;
      }

      const lines = httpdContent.split('\n');
      let insertIndex = -1;

      // Find the last occurrence of a Listen directive
      for (let i = 0; i < lines.length; i++) {
          if (lines[i].trim().startsWith('Listen ')) {
              insertIndex = i;
          }
      }

      const newLine = `Listen ${quickPort}`;

      if (insertIndex !== -1) {
          lines.splice(insertIndex + 1, 0, newLine);
      } else {
          // If no Listen found, append to end
          lines.push(newLine);
      }

      setHttpdContent(lines.join('\n'));
      setQuickPort('');
      
      // Notify user they need to save
      setFeedback({
          isOpen: true,
          type: 'success',
          title: 'Port Added to Editor',
          message: `Port ${quickPort} has been added to the configuration text below. Please click 'Save Configuration' to apply changes.`
      });
  };

  const handleSaveHttpd = async () => {
      // Direct save without confirm dialog (Modal will show result)
      setIsSaving(true);
      try {
          await api.admin.apache.updateHttpd(httpdContent);
          setFeedback({
              isOpen: true,
              type: 'success',
              title: 'Configuration Saved',
              message: 'Global Apache configuration (httpd.conf) has been successfully saved.'
          });
      } catch (e: any) {
          setFeedback({
              isOpen: true,
              type: 'error',
              title: 'Save Failed',
              message: e.message || "Failed to update global configuration."
          });
      } finally {
          setIsSaving(false);
      }
  };

  const confirmDelete = async () => {
      if (!fileToDelete) return;
      setIsDeleting(true);
      try {
          await api.admin.apache.deleteSite(fileToDelete);
          setSites(sites.filter(s => s !== fileToDelete));
          setFileToDelete(null);
          setFeedback({
              isOpen: true,
              type: 'success',
              title: 'Config Deleted',
              message: `The configuration file "${fileToDelete}" has been removed.`
          });
      } catch (e: any) {
          setFeedback({
              isOpen: true,
              type: 'error',
              title: 'Delete Failed',
              message: e.message
          });
      } finally {
          setIsDeleting(false);
      }
  };

  return (
    <div className="space-y-6 animate-in fade-in duration-300 relative">
      
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
                            {feedback.type === 'success' ? 'OK, Great!' : 'Close'}
                        </button>
                    </div>
                </div>
            </div>
        </div>
      )}

      <div className="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
           <h2 className="text-xl font-bold text-slate-800 flex items-center gap-2">
             <Settings className="w-6 h-6 text-indigo-600" /> Apache Configuration
           </h2>
           <p className="text-sm text-slate-500 mt-1">Manage Virtual Hosts and global server settings.</p>
        </div>
        
        <div className="flex items-center gap-3">
            <button 
                onClick={handleReloadApache} 
                disabled={reloading}
                className="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg font-medium hover:bg-slate-50 hover:text-indigo-600 transition-colors shadow-sm flex items-center gap-2"
            >
                <RefreshCw className={`w-4 h-4 ${reloading ? 'animate-spin' : ''}`} />
                {reloading ? 'Reloading...' : 'Reload Apache'}
            </button>
            {activeTab === 'SITES' && (
                <button 
                    onClick={openCreateModal}
                    className="px-4 py-2 bg-indigo-600 text-white rounded-lg font-medium shadow-sm hover:bg-indigo-700 flex items-center gap-2"
                >
                    <Plus className="w-4 h-4" /> New Site
                </button>
            )}
        </div>
      </div>

      <div className="flex space-x-1 border-b border-slate-200">
        <button
          onClick={() => setActiveTab('SITES')}
          className={`px-4 py-2 text-sm font-medium border-b-2 transition-colors flex items-center gap-2 ${activeTab === 'SITES' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700'}`}
        >
          <FileCode className="w-4 h-4" /> Virtual Hosts
        </button>
        <button
          onClick={() => setActiveTab('HTTPD')}
          className={`px-4 py-2 text-sm font-medium border-b-2 transition-colors flex items-center gap-2 ${activeTab === 'HTTPD' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700'}`}
        >
          <Settings className="w-4 h-4" /> httpd.conf
        </button>
      </div>

      {loading && !reloading ? (
           <div className="py-12 flex justify-center"><Loader2 className="w-8 h-8 text-indigo-600 animate-spin" /></div>
      ) : activeTab === 'SITES' ? (
          <Card>
            <div className="overflow-x-auto">
                <table className="min-w-full text-left text-sm">
                    <thead className="bg-slate-50 text-slate-500 border-b border-slate-200">
                        <tr>
                            <th className="px-6 py-3 font-medium">Config Filename</th>
                            <th className="px-6 py-3 font-medium text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-slate-100">
                        {sites.map((site, idx) => (
                            <tr key={idx} className="hover:bg-slate-50/50">
                                <td className="px-6 py-4 font-mono text-slate-700 font-medium">{site}</td>
                                <td className="px-6 py-4 text-right">
                                    <div className="flex justify-end gap-2">
                                        <button onClick={() => openEditSite(site)} className="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Edit Config">
                                            <Edit2 className="w-4 h-4" />
                                        </button>
                                        <button onClick={() => setFileToDelete(site)} className="p-2 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Delete Config">
                                            <Trash2 className="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        ))}
                        {sites.length === 0 && (
                            <tr><td colSpan={2} className="px-6 py-12 text-center text-slate-500 italic">No virtual hosts found in sites-enabled.</td></tr>
                        )}
                    </tbody>
                </table>
            </div>
          </Card>
      ) : (
          <Card title="Global Configuration (httpd.conf)">
              {/* Quick Action Bar */}
              <div className="mb-4 p-4 bg-slate-50 border border-slate-200 rounded-lg flex flex-col sm:flex-row items-start sm:items-center gap-4">
                  <div className="flex-1">
                      <h4 className="text-sm font-bold text-slate-700 flex items-center gap-2">
                          <Network className="w-4 h-4 text-indigo-600"/> Quick Add Port
                      </h4>
                      <p className="text-xs text-slate-500 mt-1">Appends a new <code className="bg-slate-200 px-1 rounded">Listen [port]</code> directive below existing listeners.</p>
                  </div>
                  <div className="flex items-center gap-2 w-full sm:w-auto">
                      <input
                          type="number"
                          value={quickPort}
                          onChange={(e) => setQuickPort(e.target.value)}
                          placeholder="e.g. 9062"
                          className="px-3 py-2 border border-slate-300 rounded-lg text-sm w-full sm:w-32 focus:ring-2 focus:ring-indigo-500 outline-none"
                      />
                      <button
                          onClick={handleAddPort}
                          className="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 whitespace-nowrap"
                      >
                          Add Port
                      </button>
                  </div>
              </div>

              <div className="bg-slate-900 rounded-lg p-1 border border-slate-700">
                  <textarea 
                      value={httpdContent}
                      onChange={(e) => setHttpdContent(e.target.value)}
                      className="w-full h-[500px] bg-slate-900 text-slate-300 font-mono text-xs p-4 focus:outline-none resize-y"
                      spellCheck={false}
                  />
              </div>
              <div className="mt-4 flex justify-end">
                  <button 
                      onClick={handleSaveHttpd} 
                      disabled={isSaving}
                      className="px-6 py-2 bg-indigo-600 text-white rounded-lg font-medium shadow-sm hover:bg-indigo-700 flex items-center gap-2"
                  >
                      {isSaving && <Loader2 className="w-4 h-4 animate-spin" />}
                      Save Configuration
                  </button>
              </div>
          </Card>
      )}

      {/* Editor Modal */}
      {editorMode && (
          <div className="fixed inset-0 z-[70] flex items-center justify-center p-4">
              <div className="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onClick={() => setEditorMode(null)} />
              <div className="relative w-full max-w-6xl bg-white rounded-xl shadow-2xl p-6 animate-in fade-in zoom-in-95 duration-200 flex flex-col h-[90vh]">
                  <div className="flex justify-between items-center mb-6 shrink-0">
                      <h3 className="text-lg font-bold text-slate-900">{editorMode === 'CREATE_SITE' ? 'Create Virtual Host' : `Edit ${currentFileName}`}</h3>
                      <button onClick={() => setEditorMode(null)} className="text-slate-400 hover:text-slate-600"><X className="w-5 h-5" /></button>
                  </div>
                  
                  {editorMode === 'CREATE_SITE' && (
                      <div className="mb-4 shrink-0">
                          <label className="text-sm font-medium text-slate-700 mb-1 block">Filename</label>
                          <div className="flex items-center">
                              <input 
                                  type="text" 
                                  value={currentFileName} 
                                  onChange={(e) => setCurrentFileName(e.target.value)}
                                  placeholder="mysite" 
                                  className="flex-1 px-3 py-2 border border-slate-300 border-r-0 rounded-l-lg focus:ring-2 focus:ring-indigo-500 outline-none"
                              />
                              <span className="px-3 py-2 bg-slate-100 border border-slate-300 border-l-0 rounded-r-lg text-slate-500 font-mono text-sm">
                                  .test.conf
                              </span>
                          </div>
                      </div>
                  )}

                  <div className="flex-1 min-h-0 bg-slate-900 rounded-lg border border-slate-700 overflow-hidden">
                      <textarea 
                          value={editorContent}
                          onChange={(e) => setEditorContent(e.target.value)}
                          className="w-full h-full bg-slate-900 text-emerald-400 font-mono text-sm p-4 focus:outline-none resize-none"
                          spellCheck={false}
                      />
                  </div>

                  <div className="pt-4 flex justify-end gap-3 shrink-0">
                      <button onClick={() => setEditorMode(null)} className="px-4 py-2 text-slate-600 hover:bg-slate-50 rounded-lg font-medium text-sm transition-colors">Cancel</button>
                      <button 
                          onClick={handleSaveSite} 
                          disabled={isSaving || !currentFileName}
                          className="px-4 py-2 bg-indigo-600 text-white rounded-lg font-medium text-sm shadow-sm hover:bg-indigo-700 transition-colors flex items-center gap-2 disabled:opacity-50"
                      >
                          {isSaving && <Loader2 className="w-4 h-4 animate-spin" />}
                          Save Config
                      </button>
                  </div>
              </div>
          </div>
      )}

      {/* Delete Confirmation */}
      {fileToDelete && (
          <div className="fixed inset-0 z-[80] flex items-center justify-center p-4">
              <div className="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onClick={() => !isDeleting && setFileToDelete(null)} />
              <div className="relative w-full max-w-sm bg-white rounded-xl shadow-2xl p-6 animate-in fade-in zoom-in-95 duration-200">
                  <div className="flex items-start gap-4">
                      <div className="p-3 bg-red-100 rounded-full shrink-0"><AlertTriangle className="w-6 h-6 text-red-600" /></div>
                      <div>
                          <h3 className="text-lg font-bold text-slate-900">Delete Config?</h3>
                          <p className="text-sm text-slate-500 mt-1">Are you sure you want to delete <span className="font-mono font-bold text-slate-800">{fileToDelete}</span>?</p>
                          <p className="text-xs text-red-600 mt-2 bg-red-50 p-2 rounded border border-red-100">Apache will be automatically validated.</p>
                      </div>
                  </div>
                  <div className="mt-6 flex justify-end gap-3">
                      <button onClick={() => setFileToDelete(null)} disabled={isDeleting} className="px-4 py-2 text-slate-600 hover:bg-slate-50 rounded-lg font-medium text-sm transition-colors">Cancel</button>
                      <button onClick={confirmDelete} disabled={isDeleting} className="px-4 py-2 bg-red-600 text-white rounded-lg font-medium text-sm hover:bg-red-700 shadow-sm transition-colors flex items-center gap-2">
                          {isDeleting && <Loader2 className="w-4 h-4 animate-spin" />} Delete
                      </button>
                  </div>
              </div>
          </div>
      )}
    </div>
  );
};