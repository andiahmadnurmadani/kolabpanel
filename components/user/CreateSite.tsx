import React, { useState, useRef } from 'react';
import { Card } from '../Shared';
import { Framework, Domain } from '../../types';
import { Upload, FileArchive, Check, Loader2, Database } from 'lucide-react';
import { api } from '../../services/api';

interface CreateSiteProps {
  domains: Domain[];
  onDeploy: () => void; // Changed signature
}

export const CreateSite: React.FC<CreateSiteProps> = ({ domains, onDeploy }) => {
  const [deployStage, setDeployStage] = useState<'IDLE' | 'UPLOADING' | 'EXTRACTING' | 'FINALIZING'>('IDLE');
  const [name, setName] = useState('');
  const [framework, setFramework] = useState<Framework>(Framework.REACT);
  const [subdomain, setSubdomain] = useState('');
  const [selectedDomain, setSelectedDomain] = useState(domains[0]?.name || 'kolabpanel.com');
  const [needsDatabase, setNeedsDatabase] = useState(false);
  const [file, setFile] = useState<File | null>(null);
  const [error, setError] = useState('');
  const fileInputRef = useRef<HTMLInputElement>(null);

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

    const userData = await api.auth.me();

    // Prepare FormData
    const formData = new FormData();
    formData.append('userId', userData.id);
    formData.append('name', name);
    formData.append('framework', framework);
    formData.append('subdomain', `${subdomain}.${selectedDomain}`);
    formData.append('needsDatabase', String(needsDatabase));
    formData.append('file', file);

    try {
        setDeployStage('UPLOADING');
        await api.sites.deploy(formData);
        
        setDeployStage('EXTRACTING');
        // Add artificial small delay to show steps clearly, or remove if not needed
        await new Promise(r => setTimeout(r, 500));
        
        setDeployStage('FINALIZING');
        await new Promise(r => setTimeout(r, 500));

        alert(`Site "${name}" successfully deployed to D:/KolabPanel!`);
        onDeploy(); // Refresh site list in App
        setDeployStage('IDLE');
        
        // Reset form
        setName('');
        setSubdomain('');
        setNeedsDatabase(false);
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
            <p className="text-xs text-slate-500">Choose from available base domains provided by admin.</p>
          </div>

          {/* Database Option */}
          <div className="p-4 border border-slate-200 rounded-lg bg-slate-50 flex items-start gap-3">
             <div className="flex items-center h-5">
               <input 
                  id="needsDatabase"
                  type="checkbox"
                  checked={needsDatabase}
                  onChange={(e) => setNeedsDatabase(e.target.checked)}
                  disabled={isProcessing}
                  className="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500"
               />
             </div>
             <label htmlFor="needsDatabase" className="cursor-pointer">
                <div className="flex items-center gap-2 text-sm font-bold text-slate-800">
                    <Database className="w-4 h-4 text-indigo-600" />
                    Create MySQL Database
                </div>
                <p className="text-xs text-slate-500 mt-1">
                    If checked, a new MySQL database and user will be automatically created for this project.
                </p>
             </label>
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
