import React, { useState, useRef, useEffect } from 'react';
import { Card } from '../Shared';
import { Framework, Domain, Site, SiteStatus } from '../../types';
import { Upload, FileArchive, Check, Loader2, Database, Link, Plus, Unlink } from 'lucide-react';
import { api } from '../../services/api';

interface CreateSiteProps {
  domains: Domain[];
  onDeploy: () => void;
}

type DbMode = 'NONE' | 'NEW' | 'ATTACH';

export const CreateSite: React.FC<CreateSiteProps> = ({ domains, onDeploy }) => {
  const [deployStage, setDeployStage] = useState<'IDLE' | 'UPLOADING' | 'EXTRACTING' | 'FINALIZING'>('IDLE');
  
  // Form State
  const [name, setName] = useState('');
  const [framework, setFramework] = useState<Framework>(Framework.REACT);
  const [subdomain, setSubdomain] = useState('');
  const [selectedDomain, setSelectedDomain] = useState(domains[0]?.name || 'kolabpanel.com');
  const [file, setFile] = useState<File | null>(null);
  const [error, setError] = useState('');
  
  // DB State
  const [dbMode, setDbMode] = useState<DbMode>('NONE');
  const [orphanedDbs, setOrphanedDbs] = useState<Site[]>([]);
  const [selectedOrphanId, setSelectedOrphanId] = useState('');

  const fileInputRef = useRef<HTMLInputElement>(null);

  // Fetch Orphaned Databases on mount
  useEffect(() => {
    const fetchOrphans = async () => {
        try {
            const user = await api.auth.me();
            const sites = await api.sites.list(user.id);
            const orphans = sites.filter(s => s.status === SiteStatus.DB_ONLY);
            setOrphanedDbs(orphans);
        } catch (e) {
            console.error("Failed to fetch sites", e);
        }
    };
    fetchOrphans();
  }, [deployStage]); // Refetch after deployment

  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setError('');
    const selectedFile = e.target.files?.[0];
    
    if (!selectedFile) return;

    // Validation: Check extension and MIME type
    const isZip = selectedFile.name.toLowerCase().endsWith('.zip') || 
                  selectedFile.type === 'application/zip' || 
                  selectedFile.type === 'application/x-zip-compressed';

    if (!isZip) {
      setError('Invalid file format. Only .zip files are allowed for auto-extraction.');
      setFile(null);
      if (fileInputRef.current) fileInputRef.current.value = '';
      return;
    }

    setFile(selectedFile);
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError('');

    if (!file) {
      setError('Please upload a source code ZIP file.');
      return;
    }

    if (!name || !subdomain) {
      setError('Please fill in all fields.');
      return;
    }

    if (dbMode === 'ATTACH' && !selectedOrphanId) {
        setError('Please select a detached database to connect.');
        return;
    }

    const userData = await api.auth.me();

    // Prepare FormData
    const formData = new FormData();
    formData.append('userId', userData.id);
    formData.append('name', name);
    formData.append('framework', framework);
    formData.append('subdomain', `${subdomain}.${selectedDomain}`);
    formData.append('needsDatabase', String(dbMode === 'NEW'));
    
    // If attaching, send the ID of the old site/db
    if (dbMode === 'ATTACH') {
        formData.append('attachedDatabaseId', selectedOrphanId);
    }

    formData.append('file', file);

    try {
        setDeployStage('UPLOADING');
        await api.sites.deploy(formData);
        
        setDeployStage('EXTRACTING');
        // Add artificial small delay to show steps clearly
        await new Promise(r => setTimeout(r, 500));
        
        setDeployStage('FINALIZING');
        await new Promise(r => setTimeout(r, 500));

        alert(`Site "${name}" successfully deployed!`);
        onDeploy(); // Refresh site list in App
        setDeployStage('IDLE');
        
        // Reset form
        setName('');
        setSubdomain('');
        setDbMode('NONE');
        setSelectedOrphanId('');
        setFile(null);
    } catch (e: any) {
        setError(e.message || 'Deployment failed');
        setDeployStage('IDLE');
    }
  };

  const isProcessing = deployStage !== 'IDLE';

  return (
    <div className="max-w-3xl mx-auto animate-in fade-in slide-in-from-bottom-4 duration-500">
      <Card title="Deploy New Site">
        <form onSubmit={handleSubmit} className="space-y-6">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div className="space-y-2">
              <label className="text-sm font-medium text-slate-700">Project Name</label>
              <input 
                type="text" 
                value={name}
                onChange={(e) => setName(e.target.value)}
                disabled={isProcessing}
                className="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all disabled:bg-slate-100" 
                placeholder="My Awesome Project" 
                required 
              />
            </div>
            <div className="space-y-2">
              <label className="text-sm font-medium text-slate-700">Framework</label>
              <select 
                value={framework}
                onChange={(e) => setFramework(e.target.value as Framework)}
                disabled={isProcessing}
                className="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none bg-white disabled:bg-slate-100"
              >
                {Object.values(Framework).map(f => <option key={f} value={f}>{f}</option>)}
              </select>
            </div>
          </div>

          <div className="space-y-2">
            <label className="text-sm font-medium text-slate-700">Subdomain & Domain</label>
            <div className="flex">
              <input 
                type="text" 
                value={subdomain}
                onChange={(e) => setSubdomain(e.target.value)}
                disabled={isProcessing}
                className="flex-1 px-3 py-2 border border-r-0 border-slate-300 rounded-l-lg focus:ring-2 focus:ring-indigo-500 outline-none disabled:bg-slate-100" 
                placeholder="project-name" 
                required 
              />
              <div className="bg-slate-100 border border-slate-300 rounded-r-lg flex items-center px-2">
                <span className="text-slate-500 text-sm font-medium mr-1">.</span>
                <select 
                  value={selectedDomain}
                  onChange={(e) => setSelectedDomain(e.target.value)}
                  disabled={isProcessing}
                  className="bg-transparent border-none text-sm text-slate-700 font-medium outline-none focus:ring-0 cursor-pointer py-1 disabled:text-slate-400"
                >
                  {domains.map(d => (
                    <option key={d.id} value={d.name}>{d.name}</option>
                  ))}
                </select>
              </div>
            </div>
          </div>

          {/* Database Configuration Section */}
          <div className="space-y-3">
             <label className="text-sm font-medium text-slate-700">Database Configuration</label>
             
             <div className="grid grid-cols-1 md:grid-cols-3 gap-3">
                {/* Option 1: None */}
                <div 
                    onClick={() => !isProcessing && setDbMode('NONE')}
                    className={`cursor-pointer p-3 rounded-lg border-2 transition-all flex flex-col items-center text-center gap-2 ${dbMode === 'NONE' ? 'border-indigo-600 bg-indigo-50' : 'border-slate-200 hover:border-slate-300 bg-white'}`}
                >
                    <div className={`p-2 rounded-full ${dbMode === 'NONE' ? 'bg-indigo-200' : 'bg-slate-100'}`}>
                        <Database className={`w-4 h-4 ${dbMode === 'NONE' ? 'text-indigo-700' : 'text-slate-400'}`} />
                    </div>
                    <span className={`text-xs font-bold ${dbMode === 'NONE' ? 'text-indigo-700' : 'text-slate-600'}`}>No Database</span>
                </div>

                {/* Option 2: New */}
                <div 
                    onClick={() => !isProcessing && setDbMode('NEW')}
                    className={`cursor-pointer p-3 rounded-lg border-2 transition-all flex flex-col items-center text-center gap-2 ${dbMode === 'NEW' ? 'border-indigo-600 bg-indigo-50' : 'border-slate-200 hover:border-slate-300 bg-white'}`}
                >
                    <div className={`p-2 rounded-full ${dbMode === 'NEW' ? 'bg-indigo-200' : 'bg-slate-100'}`}>
                        <Plus className={`w-4 h-4 ${dbMode === 'NEW' ? 'text-indigo-700' : 'text-slate-400'}`} />
                    </div>
                    <span className={`text-xs font-bold ${dbMode === 'NEW' ? 'text-indigo-700' : 'text-slate-600'}`}>Create New</span>
                </div>

                {/* Option 3: Attach (Conditional) */}
                {orphanedDbs.length > 0 ? (
                     <div 
                        onClick={() => !isProcessing && setDbMode('ATTACH')}
                        className={`cursor-pointer p-3 rounded-lg border-2 transition-all flex flex-col items-center text-center gap-2 ${dbMode === 'ATTACH' ? 'border-indigo-600 bg-indigo-50' : 'border-slate-200 hover:border-slate-300 bg-white'}`}
                    >
                        <div className={`p-2 rounded-full ${dbMode === 'ATTACH' ? 'bg-indigo-200' : 'bg-slate-100'}`}>
                            <Link className={`w-4 h-4 ${dbMode === 'ATTACH' ? 'text-indigo-700' : 'text-slate-400'}`} />
                        </div>
                        <div className="flex flex-col">
                             <span className={`text-xs font-bold ${dbMode === 'ATTACH' ? 'text-indigo-700' : 'text-slate-600'}`}>Connect Existing</span>
                             <span className="text-[10px] text-emerald-600 bg-emerald-50 px-1 rounded border border-emerald-100">{orphanedDbs.length} Available</span>
                        </div>
                    </div>
                ) : (
                    <div className="opacity-50 cursor-not-allowed p-3 rounded-lg border border-slate-200 flex flex-col items-center text-center gap-2 bg-slate-50">
                         <div className="p-2 rounded-full bg-slate-100">
                             <Unlink className="w-4 h-4 text-slate-300" />
                         </div>
                         <span className="text-xs font-bold text-slate-400">No Detached DBs</span>
                    </div>
                )}
             </div>

             {/* Dynamic Selection Input for Attach */}
             {dbMode === 'ATTACH' && (
                 <div className="mt-3 animate-in fade-in slide-in-from-top-2">
                     <label className="text-xs font-semibold text-indigo-600 mb-1 block">Select Detached Database:</label>
                     <select 
                        value={selectedOrphanId} 
                        onChange={(e) => setSelectedOrphanId(e.target.value)}
                        className="w-full px-3 py-2 bg-indigo-50 border border-indigo-200 rounded-lg text-sm text-indigo-900 focus:ring-2 focus:ring-indigo-500 outline-none"
                     >
                         <option value="" disabled>-- Choose a database --</option>
                         {orphanedDbs.map(db => (
                             <option key={db.id} value={db.id}>
                                 db_{db.subdomain.replace(/[^a-z0-9]/g, '')} (from deleted: {db.name})
                             </option>
                         ))}
                     </select>
                     <p className="text-[10px] text-slate-500 mt-1">This will attach the existing database data to your new project.</p>
                 </div>
             )}
          </div>

          <div className="space-y-2">
            <label className="text-sm font-medium text-slate-700">Source Code (ZIP)</label>
            <div 
              className={`border-2 border-dashed rounded-lg p-8 flex flex-col items-center justify-center transition-colors cursor-pointer group ${error ? 'border-red-300 bg-red-50' : isProcessing ? 'border-slate-200 bg-slate-50 cursor-not-allowed' : 'border-slate-300 hover:bg-slate-50'}`}
              onClick={() => !isProcessing && fileInputRef.current?.click()}
            >
              {file ? (
                 <div className="flex flex-col items-center text-indigo-600">
                    <FileArchive className="w-10 h-10 mb-2" />
                    <span className="font-medium text-sm">{file.name}</span>
                    <span className="text-xs text-indigo-400">{(file.size / 1024).toFixed(2)} KB - Ready to extract</span>
                    <p className="text-xs text-emerald-600 mt-2 font-medium flex items-center gap-1"><Check className="w-3 h-3" /> Valid ZIP Archive</p>
                 </div>
              ) : (
                <>
                  <Upload className="w-8 h-8 text-slate-400 group-hover:text-indigo-500 mb-2 transition-colors" />
                  <p className="text-sm text-slate-600">Drag & drop or <span className="text-indigo-600 font-medium">browse</span></p>
                  <p className="text-xs text-slate-400 mt-1">Only .zip files allowed (Max 100MB)</p>
                </>
              )}
              <input 
                type="file" 
                ref={fileInputRef} 
                className="hidden" 
                accept=".zip,application/zip,application/x-zip-compressed"
                onChange={handleFileChange}
                disabled={isProcessing}
              />
            </div>
            {error && <p className="text-sm text-red-600 font-medium mt-1">{error}</p>}
          </div>

          <div className="pt-4 flex justify-end">
            <button 
              type="submit" 
              disabled={isProcessing}
              className={`px-6 py-2 bg-indigo-600 text-white rounded-lg font-medium shadow-sm hover:bg-indigo-700 focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all flex items-center gap-2 ${isProcessing ? 'opacity-80 cursor-wait' : ''}`}
            >
              {isProcessing && <Loader2 className="w-4 h-4 animate-spin" />}
              {deployStage === 'IDLE' && 'Deploy Site'}
              {deployStage === 'UPLOADING' && 'Uploading Archive...'}
              {deployStage === 'EXTRACTING' && 'Unzipping to D:/KolabPanel...'}
              {deployStage === 'FINALIZING' && 'Finalizing...'}
            </button>
          </div>
        </form>
      </Card>
    </div>
  );
};